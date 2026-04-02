<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class Complaints
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function list(array $filters = []): array
    {
        $sql = "
            SELECT
                cp.complaint_id,
                cp.subject,
                cp.description,
                cp.priority,
                cp.status,
                cp.assigned_to_user_id,
                cp.created_at,
                cp.updated_at,
                cp.resolved_at,

                c.customer_id,
                c.customer_code,
                CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name,

                a.appointment_id,
                a.appointment_date,
                a.appointment_time,

                v.vehicle_id,
                v.vehicle_code,
                v.license_plate,
                v.make,
                v.model,

                b.branch_id,
                b.branch_code,
                b.name AS branch_name,

                au.user_id AS assigned_user_id,
                CONCAT(au.first_name, ' ', au.last_name) AS assigned_user_name,
                au.role AS assigned_user_role
            FROM complaints cp
            INNER JOIN customers c ON c.customer_id = cp.customer_id
            INNER JOIN users cu ON cu.user_id = c.user_id
            LEFT JOIN appointments a ON a.appointment_id = cp.appointment_id
            LEFT JOIN vehicles v ON v.vehicle_id = cp.vehicle_id
            LEFT JOIN branches b ON b.branch_id = cp.branch_id
            LEFT JOIN users au ON au.user_id = cp.assigned_to_user_id
            WHERE 1 = 1
        ";

        $params = [];
        $where = [];

        if (!empty($filters['status'])) {
            $where[] = "cp.status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['priority'])) {
            $where[] = "cp.priority = ?";
            $params[] = $filters['priority'];
        }

        if (!empty($filters['branch_id'])) {
            $where[] = "cp.branch_id = ?";
            $params[] = (int)$filters['branch_id'];
        }

        if (!empty($filters['assigned_to'])) {
            $where[] = "cp.assigned_to_user_id = ?";
            $params[] = (int)$filters['assigned_to'];
        }

        if (!empty($filters['search'])) {
            $where[] = "(
                cp.subject LIKE ?
                OR cp.description LIKE ?
                OR CONCAT(cu.first_name, ' ', cu.last_name) LIKE ?
                OR c.customer_code LIKE ?
                OR v.license_plate LIKE ?
                OR v.vehicle_code LIKE ?
                OR b.name LIKE ?
            )";

            $like = '%' . $filters['search'] . '%';
            for ($i = 0; $i < 7; $i++) {
                $params[] = $like;
            }
        }

        if (!empty($where)) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        $sql .= " ORDER BY
                    CASE cp.status
                        WHEN 'open' THEN 1
                        WHEN 'in_progress' THEN 2
                        WHEN 'resolved' THEN 3
                        WHEN 'closed' THEN 4
                        ELSE 5
                    END,
                    CASE cp.priority
                        WHEN 'high' THEN 1
                        WHEN 'medium' THEN 2
                        WHEN 'low' THEN 3
                        ELSE 4
                    END,
                    cp.created_at DESC
                  LIMIT 200";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                cp.complaint_id,
                cp.subject,
                cp.description,
                cp.priority,
                cp.status,
                cp.assigned_to_user_id,
                cp.created_at,
                cp.updated_at,
                cp.resolved_at,

                c.customer_id,
                c.customer_code,
                CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name,
                cu.email AS customer_email,
                cu.phone AS customer_phone,

                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status AS appointment_status,

                v.vehicle_id,
                v.vehicle_code,
                v.license_plate,
                v.make,
                v.model,
                v.year,
                v.color,

                b.branch_id,
                b.branch_code,
                b.name AS branch_name,
                b.city AS branch_city,
                b.phone AS branch_phone,

                au.user_id AS assigned_user_id,
                CONCAT(au.first_name, ' ', au.last_name) AS assigned_user_name,
                au.email AS assigned_user_email,
                au.phone AS assigned_user_phone,
                au.role AS assigned_user_role
            FROM complaints cp
            INNER JOIN customers c ON c.customer_id = cp.customer_id
            INNER JOIN users cu ON cu.user_id = c.user_id
            LEFT JOIN appointments a ON a.appointment_id = cp.appointment_id
            LEFT JOIN vehicles v ON v.vehicle_id = cp.vehicle_id
            LEFT JOIN branches b ON b.branch_id = cp.branch_id
            LEFT JOIN users au ON au.user_id = cp.assigned_to_user_id
            WHERE cp.complaint_id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function update(int $id, array $data): bool
    {
        $status = $data['status'] ?? 'open';
        $priority = $data['priority'] ?? 'medium';
        $assignedTo = $data['assigned_to_user_id'] ?? null;
        $resolutionNote = trim((string)($data['resolution_note'] ?? ''));

        $resolvedAt = null;
        if (in_array($status, ['resolved', 'closed'], true)) {
            $resolvedAt = date('Y-m-d H:i:s');
        }

        $newDescription = null;
        if ($resolutionNote !== '') {
            $existing = $this->find($id);
            if (!$existing) {
                return false;
            }

            $existingDescription = (string)($existing['description'] ?? '');
            $stamp = '[' . date('Y-m-d H:i') . '] Admin Note: ' . $resolutionNote;
            $newDescription = rtrim($existingDescription) . "\n\n" . $stamp;
        }

        $sql = "
            UPDATE complaints
            SET
                status = :status,
                priority = :priority,
                assigned_to_user_id = :assigned_to_user_id,
                resolved_at = :resolved_at,
                description = COALESCE(:description, description)
            WHERE complaint_id = :id
        ";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':status' => $status,
            ':priority' => $priority,
            ':assigned_to_user_id' => $assignedTo !== '' ? $assignedTo : null,
            ':resolved_at' => $resolvedAt,
            ':description' => $newDescription,
            ':id' => $id,
        ]);
    }

    public function getBranches(): array
    {
        $sql = "SELECT branch_id, branch_code, name FROM branches WHERE status = 'active' ORDER BY name";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAssignableUsers(): array
    {
        $sql = "
            SELECT
                user_id,
                CONCAT(first_name, ' ', last_name) AS full_name,
                role,
                status
            FROM users
            WHERE role IN ('admin', 'manager', 'supervisor', 'receptionist')
              AND status = 'active'
            ORDER BY
                FIELD(role, 'admin', 'manager', 'supervisor', 'receptionist'),
                first_name,
                last_name
        ";

        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}