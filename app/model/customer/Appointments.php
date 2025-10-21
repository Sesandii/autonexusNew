<?php
// app/model/customer/Appointments.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

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

    /** Public reader used by your appointments page */
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

    /** Count active bookings in the same slot to enforce capacity (max 3) */
    private function countInSlot(int $branchId, string $dateYmd, string $timeHms): int
    {
        // consider requested/confirmed/ongoing as occupying a slot
        $sql = "SELECT COUNT(*) FROM appointments
                 WHERE branch_id = :bid
                   AND appointment_date = :d
                   AND appointment_time = :t
                   AND status IN ('requested','confirmed','ongoing')";
        $st = $this->pdo->prepare($sql);
        $st->execute(['bid' => $branchId, 'd' => $dateYmd, 't' => $timeHms]);
        return (int)$st->fetchColumn();
    }

    /**
     * Create booking (returns [ok,bool, msg,string]).
     * $time should be 'HH:MM' or 'HH:MM:SS'
     */
    public function createBooking(
        int $userId,
        string $branchCode,
        int $vehicleId,
        int $serviceId,
        string $dateYmd,
        string $time
    ): array {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [false, 'Customer not found'];

        $bid = $this->branchIdByCode($branchCode);
        if (!$bid) return [false, 'Invalid branch'];

        // normalise time to HH:MM:SS
        $timeHms = preg_match('/^\d{2}:\d{2}:\d{2}$/', $time) ? $time : ($time . ':00');

        // 1) date must be today or future
        if ($dateYmd < date('Y-m-d')) return [false, 'Date must be today or later'];

        // 2) capacity check (max 3 per slot)
        if ($this->countInSlot($bid, $dateYmd, $timeHms) >= 3) {
            return [false, 'That time slot is full'];
        }

        // Insert
        $sql = "INSERT INTO appointments
                   (customer_id, branch_id, vehicle_id, service_id, appointment_date, appointment_time, status)
                VALUES
                   (:cid, :bid, :vid, :sid, :d, :t, 'requested')";
        $st = $this->pdo->prepare($sql);
        $ok = $st->execute([
            'cid' => $cid,
            'bid' => $bid,
            'vid' => $vehicleId,
            'sid' => $serviceId,
            'd'   => $dateYmd,
            't'   => $timeHms,
        ]);

        return $ok ? [true, 'Booking created'] : [false, 'Could not create booking'];
    }

    /** Optional: cancel if the logged-in customer owns it */
    public function cancelIfCustomerOwns(int $userId, int $appointmentId): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return false;

        $sql = "UPDATE appointments SET status='cancelled'
                 WHERE appointment_id = :id AND customer_id = :cid";
        $st = $this->pdo->prepare($sql);
        return $st->execute(['id' => $appointmentId, 'cid' => $cid]);
    }

    
}
