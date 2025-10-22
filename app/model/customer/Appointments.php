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
                   AND status IN ('requested','confirmed','ongoing')";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            'b' => $branchId,
            'd' => $dateYmd,
            't' => $time
        ]);
        return (int)$st->fetchColumn();
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

        // Only allow cancel of own appointment, and only if not completed/cancelled
        $sql = "UPDATE appointments
                   SET status = 'cancelled'
                 WHERE appointment_id = :id
                   AND customer_id = :cid
                   AND status IN ('requested','confirmed')";
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
              ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
