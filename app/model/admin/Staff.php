<?php

declare(strict_types=1);

namespace app\model\admin;

use PDO;
use Exception;

class Staff
{
    private PDO $db;

    // Initialize model dependencies and database access.
    public function __construct()
    {
        $this->db = db();
    }

    // Handle getBranches operation.
    public function getBranches(): array
    {
        $sql = "SELECT branch_id, branch_code, name, city, status FROM branches ORDER BY name ASC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle roleCounts operation.
    public function roleCounts(): array
    {
        return [
            'manager'      => (int)$this->db->query("SELECT COUNT(*) FROM managers")->fetchColumn(),
            'supervisor'   => (int)$this->db->query("SELECT COUNT(*) FROM supervisors")->fetchColumn(),
            'mechanic'     => (int)$this->db->query("SELECT COUNT(*) FROM mechanics")->fetchColumn(),
            'receptionist' => (int)$this->db->query("SELECT COUNT(*) FROM receptionists")->fetchColumn(),
        ];
    }

    // Handle summaryCards operation.
    public function summaryCards(): array
    {
        $totalUsers = (int)$this->db->query("SELECT COUNT(*) FROM users WHERE role IN ('manager','supervisor','mechanic','receptionist')")->fetchColumn();
        $activeUsers = (int)$this->db->query("SELECT COUNT(*) FROM users WHERE role IN ('manager','supervisor','mechanic','receptionist') AND status='active'")->fetchColumn();
        $inactiveUsers = (int)$this->db->query("SELECT COUNT(*) FROM users WHERE role IN ('manager','supervisor','mechanic','receptionist') AND status='inactive'")->fetchColumn();
        $availableMechanics = (int)$this->db->query("SELECT COUNT(*) FROM mechanics WHERE status='available'")->fetchColumn();
        $busyMechanics = (int)$this->db->query("SELECT COUNT(*) FROM mechanics WHERE status='busy'")->fetchColumn();

        return [
            'total'      => $totalUsers,
            'active'     => $activeUsers,
            'inactive'   => $inactiveUsers,
            'available'  => $availableMechanics,
            'busy'       => $busyMechanics,
        ];
    }

    // Handle all operation.
    public function all(array $filters = []): array
    {
        $sql = "
            SELECT * FROM (
                SELECT
                    'manager' AS role,
                    m.manager_id AS staff_id,
                    m.user_id,
                    m.manager_code AS staff_code,
                    u.first_name,
                    u.last_name,
                    u.username,
                    u.email,
                    u.phone,
                    u.status AS user_status,
                    COALESCE(m.branch_id, b.branch_id) AS branch_id,
                    COALESCE(b.name, 'Not Assigned') AS branch_name,
                    COALESCE(b.branch_code, '-') AS branch_code,
                    CASE
                        WHEN u.status = 'inactive' THEN 'inactive'
                        ELSE 'active'
                    END AS staff_status,
                    'Service Manager' AS role_label,
                    u.created_at,
                    (
                        SELECT COUNT(*)
                        FROM appointments a
                        WHERE a.branch_id = COALESCE(m.branch_id, b.branch_id)
                          AND a.status IN ('requested','confirmed','assigned','in_service')
                    ) AS workload_count,
                    NULL AS extra_info
                FROM managers m
                INNER JOIN users u ON u.user_id = m.user_id
                LEFT JOIN branches b ON b.manager_id = m.manager_id

                UNION ALL

                SELECT
                    'supervisor' AS role,
                    s.supervisor_id AS staff_id,
                    s.user_id,
                    s.supervisor_code AS staff_code,
                    u.first_name,
                    u.last_name,
                    u.username,
                    u.email,
                    u.phone,
                    u.status AS user_status,
                    s.branch_id,
                    COALESCE(b.name, 'Not Assigned') AS branch_name,
                    COALESCE(b.branch_code, '-') AS branch_code,
                    CASE
                        WHEN s.status = 'inactive' OR u.status = 'inactive' THEN 'inactive'
                        ELSE 'active'
                    END AS staff_status,
                    'Workshop Supervisor' AS role_label,
                    u.created_at,
                    (
                        SELECT COUNT(*)
                        FROM appointments a
                        WHERE a.assigned_to = s.supervisor_id
                          AND a.status IN ('requested','confirmed','assigned','in_service')
                    ) AS workload_count,
                    CONCAT('Manager ID: ', s.manager_id) AS extra_info
                FROM supervisors s
                INNER JOIN users u ON u.user_id = s.user_id
                LEFT JOIN branches b ON b.branch_id = s.branch_id

                UNION ALL

                SELECT
                    'mechanic' AS role,
                    m.mechanic_id AS staff_id,
                    m.user_id,
                    m.mechanic_code AS staff_code,
                    u.first_name,
                    u.last_name,
                    u.username,
                    u.email,
                    u.phone,
                    u.status AS user_status,
                    m.branch_id,
                    COALESCE(b.name, 'Not Assigned') AS branch_name,
                    COALESCE(b.branch_code, '-') AS branch_code,
                    m.status AS staff_status,
                    'Mechanic' AS role_label,
                    u.created_at,
                    (
                        SELECT COUNT(*)
                        FROM work_orders wo
                        WHERE wo.mechanic_id = m.mechanic_id
                          AND wo.status IN ('open','in_progress','on_hold')
                    ) AS workload_count,
                    CONCAT(
                        COALESCE(m.specialization, 'General'),
                        ' / ',
                        COALESCE(m.experience_years, 0),
                        ' yrs'
                    ) AS extra_info
                FROM mechanics m
                INNER JOIN users u ON u.user_id = m.user_id
                LEFT JOIN branches b ON b.branch_id = m.branch_id

                UNION ALL

                SELECT
                    'receptionist' AS role,
                    r.receptionist_id AS staff_id,
                    r.user_id,
                    r.receptionist_code AS staff_code,
                    u.first_name,
                    u.last_name,
                    u.username,
                    u.email,
                    u.phone,
                    u.status AS user_status,
                    r.branch_id,
                    COALESCE(b.name, 'Not Assigned') AS branch_name,
                    COALESCE(b.branch_code, '-') AS branch_code,
                    CASE
                        WHEN r.status = 'inactive' OR u.status = 'inactive' THEN 'inactive'
                        ELSE 'active'
                    END AS staff_status,
                    'Receptionist' AS role_label,
                    u.created_at,
                    (
                        SELECT COUNT(*)
                        FROM appointments a
                        WHERE a.branch_id = r.branch_id
                          AND a.status IN ('requested','confirmed','assigned','in_service')
                    ) AS workload_count,
                    NULL AS extra_info
                FROM receptionists r
                INNER JOIN users u ON u.user_id = r.user_id
                LEFT JOIN branches b ON b.branch_id = r.branch_id
            ) staff
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($filters['q'])) {
            $sql .= "
                AND (
                    staff.first_name LIKE :q
                    OR staff.last_name LIKE :q
                    OR staff.email LIKE :q
                    OR staff.phone LIKE :q
                    OR staff.staff_code LIKE :q
                    OR staff.branch_name LIKE :q
                    OR staff.role_label LIKE :q
                )
            ";
            $params[':q'] = '%' . trim((string)$filters['q']) . '%';
        }

        if (!empty($filters['branch_id'])) {
            $sql .= " AND staff.branch_id = :branch_id ";
            $params[':branch_id'] = (int)$filters['branch_id'];
        }

        if (!empty($filters['role'])) {
            $sql .= " AND staff.role = :role ";
            $params[':role'] = trim((string)$filters['role']);
        }

        if (!empty($filters['staff_status'])) {
            $sql .= " AND staff.staff_status = :staff_status ";
            $params[':staff_status'] = trim((string)$filters['staff_status']);
        }

        $sql .= " ORDER BY staff.role_label ASC, staff.first_name ASC, staff.last_name ASC ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Handle updateStatus operation.
    public function updateStatus(string $role, int $staffId, int $userId, string $status): bool
    {
        $allowed = ['active', 'inactive', 'available', 'busy'];
        if (!in_array($status, $allowed, true)) {
            throw new Exception('Invalid staff status');
        }

        $this->db->beginTransaction();

        try {
            if ($role === 'mechanic') {
                $stmt = $this->db->prepare("UPDATE mechanics SET status = :status WHERE mechanic_id = :id");
                $stmt->execute([
                    ':status' => $status,
                    ':id'     => $staffId,
                ]);

                $userStatus = $status === 'inactive' ? 'inactive' : 'active';
                $userStmt = $this->db->prepare("UPDATE users SET status = :status WHERE user_id = :user_id");
                $userStmt->execute([
                    ':status'  => $userStatus,
                    ':user_id' => $userId,
                ]);
            } elseif ($role === 'supervisor') {
                $normalized = in_array($status, ['active', 'inactive'], true) ? $status : 'active';

                $stmt = $this->db->prepare("UPDATE supervisors SET status = :status WHERE supervisor_id = :id");
                $stmt->execute([
                    ':status' => $normalized,
                    ':id'     => $staffId,
                ]);

                $userStmt = $this->db->prepare("UPDATE users SET status = :status WHERE user_id = :user_id");
                $userStmt->execute([
                    ':status'  => $normalized,
                    ':user_id' => $userId,
                ]);
            } elseif ($role === 'receptionist') {
                $normalized = in_array($status, ['active', 'inactive'], true) ? $status : 'active';

                $stmt = $this->db->prepare("UPDATE receptionists SET status = :status WHERE receptionist_id = :id");
                $stmt->execute([
                    ':status' => $normalized,
                    ':id'     => $staffId,
                ]);

                $userStmt = $this->db->prepare("UPDATE users SET status = :status WHERE user_id = :user_id");
                $userStmt->execute([
                    ':status'  => $normalized,
                    ':user_id' => $userId,
                ]);
            } elseif ($role === 'manager') {
                $normalized = in_array($status, ['active', 'inactive'], true) ? $status : 'active';
                $userStmt = $this->db->prepare("UPDATE users SET status = :status WHERE user_id = :user_id");
                $userStmt->execute([
                    ':status'  => $normalized,
                    ':user_id' => $userId,
                ]);
            } else {
                throw new Exception('Invalid role');
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Handle transferBranch operation.
    public function transferBranch(string $role, int $staffId, int $branchId): bool
    {
        $map = [
            'manager'      => ['table' => 'managers', 'id_col' => 'manager_id'],
            'supervisor'   => ['table' => 'supervisors', 'id_col' => 'supervisor_id'],
            'mechanic'     => ['table' => 'mechanics', 'id_col' => 'mechanic_id'],
            'receptionist' => ['table' => 'receptionists', 'id_col' => 'receptionist_id'],
        ];

        if (!isset($map[$role])) {
            throw new Exception('Invalid role for transfer');
        }

        $sql = "UPDATE {$map[$role]['table']} SET branch_id = :branch_id WHERE {$map[$role]['id_col']} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':branch_id' => $branchId,
            ':id'        => $staffId,
        ]);
    }
}