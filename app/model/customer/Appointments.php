<?php
// app/model/customer/Appointments.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;
use PDOException;

class Appointments
{
    private PDO $pdo;
    public function __construct() { $this->pdo = db(); }

    /** Map user -> customer_id */
    private function customerIdByUserId(int $userId): ?int
    {
        $sql = "SELECT customer_id FROM customers WHERE user_id = :uid LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['uid' => $userId]);
        $cid = $st->fetchColumn();
        return $cid !== false ? (int)$cid : null;
    }

    /** Map branch_code -> branch_id */
    private function branchIdByCode(string $code): ?int
    {
        $sql = "SELECT branch_id FROM branches WHERE branch_code = :c LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['c' => $code]);
        $bid = $st->fetchColumn();
        return $bid !== false ? (int)$bid : null;
    }

    /** Verify vehicle belongs to customer */
    private function ownsVehicle(int $customerId, int $vehicleId): bool
    {
        $sql = "SELECT 1 FROM vehicles WHERE vehicle_id = :v AND customer_id = :c LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['v' => $vehicleId, 'c' => $customerId]);
        return (bool)$st->fetchColumn();
    }

    /** How many bookings exist for a branch at date+time */
    private function countAtSlot(int $branchId, string $dateYmd, string $time): int
    {
        $sql = "SELECT COUNT(*) 
                  FROM appointments 
                 WHERE branch_id = :b 
                   AND appointment_date = :d 
                   AND appointment_time = :t
                   AND status IN ('requested','confirmed','ongoing','in_service')";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            'b' => $branchId,
            'd' => $dateYmd,
            't' => $time
        ]);
        return (int)$st->fetchColumn();
    }

    /** Get slot availability for all time slots for a branch/date */
    public function getSlotAvailability(string $branchCode, string $dateYmd): array
    {
        $branchId = $this->branchIdByCode($branchCode);
        if (!$branchId) return [];

        $timeSlots = ['09:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00'];
        $cap = 3;
        $result = [];

        foreach ($timeSlots as $time) {
            $count = $this->countAtSlot($branchId, $dateYmd, $time);
            $shortTime = substr($time, 0, 5); // "09:00"
            $result[$shortTime] = [
                'booked'    => $count,
                'available' => max(0, $cap - $count),
                'full'      => $count >= $cap,
            ];
        }

        return $result;
    }

    /** Create a booking (returns [ok, message]) */
    public function createBooking(
        int $userId, string $branchCode, int $vehicleId, int $serviceId, string $dateYmd, string $time
    ): array {
        try {
            $customerId = $this->customerIdByUserId($userId);
            if (!$customerId) return [false, 'Customer profile not found.'];

            $branchId = $this->branchIdByCode($branchCode);
            if (!$branchId) return [false, 'Invalid branch.'];

            if (!$this->ownsVehicle($customerId, $vehicleId)) {
                return [false, 'Selected vehicle does not belong to your account.'];
            }

           // normalize time to HH:MM:SS
if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
    if (strlen($time) === 5) {
        $time .= ':00';
    }
} else {
    $time = '00:00:00';
}

            // simple slot cap (max 3 per slot, as discussed earlier)
            $cap   = 3;
            $count = $this->countAtSlot($branchId, $dateYmd, $time);
            if ($count >= $cap) {
                return [false, 'Selected time slot is full. Please choose another time.'];
            }

            $sql = "INSERT INTO appointments
                        (customer_id, branch_id, vehicle_id, service_id, appointment_date, appointment_time, status, created_at)
                    VALUES
                        (:cid, :bid, :vid, :sid, :d, :t, 'requested', NOW())";
            $st = $this->pdo->prepare($sql);
            $st->execute([
                'cid' => $customerId,
                'bid' => $branchId,
                'vid' => $vehicleId,
                'sid' => $serviceId,
                'd'   => $dateYmd,
                't'   => $time,
            ]);

            return [true, 'Booking created successfully.'];
        } catch (PDOException $e) {
            // optional: log $e->getMessage()
            return [false, 'Failed to create booking.'];
        }
    }

    /** Cancel appointment if it belongs to the current customer */
    public function cancelIfCustomerOwns(int $userId, int $appointmentId): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid || $appointmentId <= 0) return false;

        // Only allow cancel of own appointment, and only if pending/requested/confirmed (not in_progress/completed)
        $sql = "UPDATE appointments
                   SET status = 'cancelled'
                 WHERE appointment_id = :id
                   AND customer_id = :cid
                   AND status IN ('requested','confirmed','pending')";
        $st = $this->pdo->prepare($sql);
        return $st->execute(['id' => $appointmentId, 'cid' => $cid]);
    }

    /** Reader used by your appointments page */
    public function getByCustomer(int $userId): array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [];

        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
                       b.name AS branch_name,
                       s.name AS service_name,
                       v.license_plate, v.make, v.model
                  FROM appointments a
             LEFT JOIN branches b ON b.branch_id = a.branch_id
             LEFT JOIN services s ON s.service_id = a.service_id
             LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
                 WHERE a.customer_id = :cid
              ORDER BY CASE a.status
                         WHEN 'in_service' THEN 1
                         WHEN 'requested' THEN 2
                         WHEN 'confirmed' THEN 3
                         ELSE 4
                       END,
                       a.appointment_date DESC, a.appointment_time DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Completed appointments for a given user */
