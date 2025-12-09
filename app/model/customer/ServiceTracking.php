<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class ServiceTracking
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /**
     * Search/filter a customer's service jobs.
     * $status: All | Pending | In Progress | Completed
     * $q: matches service name, vehicle plate, or appointment date (YYYY-MM-DD)
     */
    public function searchByCustomer(int $userId, string $q = '', string $status = 'All'): array
    {
        if (!$userId) {
            return [];
        }

        $params = [':uid' => $userId];

        // Base query: appointments for this customer's user_id
        $sql = "
            SELECT
                s.name AS service_name,
                a.appointment_date,
                a.appointment_time,
                v.license_plate,

                -- derive a friendly status for the UI
                CASE
                    WHEN wo.status = 'completed' THEN 'Completed'
                    WHEN wo.status IN ('in_progress','open') THEN 'In Progress'
                    WHEN a.status = 'cancelled' THEN 'Pending'   -- or 'Cancelled' if you add that tab
                    ELSE 'Pending'
                END AS job_status,

                -- show completed date as 'Est. completion' (or null if not completed)
                wo.completed_at AS est_completion
            FROM appointments a
            JOIN customers c ON c.customer_id = a.customer_id
            JOIN users u     ON u.user_id     = c.user_id
            JOIN services s  ON s.service_id  = a.service_id
            LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
            LEFT JOIN work_orders wo ON wo.appointment_id = a.appointment_id
            WHERE u.user_id = :uid
        ";

        $where = [];

        // Text search: service name, plate, or date
        if ($q !== '') {
            $where[]        = '(s.name LIKE :q OR v.license_plate LIKE :q OR a.appointment_date LIKE :q)';
            $params[':q']   = '%' . $q . '%';
        }

        // Status filter using derived job_status
        if ($status !== 'All') {
            $where[]          = 'job_status = :st';
            $params[':st']    = $status;
        }

        if ($where) {
            // use HAVING because job_status is an alias in SELECT
            $sql .= ' HAVING ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY a.appointment_date DESC, a.appointment_id DESC LIMIT 500';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // normalise to front-end keys
        return array_map(function (array $r): array {
            $date = $r['appointment_date'];
            if (!empty($r['appointment_time'])) {
                $date .= ' ' . $r['appointment_time'];
            }

            return [
                'type'          => (string)($r['service_name'] ?? ''),
                'dateBooked'    => (string)$date,
                'status'        => (string)($r['job_status'] ?? 'Pending'),
                'estCompletion' => (string)($r['est_completion'] ?? ''),
                'vehicle'       => (string)($r['license_plate'] ?? ''),
            ];
        }, $rows);
    }
}
