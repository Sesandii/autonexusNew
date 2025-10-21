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
     * $q: matches service name, vehicle no, or date_booked (YYYY-MM-DD)
     */
    public function searchByCustomer(int $customerId, string $q = '', string $status = 'All'): array
    {
        if (!$customerId) return [];

        $params = ['cid' => $customerId];
        $where  = ['a.customer_id = :cid'];

        if ($status !== 'All') {
            $where[] = 'a.status = :st';
            $params['st'] = $status;
        }

        if ($q !== '') {
            $where[] = '(s.service_name LIKE :q OR a.vehicle_no LIKE :q OR a.date_booked LIKE :q)';
            $params['q'] = '%' . $q . '%';
        }

        $sql = "
            SELECT
                s.service_name      AS type,
                a.date_booked       AS date_booked,
                a.status            AS status,
                a.est_completion    AS est_completion
            FROM appointments a
            JOIN services s ON s.service_id = a.service_id
            WHERE " . implode(' AND ', $where) . "
            ORDER BY a.date_booked DESC, a.appointment_id DESC
            LIMIT 500
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        // Normalize field names to match your front-end keys
        return array_map(function ($r) {
            return [
                'type'          => (string)$r['type'],
                'dateBooked'    => (string)$r['date_booked'],
                'status'        => (string)$r['status'],
                'estCompletion' => (string)$r['est_completion'],
            ];
        }, $rows);
    }
}
