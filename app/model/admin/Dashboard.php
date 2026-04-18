<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class Dashboard
{
    private PDO $pdo;
    private string $dbName;

    public function __construct()
    {
        $this->pdo = db();
        $this->dbName = (string)$this->pdo->query('SELECT DATABASE()')->fetchColumn();
    }

    /* ---------------- helpers ---------------- */

    private function tableExists(string $table): bool
    {
        $sql = 'SELECT 1
                FROM information_schema.tables
                WHERE table_schema = :db AND table_name = :t
                LIMIT 1';
        $st = $this->pdo->prepare($sql);
        $st->execute([
            'db' => $this->dbName,
            't'  => $table,
        ]);
        return (bool)$st->fetchColumn();
    }

    private function columnExists(string $table, string $column): bool
    {
        $sql = 'SELECT 1
                FROM information_schema.columns
                WHERE table_schema = :db
                  AND table_name = :t
                  AND column_name = :c
                LIMIT 1';
        $st = $this->pdo->prepare($sql);
        $st->execute([
            'db' => $this->dbName,
            't'  => $table,
            'c'  => $column,
        ]);
        return (bool)$st->fetchColumn();
    }

    private function scalar(string $sql, array $params = [], $default = 0)
    {
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        $v = $st->fetchColumn();

        if ($v === null) {
            return $default;
        }

        return is_numeric($v) ? (0 + $v) : $default;
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ---------------- KPI metrics ---------------- */

    public function totalActiveCustomers(): int
    {
        if (!$this->tableExists('customers') || !$this->tableExists('users')) {
            return 0;
        }

        return (int)$this->scalar(
            'SELECT COUNT(*)
             FROM customers c
             INNER JOIN users u ON u.user_id = c.user_id
             WHERE u.role = "customer" AND u.status = "active"'
        );
    }

    public function totalAppointments(): int
    {
        if (!$this->tableExists('appointments')) {
            return 0;
        }

        return (int)$this->scalar('SELECT COUNT(*) FROM appointments');
    }

    public function ongoingWorkOrders(): int
    {
        if (!$this->tableExists('work_orders') || !$this->columnExists('work_orders', 'status')) {
            return 0;
        }

        return (int)$this->scalar(
            'SELECT COUNT(*)
             FROM work_orders
             WHERE status IN ("open","in_progress","on_hold")'
        );
    }

    public function servicesCompleted(): int
    {
        if ($this->tableExists('work_orders') && $this->columnExists('work_orders', 'status')) {
            return (int)$this->scalar(
                'SELECT COUNT(*)
                 FROM work_orders
                 WHERE status = "completed"'
            );
        }

        if ($this->tableExists('appointments') && $this->columnExists('appointments', 'status')) {
            return (int)$this->scalar(
                'SELECT COUNT(*)
                 FROM appointments
                 WHERE status = "completed"'
            );
        }

        return 0;
    }

    public function totalRevenue(): float
    {
        if ($this->tableExists('payments') && $this->columnExists('payments', 'amount')) {
            return (float)$this->scalar(
                'SELECT COALESCE(SUM(amount), 0)
                 FROM payments
                 WHERE status = "success"',
                [],
                0.0
            );
        }

        if ($this->tableExists('invoices') && $this->columnExists('invoices', 'grand_total')) {
            return (float)$this->scalar(
                'SELECT COALESCE(SUM(grand_total), 0)
                 FROM invoices
                 WHERE status = "paid"',
                [],
                0.0
            );
        }

        if ($this->tableExists('invoices') && $this->columnExists('invoices', 'total_amount')) {
            return (float)$this->scalar(
                'SELECT COALESCE(SUM(total_amount), 0)
                 FROM invoices
                 WHERE status = "paid"',
                [],
                0.0
            );
        }

        return 0.0;
    }

    public function feedbackCount(): int
    {
        if (!$this->tableExists('feedback')) {
            return 0;
        }

        return (int)$this->scalar('SELECT COUNT(*) FROM feedback');
    }

    public function metrics(): array
    {
        return [
            'customers'    => $this->totalActiveCustomers(),
            'appointments' => $this->totalAppointments(),
            'ongoing'      => $this->ongoingWorkOrders(),
            'completed'    => $this->servicesCompleted(),
            'revenue'      => $this->totalRevenue(),
            'feedback'     => $this->feedbackCount(),
        ];
    }

    /* ---------------- widgets ---------------- */

    public function todayAppointments(int $limit = 5): array
    {
        if (!$this->tableExists('appointments')) {
            return [];
        }

        $limit = max(1, (int)$limit);

        $sql = "
            SELECT
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                a.status,
                b.name AS branch_name,
                s.name AS service_name,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                v.license_plate
            FROM appointments a
            INNER JOIN customers c ON c.customer_id = a.customer_id
            INNER JOIN users u ON u.user_id = c.user_id
            INNER JOIN branches b ON b.branch_id = a.branch_id
            INNER JOIN services s ON s.service_id = a.service_id
            INNER JOIN vehicles v ON v.vehicle_id = a.vehicle_id
            WHERE a.appointment_date = CURDATE()
            ORDER BY a.appointment_time ASC
            LIMIT {$limit}
        ";

        return $this->fetchAll($sql);
    }

    public function pendingServiceApprovals(): int
    {
        if (!$this->tableExists('services') || !$this->columnExists('services', 'status')) {
            return 0;
        }

        return (int)$this->scalar(
            'SELECT COUNT(*)
             FROM services
             WHERE status = "pending"'
        );
    }

    public function overdueWorkOrders(int $limit = 4): array
    {
        if (
            !$this->tableExists('work_orders') ||
            !$this->tableExists('appointments') ||
            !$this->columnExists('work_orders', 'status')
        ) {
            return [];
        }

        $limit = max(1, (int)$limit);

        $sql = "
            SELECT
                w.work_order_id,
                w.status,
                w.started_at,
                w.job_start_time,
                a.appointment_id,
                a.appointment_date,
                a.appointment_time,
                b.name AS branch_name,
                s.name AS service_name,
                CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name
            FROM work_orders w
            INNER JOIN appointments a ON a.appointment_id = w.appointment_id
            INNER JOIN customers c ON c.customer_id = a.customer_id
            INNER JOIN users cu ON cu.user_id = c.user_id
            INNER JOIN branches b ON b.branch_id = a.branch_id
            INNER JOIN services s ON s.service_id = a.service_id
            WHERE w.status IN ('open','in_progress','on_hold')
              AND a.appointment_date < CURDATE()
            ORDER BY a.appointment_date ASC, a.appointment_time ASC
            LIMIT {$limit}
        ";

        return $this->fetchAll($sql);
    }

    public function recentNotifications(int $limit = 4): array
    {
        if (!$this->tableExists('notifications')) {
            return [];
        }

        $limit = max(1, (int)$limit);

        $sql = "
            SELECT
                notification_id,
                subject,
                audience,
                status,
                created_at
            FROM notifications
            ORDER BY created_at DESC
            LIMIT {$limit}
        ";

        return $this->fetchAll($sql);
    }

    public function recentComplaints(int $limit = 4): array
    {
        if (!$this->tableExists('complaints')) {
            return [];
        }

        $limit = max(1, (int)$limit);

        $sql = "
            SELECT
                cp.complaint_id,
                cp.subject,
                cp.priority,
                cp.status,
                cp.created_at,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name
            FROM complaints cp
            INNER JOIN customers c ON c.customer_id = cp.customer_id
            INNER JOIN users u ON u.user_id = c.user_id
            ORDER BY cp.created_at DESC
            LIMIT {$limit}
        ";

        return $this->fetchAll($sql);
    }

    public function recentFeedback(int $limit = 4): array
    {
        if (!$this->tableExists('feedback') || !$this->tableExists('appointments')) {
            return [];
        }

        $limit = max(1, (int)$limit);

        $sql = "
            SELECT
                f.feedback_id,
                f.rating,
                f.comment,
                f.replied_status,
                f.created_at,
                a.appointment_id,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name
            FROM feedback f
            INNER JOIN appointments a ON a.appointment_id = f.appointment_id
            INNER JOIN customers c ON c.customer_id = a.customer_id
            INNER JOIN users u ON u.user_id = c.user_id
            ORDER BY f.created_at DESC
            LIMIT {$limit}
        ";

        return $this->fetchAll($sql);
    }
}