public function completedByUser(int $userId): array
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return [];

    $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                   wo.status, b.name AS branch_name, s.name AS service_name,
                   v.license_plate, v.make, v.model
              FROM appointments a
              JOIN work_orders wo ON wo.appointment_id = a.appointment_id AND wo.status = 'completed'
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
             WHERE a.customer_id = :cid
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $st = $this->pdo->prepare($sql);
    $st->execute(['cid' => $cid]);
    return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
}

/** Completed appointments for user that DON'T have feedback yet */
public function completedWithoutFeedbackByUser(int $userId): array
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return [];

    $sql = "SELECT a.appointment_id, 
                   a.appointment_date AS service_date, 
                   a.appointment_time AS service_time,
                   b.name AS branch_name, 
                   s.name AS service_name,
                   v.license_plate AS vehicle_license_plate,
                   v.make AS vehicle_make,
                   v.model AS vehicle_model,
                   wo.work_order_id,
                   wo.service_summary
              FROM appointments a
              JOIN work_orders wo ON wo.appointment_id = a.appointment_id AND wo.status = 'completed'
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
         LEFT JOIN feedback f ON f.appointment_id = a.appointment_id
             WHERE a.customer_id = :cid
               AND f.appointment_id IS NULL
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $st = $this->pdo->prepare($sql);
    $st->execute(['cid' => $cid]);
    return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
}

    /** Quick ownership check used before accepting feedback */
public function appointmentBelongsToUserAndCompleted(int $userId, int $appointmentId): bool
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return false;

    $sql = "SELECT 1 FROM appointments a
              JOIN work_orders wo ON wo.appointment_id = a.appointment_id AND wo.status = 'completed'
             WHERE a.appointment_id = :aid
               AND a.customer_id = :cid
             LIMIT 1";
    $st = $this->pdo->prepare($sql);
    $st->execute(['aid' => $appointmentId, 'cid' => $cid]);
    return (bool)$st->fetchColumn();
}

/** Get a single appointment by ID */
public function getById(int $appointmentId): ?array
{
    $sql = "SELECT a.*, 
                   b.name AS branch_name,
                   s.name AS service_name,
                   v.license_plate, v.make, v.model,
                   c.customer_id
              FROM appointments a
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
         LEFT JOIN customers c ON c.customer_id = a.customer_id
             WHERE a.appointment_id = :aid
             LIMIT 1";
    $st = $this->pdo->prepare($sql);
    $st->execute(['aid' => $appointmentId]);
    $result = $st->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

/** Get a single appointment by ID for a specific user (ownership check) */
public function getAppointmentById(int $userId, int $appointmentId): ?array
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return null;

    $sql = "SELECT a.*, 
                   b.name AS branch_name, b.city AS branch_city,
                   s.name AS service_name, s.description AS service_description,
                   v.license_plate, v.make, v.model, v.year AS vehicle_year,
                   wo.status AS work_order_status, wo.work_order_id,
                   wo.started_at AS service_start, wo.completed_at AS service_end
              FROM appointments a
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
         LEFT JOIN work_orders wo ON wo.appointment_id = a.appointment_id
             WHERE a.appointment_id = :aid
               AND a.customer_id = :cid
             LIMIT 1";
    $st = $this->pdo->prepare($sql);
    $st->execute(['aid' => $appointmentId, 'cid' => $cid]);
    $result = $st->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

}
