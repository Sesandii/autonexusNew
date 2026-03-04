<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class ServiceHistory
{
    private PDO $pdo;

    public function __construct()
    {
        // uses your global db() helper
        $this->pdo = db();
    }

    /**
     * Get all COMPLETED services for a given logged-in user
     * ($userId is users.user_id from the session).
     */
    public function getByCustomer(int $userId): array
    {
        if (!$userId) {
            return [];
        }

        $sql = "
            SELECT
                w.work_order_id,

                -- service date from the appointment
                a.appointment_date              AS date,

                -- nice vehicle label
                CONCAT(
                    COALESCE(v.license_plate, 'N/A'),
                    ' • ',
                    COALESCE(v.make, ''),
                    ' ',
                    COALESCE(v.model, '')
                )                               AS vehicle,

                -- service info
                s.name                          AS service_type,
                w.service_summary               AS description,

                -- mechanic / technician
                CONCAT(mu.first_name, ' ', mu.last_name)
                                              AS technician,

                -- pricing & status
                w.total_cost                    AS price,
                w.status                        AS status

            FROM work_orders w
            JOIN appointments a   ON a.appointment_id = w.appointment_id
            JOIN customers c      ON c.customer_id    = a.customer_id
            JOIN users u          ON u.user_id        = c.user_id
            LEFT JOIN services s  ON s.service_id     = a.service_id
            LEFT JOIN vehicles v  ON v.vehicle_id     = a.vehicle_id
            LEFT JOIN mechanics m ON m.mechanic_id    = w.mechanic_id
            LEFT JOIN users mu    ON mu.user_id       = m.user_id

            WHERE
                c.user_id = :uid
                AND w.status = 'completed'

            ORDER BY
                a.appointment_date DESC,
                w.completed_at     DESC,
                w.work_order_id    DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }
}
