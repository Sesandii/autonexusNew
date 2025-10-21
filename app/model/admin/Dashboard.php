<?php
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
    private function tableExists(string $table): bool {
        $sql = 'SELECT 1 FROM information_schema.tables WHERE table_schema = :db AND table_name = :t LIMIT 1';
        $st = $this->pdo->prepare($sql);
        $st->execute(['db'=>$this->dbName,'t'=>$table]);
        return (bool)$st->fetchColumn();
    }
    private function columnExists(string $table, string $column): bool {
        $sql = 'SELECT 1 FROM information_schema.columns WHERE table_schema = :db AND table_name = :t AND column_name = :c LIMIT 1';
        $st = $this->pdo->prepare($sql);
        $st->execute(['db'=>$this->dbName,'t'=>$table,'c'=>$column]);
        return (bool)$st->fetchColumn();
    }
    private function scalar(string $sql, array $params = [], $default = 0) {
        $st = $this->pdo->prepare($sql);
        $st->execute($params);
        $v = $st->fetchColumn();
        return $v === null ? $default : (is_numeric($v) ? (0 + $v) : $default);
    }

    /* ---------------- metrics ---------------- */

    /** Active users (falls back to total if no status column) */
    public function totalUsers(): int {
        if (!$this->tableExists('users')) return 0;
        if ($this->columnExists('users','status')) {
            return (int)$this->scalar('SELECT COUNT(*) FROM users WHERE status = "active"');
        }
        return (int)$this->scalar('SELECT COUNT(*) FROM users');
    }

    /** Appointments count (ignores cancelled if a status column exists) */
    public function totalAppointments(): int {
        if (!$this->tableExists('appointments')) return 0;
        if ($this->columnExists('appointments','status')) {
            return (int)$this->scalar('SELECT COUNT(*) FROM appointments WHERE status NOT IN ("cancelled","canceled")');
        }
        return (int)$this->scalar('SELECT COUNT(*) FROM appointments');
    }

    /** Services completed (works with work_orders or appointments) */
    public function servicesCompleted(): int {
        // Prefer work_orders / service_jobs if present
        if ($this->tableExists('work_orders')) {
            if ($this->columnExists('work_orders','status')) {
                return (int)$this->scalar('SELECT COUNT(*) FROM work_orders WHERE status IN ("completed","done","closed")');
            }
            return (int)$this->scalar('SELECT COUNT(*) FROM work_orders');
        }
        // Fallback: appointments with status completed
        if ($this->tableExists('appointments') && $this->columnExists('appointments','status')) {
            return (int)$this->scalar('SELECT COUNT(*) FROM appointments WHERE status IN ("completed","done")');
        }
        return 0;
    }

    /** Total revenue (tries payments first, then invoices) */
    public function totalRevenue(): float {
        // payments.amount (status=paid/success OR paid_at not null)
        if ($this->tableExists('payments') && $this->columnExists('payments','amount')) {
            $where = [];
            $params = [];
            if ($this->columnExists('payments','status')) $where[] = 'status IN ("paid","success")';
            if ($this->columnExists('payments','paid_at')) $where[] = 'paid_at IS NOT NULL';
            $sql = 'SELECT COALESCE(SUM(amount),0) FROM payments';
            if ($where) $sql .= ' WHERE ' . implode(' OR ', $where);
            return (float)$this->scalar($sql);
        }
        // invoices.total_amount (status=paid OR paid_at not null)
        if ($this->tableExists('invoices')) {
            $amountCol = $this->columnExists('invoices','total_amount') ? 'total_amount'
                      : ($this->columnExists('invoices','amount') ? 'amount' : null);
            if ($amountCol) {
                $where = [];
                if ($this->columnExists('invoices','status')) $where[] = 'status = "paid"';
                if ($this->columnExists('invoices','paid_at')) $where[] = 'paid_at IS NOT NULL';
                $sql = "SELECT COALESCE(SUM($amountCol),0) FROM invoices";
                if ($where) $sql .= ' WHERE ' . implode(' OR ', $where);
                return (float)$this->scalar($sql);
            }
        }
        return 0.0;
    }

    /** Feedback count */
    public function feedbackCount(): int {
        if (!$this->tableExists('feedback')) return 0;
        return (int)$this->scalar('SELECT COUNT(*) FROM feedback');
    }

    /** Convenience for the controller */
    public function metrics(): array {
        return [
            'users'       => $this->totalUsers(),
            'appointments'=> $this->totalAppointments(),
            'completed'   => $this->servicesCompleted(),
            'revenue'     => $this->totalRevenue(),
            'feedback'    => $this->feedbackCount(),
        ];
    }
}
