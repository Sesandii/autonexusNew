<?php
declare(strict_types=1);

namespace app\model\admin;

use app\core\Database;
use PDO;

class ServiceApproval
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    /**
     * List pending services (status = 'pending') with filters
     *
     * @param array $filters
     *  - q          : search text
     *  - from       : date Y-m-d
     *  - to         : date Y-m-d
     *  - branch_id  : int
     *  - type_id    : int
     */
    public function listPending(array $filters): array
    {
        $sql = "
            SELECT
                s.service_id,
                s.service_code,
                s.name AS service_name,
                s.description,
                s.base_duration_minutes,
                s.default_price,
                s.status,
                s.created_at,
                st.type_name,
                u_submit.first_name AS submitted_first,
                u_submit.last_name  AS submitted_last,
                GROUP_CONCAT(DISTINCT b.name ORDER BY b.name SEPARATOR ', ') AS branch_names
            FROM services s
            LEFT JOIN service_types   st ON st.type_id   = s.type_id
            LEFT JOIN users        u_submit ON u_submit.user_id = s.submitted_by
            LEFT JOIN branch_services bs ON bs.service_id = s.service_id
            LEFT JOIN branches        b  ON b.branch_id  = bs.branch_id
            WHERE s.status = 'pending'
        ";

        $params = [];

        if (!empty($filters['branch_id'])) {
            $sql .= " AND b.branch_id = :branch_id";
            $params[':branch_id'] = (int)$filters['branch_id'];
        }

        if (!empty($filters['type_id'])) {
            $sql .= " AND s.type_id = :type_id";
            $params[':type_id'] = (int)$filters['type_id'];
        }

        if (!empty($filters['from'])) {
            $sql .= " AND s.created_at >= :from";
            $params[':from'] = $filters['from'] . ' 00:00:00';
        }

        if (!empty($filters['to'])) {
            $sql .= " AND s.created_at <= :to";
            $params[':to'] = $filters['to'] . ' 23:59:59';
        }

        if (!empty($filters['q'])) {
            $sql .= " AND (s.name LIKE :q OR s.service_code LIKE :q OR b.name LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        $sql .= "
            GROUP BY s.service_id
            ORDER BY s.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBranches(): array
    {
        $stmt = $this->db->query("
            SELECT branch_id, name
            FROM branches
            WHERE status = 'active'
            ORDER BY name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServiceTypes(): array
    {
        $stmt = $this->db->query("
            SELECT type_id, type_name
            FROM service_types
            ORDER BY type_name
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Detailed info for a single service + branches + submit/approve users
     */
    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                s.*,
                st.type_name,
                u_submit.first_name AS submitted_first,
                u_submit.last_name  AS submitted_last,
                u_approve.first_name AS approved_first,
                u_approve.last_name  AS approved_last
            FROM services s
            LEFT JOIN service_types st   ON st.type_id   = s.type_id
            LEFT JOIN users u_submit     ON u_submit.user_id = s.submitted_by
            LEFT JOIN users u_approve    ON u_approve.user_id = s.approved_by
            WHERE s.service_id = :id
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        // branches offering this service
        $stmt2 = $this->db->prepare("
            SELECT b.name
            FROM branch_services bs
            JOIN branches b ON b.branch_id = bs.branch_id
            WHERE bs.service_id = :id
            ORDER BY b.name
        ");
        $stmt2->execute([':id' => $id]);
        $row['branches'] = $stmt2->fetchAll(PDO::FETCH_COLUMN);

        return $row;
    }

    public function approve(int $id, int $adminUserId): bool
    {
        $sql = "
            UPDATE services
            SET status = 'active',
                approved_by = :admin,
                approved_at = NOW(),
                updated_at  = NOW()
            WHERE service_id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':admin' => $adminUserId,
            ':id'    => $id,
        ]);
    }

    public function reject(int $id, int $adminUserId): bool
    {
        // if you add 'rejected' to ENUM use that instead of 'inactive'
        $sql = "
            UPDATE services
            SET status = 'rejected',
                approved_by = :admin,
                approved_at = NOW(),
                updated_at  = NOW()
            WHERE service_id = :id
        ";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':admin' => $adminUserId,
            ':id'    => $id,
        ]);
    }
}
