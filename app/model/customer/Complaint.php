<?php
// app/model/customer/Complaint.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class Complaint
{
    private PDO $pdo;
    public function __construct() { $this->pdo = db(); }

    private function customerIdByUserId(int $userId): ?int
    {
        $sql = "SELECT customer_id FROM customers WHERE user_id = :uid LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['uid' => $userId]);
        $cid = $st->fetchColumn();
        return $cid !== false ? (int)$cid : null;
    }

    /** All complaints filed by this customer */
    public function getByUser(int $userId): array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [];

        $sql = "SELECT c.complaint_id, c.complaint_date, c.description,
                       c.priority, c.status,
                       v.make, v.model, v.license_plate
                  FROM complaints c
             LEFT JOIN vehicles v ON v.vehicle_id = c.vehicle_id
                 WHERE c.customer_id = :cid
              ORDER BY c.complaint_date DESC, c.complaint_id DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Vehicles owned by this customer (for the form dropdown) */
    public function vehiclesByUser(int $userId): array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [];

        $sql = "SELECT vehicle_id, make, model, license_plate
                  FROM vehicles
                 WHERE customer_id = :cid
              ORDER BY license_plate";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /** Submit a new complaint */
    public function create(int $userId, int $vehicleId, string $description, string $priority): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return false;

        // Confirm vehicle belongs to customer
        $check = $this->pdo->prepare(
            "SELECT 1 FROM vehicles WHERE vehicle_id = :v AND customer_id = :c LIMIT 1"
        );
        $check->execute(['v' => $vehicleId, 'c' => $cid]);
        if (!$check->fetchColumn()) return false;

        $sql = "INSERT INTO complaints
                    (customer_id, user_id, vehicle_id, complaint_date, complaint_time,
                     description, priority, status)
                VALUES
                    (:cid, :uid, :vid, CURDATE(), CURTIME(),
                     :desc, :priority, 'Open')";
        $st = $this->pdo->prepare($sql);
        return $st->execute([
            'cid'      => $cid,
            'uid'      => $userId,
            'vid'      => $vehicleId,
            'desc'     => $description,
            'priority' => $priority,
        ]);
    }
}
