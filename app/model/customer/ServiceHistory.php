<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class ServiceHistory
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // db() should return PDO
    }

    /**
     * Return completed services for the logged in customer (by users.user_id)
     */
    public function getByCustomer(int $userId): array
    {
        if ($userId <= 0) return [];

        try {
            $sql = "
                SELECT
                    w.work_order_id,
                    a.appointment_id,
                    a.appointment_date AS date,
                    a.appointment_time AS time,

                    CONCAT(
                        COALESCE(v.license_plate, 'N/A'),
                        ' • ',
                        COALESCE(v.make, ''),
                        ' ',
                        COALESCE(v.model, '')
                    ) AS vehicle,

                    s.name AS service_type,
                    w.service_summary AS description,

                    CONCAT(COALESCE(mu.first_name,''), ' ', COALESCE(mu.last_name,'')) AS technician,

                    w.total_cost AS price,
                    w.status     AS status,
                    
                    b.name AS branch_name

                FROM work_orders w
                JOIN appointments a   ON a.appointment_id = w.appointment_id
                JOIN customers c      ON c.customer_id    = a.customer_id
                LEFT JOIN vehicles v  ON v.vehicle_id     = a.vehicle_id
                LEFT JOIN services s  ON s.service_id     = a.service_id
                LEFT JOIN mechanics m ON m.mechanic_id    = w.mechanic_id
                LEFT JOIN users mu    ON mu.user_id       = m.user_id
                LEFT JOIN branches b  ON b.branch_id      = a.branch_id

                WHERE c.user_id = :uid
                  -- Temporarily show all statuses for debugging
                  -- AND LOWER(w.status) = 'completed'

                ORDER BY a.appointment_date DESC, w.completed_at DESC, w.work_order_id DESC
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['uid' => $userId]);

            $result = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
            
            // Debug: Log the query and result
            error_log("ServiceHistory Query - UserID: $userId, Results: " . count($result));
            
            return $result;
        } catch (\PDOException $e) {
            error_log("ServiceHistory Query Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get a single service record by work_order_id
     */
    public function getById(int $workOrderId): ?array
    {
        if ($workOrderId <= 0) return null;

        $sql = "
            SELECT
                w.work_order_id,
                w.service_summary AS description,
                w.status,
                w.total_cost AS price,
                w.created_at,
                w.completed_at,
                
                a.appointment_id,
                a.appointment_date AS date,
                a.appointment_time AS time,
                
                CONCAT(
                    COALESCE(v.license_plate, 'N/A'),
                    ' • ',
                    COALESCE(v.make, ''),
                    ' ',
                    COALESCE(v.model, '')
                ) AS vehicle,
                
                v.make,
                v.model,
                v.license_plate,
                v.year AS vehicle_year,
                
                s.name AS service_type,
                s.description AS service_description,
                
                CONCAT(COALESCE(mu.first_name,''), ' ', COALESCE(mu.last_name,'')) AS technician,
                
                b.name AS branch_name,
                b.address AS branch_address,
                b.phone AS branch_phone,
                
                CONCAT(COALESCE(cu.first_name,''), ' ', COALESCE(cu.last_name,'')) AS customer_name,
                cu.email AS customer_email,
                cu.phone AS customer_phone

            FROM work_orders w
            JOIN appointments a   ON a.appointment_id = w.appointment_id
            JOIN customers c      ON c.customer_id    = a.customer_id
            JOIN users cu         ON cu.user_id       = c.user_id
            LEFT JOIN vehicles v  ON v.vehicle_id     = a.vehicle_id
            LEFT JOIN services s  ON s.service_id     = a.service_id
            LEFT JOIN mechanics m ON m.mechanic_id    = w.mechanic_id
            LEFT JOIN users mu    ON mu.user_id       = m.user_id
            LEFT JOIN branches b  ON b.branch_id      = a.branch_id

            WHERE w.work_order_id = :woid
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['woid' => $workOrderId]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }
}
