<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

/**
 * Provides searchable customer service-tracking rows for list and JSON APIs.
 */
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
        // Wrap in a subquery to allow filtering by derived job_status
        $sql = "
            SELECT * FROM (
                SELECT
                    a.appointment_id,
                    s.name AS service_name,
                    COALESCE(s.base_duration_minutes, 0) AS duration_minutes,
                    a.appointment_date,
                    a.appointment_time,
                    v.license_plate,
                    b.name AS branch_name,

                    -- derive a friendly status for the UI
                    CASE
                        WHEN wo.status = 'completed' THEN 'Completed'
                        WHEN wo.status IN ('in_progress','open') THEN 'In Progress'
                        WHEN a.status = 'cancelled' THEN 'Cancelled'
                        WHEN wo.work_order_id IS NOT NULL THEN 'In Progress'
                        ELSE 'Pending'
                    END AS job_status,

                    -- show completed date as 'Est. completion' (or null if not completed)
                    wo.completed_at AS est_completion
                FROM appointments a
                JOIN customers c ON c.customer_id = a.customer_id
                LEFT JOIN services s  ON s.service_id  = a.service_id
                LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
                LEFT JOIN branches b ON b.branch_id = a.branch_id
                LEFT JOIN work_orders wo ON wo.appointment_id = a.appointment_id
                WHERE c.user_id = :uid
            ) AS derived
        ";

        $where = [];

        // Text search: service name, plate, or date
        if ($q !== '') {
            $where[]        = '(service_name LIKE :q OR license_plate LIKE :q OR appointment_date LIKE :q)';
            $params[':q']   = '%' . $q . '%';
        }

        // Status filter
        if ($status !== 'All') {
            $where[]          = 'job_status = :st';
            $params[':st']    = $status;
        }

        if ($where) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= ' ORDER BY appointment_date DESC, appointment_time DESC LIMIT 500';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // normalise to front-end keys
        return array_map(function (array $r): array {
            $date = (string)($r['appointment_date'] ?? '');
            $time = (string)($r['appointment_time'] ?? '');
            $dateBooked = trim($date . ' ' . $time);

            $time24 = '';
            $timeDisplay = '-';
            if ($time !== '') {
                $ts = strtotime('1970-01-01 ' . $time);
                if ($ts !== false) {
                    $time24 = date('H:i', $ts);
                    $timeDisplay = date('g:i A', $ts);
                }
            }

            return [
                'appointmentId' => (int)($r['appointment_id'] ?? 0),
                'type'          => (string)($r['service_name'] ?? ''),
                'dateBooked'    => (string)$dateBooked,
                'status'        => (string)($r['job_status'] ?? 'Pending'),
                'estCompletion' => (string)($r['est_completion'] ?? ''),
                'vehicle'       => (string)($r['license_plate'] ?? ''),
                'branch'        => (string)($r['branch_name'] ?? 'Unknown Branch'),
                'durationMinutes'=> (int)($r['duration_minutes'] ?? 0),
                'appointmentDate'=> $date,
                'time24'        => $time24,
                'timeDisplay'   => $timeDisplay,
            ];
        }, $rows);
    }
}
