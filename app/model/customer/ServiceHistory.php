<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class ServiceHistory
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /**
     * Get all service history records for a specific customer
     */
    public function getByCustomer(int $customerId): array
    {
        if (!$customerId) return [];

        $sql = "SELECT 
                    a.appointment_id,
                    a.vehicle_no AS vehicle,
                    a.service_date AS date,
                    s.service_name AS service_type,
                    a.description,
                    m.first_name AS technician,
                    a.total_price AS price,
                    a.status,
                    a.report_pdf AS pdf
                FROM appointments a
                JOIN services s ON s.service_id = a.service_id
                LEFT JOIN mechanics m ON m.mechanic_id = a.mechanic_id
                WHERE a.customer_id = :cid
                ORDER BY a.service_date DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cid' => $customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
