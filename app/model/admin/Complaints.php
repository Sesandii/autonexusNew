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
                s.service_id,
                s.name AS service_name,
                s.service_code,

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
            LEFT JOIN services s ON s.service_id = a.service_id
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
            if ($filters['assigned_to'] === 'unassigned') {
                $where[] = "cp.assigned_to_user_id IS NULL";
            } else {
                $where[] = "cp.assigned_to_user_id = ?";
                $params[] = (int)$filters['assigned_to'];
            }
        }

        if (!empty($filters['sla'])) {
            [$slaSql, $slaParams] = $this->buildSlaWhere($filters['sla']);
            if ($slaSql !== '') {
                $where[] = $slaSql;
                $params = array_merge($params, $slaParams);
            }
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
                OR s.name LIKE ?
            )";

            $like = '%' . $filters['search'] . '%';
            for ($i = 0; $i < 8; $i++) {
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
                  LIMIT 300";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as &$row) {
            $this->decorateComplaint($row);
        }

        return $rows;
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
                s.service_id,
                s.name AS service_name,
                s.service_code,

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
            LEFT JOIN services s ON s.service_id = a.service_id
            LEFT JOIN vehicles v ON v.vehicle_id = cp.vehicle_id
            LEFT JOIN branches b ON b.branch_id = cp.branch_id
            LEFT JOIN users au ON au.user_id = cp.assigned_to_user_id
            WHERE cp.complaint_id = :id
            LIMIT 1
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        $this->decorateComplaint($row);
        $row['timeline'] = $this->extractTimeline((string)($row['description'] ?? ''));

        return $row;
    }

    public function update(int $id, array $data): bool
    {
        $status         = $data['status'] ?? 'open';
        $priority       = $data['priority'] ?? 'medium';
        $assignedTo     = $data['assigned_to_user_id'] ?? null;
        $resolutionNote = trim((string)($data['resolution_note'] ?? ''));

        $resolvedAt = null;
        if (in_array($status, ['resolved', 'closed'], true)) {
            $resolvedAt = date('Y-m-d H:i:s');
        }

        if ($status === 'open' || $status === 'in_progress') {
            $resolvedAt = null;
        }

        $newDescription = null;
        if ($resolutionNote !== '') {
            $existing = $this->find($id);
            if (!$existing) {
                return false;
            }

            $existingDescription = (string)($existing['description'] ?? '');
            $stampLabel = in_array($status, ['resolved', 'closed'], true) ? 'Resolution Note' : 'Admin Note';
            $stamp = '[' . date('Y-m-d H:i') . '] ' . $stampLabel . ': ' . $resolutionNote;
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
            ':status'              => $status,
            ':priority'            => $priority,
            ':assigned_to_user_id' => $assignedTo !== '' ? $assignedTo : null,
            ':resolved_at'         => $resolvedAt,
            ':description'         => $newDescription,
            ':id'                  => $id,
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

    public function summaryCards(): array
    {
        $sql = "
            SELECT
                COUNT(*) AS total_count,
                SUM(CASE WHEN status = 'open' THEN 1 ELSE 0 END) AS open_count,
                SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) AS progress_count,
                SUM(CASE WHEN status IN ('resolved','closed') THEN 1 ELSE 0 END) AS done_count,
                SUM(CASE WHEN priority = 'high' AND status IN ('open','in_progress') THEN 1 ELSE 0 END) AS urgent_open_count,
                SUM(CASE WHEN assigned_to_user_id IS NULL AND status IN ('open','in_progress') THEN 1 ELSE 0 END) AS unassigned_count
            FROM complaints
        ";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_count'      => (int)($row['total_count'] ?? 0),
            'open_count'       => (int)($row['open_count'] ?? 0),
            'progress_count'   => (int)($row['progress_count'] ?? 0),
            'done_count'       => (int)($row['done_count'] ?? 0),
            'urgent_open_count'=> (int)($row['urgent_open_count'] ?? 0),
            'unassigned_count' => (int)($row['unassigned_count'] ?? 0),
        ];
    }

    public function analytics(): array
    {
        return [
            'by_branch'   => $this->analyticsByBranch(),
            'by_staff'    => $this->analyticsByStaff(),
            'by_service'  => $this->analyticsByService(),
            'by_priority' => $this->analyticsByPriority(),
            'by_status'   => $this->analyticsByStatus(),
        ];
    }

    public function assignmentQueue(): array
    {
        $sql = "
            SELECT
                cp.complaint_id,
                cp.subject,
                cp.priority,
                cp.status,
                cp.created_at,
                b.name AS branch_name,
                CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name
            FROM complaints cp
            INNER JOIN customers c ON c.customer_id = cp.customer_id
            INNER JOIN users cu ON cu.user_id = c.user_id
            LEFT JOIN branches b ON b.branch_id = cp.branch_id
            WHERE cp.assigned_to_user_id IS NULL
              AND cp.status IN ('open', 'in_progress')
            ORDER BY
                CASE cp.priority
                    WHEN 'high' THEN 1
                    WHEN 'medium' THEN 2
                    ELSE 3
                END,
                cp.created_at ASC
            LIMIT 20
        ";

        $rows = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $this->decorateComplaint($row);
        }
        return $rows;
    }

    private function analyticsByBranch(): array
    {
        $sql = "
            SELECT
                COALESCE(b.name, 'Unassigned Branch') AS label,
                COUNT(*) AS total
            FROM complaints cp
            LEFT JOIN branches b ON b.branch_id = cp.branch_id
            GROUP BY COALESCE(b.name, 'Unassigned Branch')
            ORDER BY total DESC, label ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function analyticsByStaff(): array
    {
        $sql = "
            SELECT
                COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Unassigned') AS label,
                COUNT(*) AS total
            FROM complaints cp
            LEFT JOIN users u ON u.user_id = cp.assigned_to_user_id
            GROUP BY COALESCE(CONCAT(u.first_name, ' ', u.last_name), 'Unassigned')
            ORDER BY total DESC, label ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function analyticsByService(): array
    {
        $sql = "
            SELECT
                COALESCE(s.name, 'No Appointment Service') AS label,
                COUNT(*) AS total
            FROM complaints cp
            LEFT JOIN appointments a ON a.appointment_id = cp.appointment_id
            LEFT JOIN services s ON s.service_id = a.service_id
            GROUP BY COALESCE(s.name, 'No Appointment Service')
            ORDER BY total DESC, label ASC
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function analyticsByPriority(): array
    {
        $sql = "
            SELECT priority AS label, COUNT(*) AS total
            FROM complaints
            GROUP BY priority
            ORDER BY FIELD(priority, 'high', 'medium', 'low')
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function analyticsByStatus(): array
    {
        $sql = "
            SELECT status AS label, COUNT(*) AS total
            FROM complaints
            GROUP BY status
            ORDER BY FIELD(status, 'open', 'in_progress', 'resolved', 'closed')
        ";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    private function buildSlaWhere(string $sla): array
    {
        if ($sla === 'due_soon') {
            return [
                "cp.status IN ('open','in_progress') AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) BETWEEN 24 AND 47",
                []
            ];
        }

        if ($sla === 'breached') {
            return [
                "cp.status IN ('open','in_progress') AND (
                    (cp.priority = 'high' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) > 12)
                    OR (cp.priority = 'medium' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) > 24)
                    OR (cp.priority = 'low' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) > 48)
                )",
                []
            ];
        }

        if ($sla === 'healthy') {
            return [
                "cp.status IN ('open','in_progress') AND (
                    (cp.priority = 'high' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) <= 12)
                    OR (cp.priority = 'medium' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) <= 24)
                    OR (cp.priority = 'low' AND TIMESTAMPDIFF(HOUR, cp.created_at, NOW()) <= 48)
                )",
                []
            ];
        }

        return ['', []];
    }

    private function decorateComplaint(array &$row): void
    {
        $createdAt = (string)($row['created_at'] ?? '');
        $resolvedAt = $row['resolved_at'] ?? null;
        $priority = (string)($row['priority'] ?? 'medium');
        $status = (string)($row['status'] ?? 'open');

        $ageHours = $createdAt ? max(0, (int)floor((time() - strtotime($createdAt)) / 3600)) : 0;
        $row['age_hours'] = $ageHours;
        $row['aging_label'] = $this->formatAging($ageHours);

        [$slaStatus, $slaHoursLeft] = $this->computeSla($priority, $status, $createdAt);
        $row['sla_status'] = $slaStatus;
        $row['sla_hours_left'] = $slaHoursLeft;

        $escalated = false;
        if (in_array($status, ['open', 'in_progress'], true)) {
            if ($slaStatus === 'breached') {
                $escalated = true;
            }
            if ($priority === 'high' && empty($row['assigned_to_user_id']) && $ageHours >= 2) {
                $escalated = true;
            }
        }
        $row['escalated'] = $escalated ? 1 : 0;

        $row['can_reopen'] = in_array($status, ['resolved', 'closed'], true) ? 1 : 0;
        $row['is_resolvedish'] = in_array($status, ['resolved', 'closed'], true) ? 1 : 0;
        $row['resolution_time_label'] = ($resolvedAt && $createdAt)
            ? $this->formatAging(max(0, (int)floor((strtotime((string)$resolvedAt) - strtotime($createdAt)) / 3600)))
            : '—';
    }

    private function computeSla(string $priority, string $status, string $createdAt): array
    {
        if (!in_array($status, ['open', 'in_progress'], true) || $createdAt === '') {
            return ['completed', null];
        }

        $limit = 24;
        if ($priority === 'high') {
            $limit = 12;
        } elseif ($priority === 'medium') {
            $limit = 24;
        } elseif ($priority === 'low') {
            $limit = 48;
        }

        $ageHours = max(0, (int)floor((time() - strtotime($createdAt)) / 3600));
        $left = $limit - $ageHours;

        if ($left < 0) {
            return ['breached', $left];
        }

        if ($left <= 4) {
            return ['due_soon', $left];
        }

        return ['healthy', $left];
    }

    private function formatAging(int $hours): string
    {
        if ($hours < 24) {
            return $hours . 'h';
        }

        $days = intdiv($hours, 24);
        $rem  = $hours % 24;

        if ($rem === 0) {
            return $days . 'd';
        }

        return $days . 'd ' . $rem . 'h';
    }

    private function extractTimeline(string $description): array
    {
        $lines = preg_split('/\R/', $description) ?: [];
        $timeline = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^\[(.*?)\]\s*(Admin Note|Resolution Note)\:\s*(.+)$/i', $line, $m)) {
                $timeline[] = [
                    'stamp'   => $m[1],
                    'type'    => $m[2],
                    'message' => $m[3],
                ];
            }
        }

        return array_reverse($timeline);
    }
}