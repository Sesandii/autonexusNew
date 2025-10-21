<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class ServiceReminder
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /**
     * Get service reminder info for all vehicles owned by the customer
     */
    public function getByCustomer(int $customerId): array
    {
        if (!$customerId) return [];

        $sql = "
            SELECT 
                v.vehicle_id,
                v.brand,
                v.model,
                v.plate_no AS reg_no,
                v.current_mileage,
                s.last_service_date,
                s.next_service_due,
                s.status
            FROM vehicles v
            LEFT JOIN service_schedule s ON s.vehicle_id = v.vehicle_id
            WHERE v.customer_id = :cid
            ORDER BY v.vehicle_id DESC
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cid' => $customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update mileage manually
     */
    public function updateMileage(int $vehicleId, int $mileage): void
    {
        $sql = "UPDATE vehicles SET current_mileage = :m WHERE vehicle_id = :v";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['m' => $mileage, 'v' => $vehicleId]);
    }
}
