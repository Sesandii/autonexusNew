<?php
declare(strict_types=1);

namespace app\model\admin;

use app\core\Database;
use PDO;

class OngoingService
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // your global db() function
    }

    /**
     * Map DB status -> UI label.
     */
    public static function uiStatus(string $dbStatus): string
    {
        switch ($dbStatus) {
            case 'open':        return 'Received';
            case 'in_progress': return 'In Service';
            case 'completed':   return 'Completed';
            default:            return ucfirst(str_replace('_', ' ', $dbStatus));
        }
    }

    /**
     * Ongoing services (work orders) for a given date (appointments on that date).
     *
     * @param string $date Y-m-d
     * @return array
     */
    public function getForDate(string $date): array
    {
        $sql = "
            SELECT
                w.work_order_id,
                w.status              AS work_status,
                w.started_at,
                w.completed_at,
                w.total_cost,

                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status              AS appointment_status,

                s.service_id,
                s.name                AS service_name,
                s.base_duration_minutes,

                b.branch_id,
                b.name                AS branch_name,

                c.customer_id,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,

                m.mechanic_id,
                CONCAT(mu.first_name, ' ', mu.last_name) AS mechanic_name
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN customers c    ON c.customer_id   = a.customer_id
            JOIN users u        ON u.user_id       = c.user_id
            JOIN services s     ON s.service_id    = a.service_id
            JOIN branches b     ON b.branch_id     = a.branch_id
            LEFT JOIN mechanics m ON m.mechanic_id = w.mechanic_id
            LEFT JOIN users mu    ON mu.user_id    = m.user_id
            WHERE
                a.appointment_date = :date
                AND w.status IN ('open', 'in_progress', 'completed')
            ORDER BY a.appointment_time ASC, w.work_order_id ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':date' => $date]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Single work order with full details for show.php
     */
    public function findWithDetails(int $id): ?array
    {
        $sql = "
            SELECT
                w.work_order_id,
                w.status              AS work_status,
                w.started_at,
                w.completed_at,
                w.total_cost,
                w.service_summary,

                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status              AS appointment_status,
                a.notes               AS appointment_notes,
                a.created_at          AS appointment_created_at,
                a.updated_at          AS appointment_updated_at,

                s.service_id,
                s.service_code,
                s.name                AS service_name,
                s.base_duration_minutes,
                s.default_price,

                b.branch_id,
                b.name                AS branch_name,
                b.city                AS branch_city,
                b.address_line        AS branch_address,
                b.phone               AS branch_phone,

                c.customer_id,
                c.customer_code,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                u.phone               AS customer_phone,
                u.email               AS customer_email,

                v.vehicle_id,
                v.vehicle_code,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                v.color,

                m.mechanic_id,
                m.mechanic_code,
                CONCAT(mu.first_name, ' ', mu.last_name) AS mechanic_name,
                mu.phone             AS mechanic_phone,
                m.specialization,
                m.experience_years,

                sup.supervisor_id,
                sup.supervisor_code,
                CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name,
                su.phone             AS supervisor_phone
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN customers c    ON c.customer_id   = a.customer_id
            JOIN users u        ON u.user_id       = c.user_id
            LEFT JOIN vehicles v ON v.vehicle_id   = a.vehicle_id
            JOIN services s     ON s.service_id    = a.service_id
            JOIN branches b     ON b.branch_id     = a.branch_id

            LEFT JOIN mechanics m  ON m.mechanic_id  = w.mechanic_id
            LEFT JOIN users mu     ON mu.user_id     = m.user_id

            LEFT JOIN supervisors sup ON sup.branch_id = b.branch_id AND sup.status = 'active'
            LEFT JOIN users su        ON su.user_id    = sup.user_id

            WHERE w.work_order_id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Branch list for filters.
     */
    public function getBranches(): array
    {
        $sql = "SELECT branch_id, name FROM branches WHERE status = 'active' ORDER BY name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
