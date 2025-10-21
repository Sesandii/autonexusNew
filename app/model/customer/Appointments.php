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

    
}
