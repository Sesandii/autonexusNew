<?php
// app/model/customer/Vehicles.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class Vehicles
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

    /**
     * Vehicles that belong to the given user_id (via customers table).
     * Returns: vehicle_id, license_plate, make, model, year
     */
    public function byUserId(int $userId): array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [];

        $sql = "SELECT v.vehicle_id, v.license_plate, v.make, v.model, v.year
                FROM vehicles v
                WHERE v.customer_id = :cid
                ORDER BY v.license_plate";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
