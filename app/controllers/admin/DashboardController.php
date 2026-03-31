<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use PDO;

class DashboardController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin-dashboard */
    public function index(): void
    {
        $user = $_SESSION['user'] ?? null;
        $pdo  = db();

        // --- KPI queries (adapt to your schema) ---
        $totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role ='customer' AND status = 'active' ")->fetchColumn() ?? 0;
        $totalAppointments = $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn() ?? 0;

        // Completed services (work_orders or appointments)
        if ($this->tableExists($pdo, 'work_orders')) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM work_orders WHERE status IN ('completed','done','closed')");
            $completedServices = $stmt->fetchColumn() ?? 0;
        } elseif ($this->tableExists($pdo, 'appointments')) {
            $stmt = $pdo->query("SELECT COUNT(*) FROM appointments WHERE status IN ('completed','done')");
            $completedServices = $stmt->fetchColumn() ?? 0;
        } else {
            $completedServices = 0;
        }

        // Total revenue (payments or invoices)
        if ($this->tableExists($pdo, 'payments')) {
            $stmt = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status IN ('paid','success')");
            $totalRevenue = $stmt->fetchColumn() ?? 0;
        } elseif ($this->tableExists($pdo, 'invoices')) {
            $stmt = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM invoices WHERE status='paid'");
            $totalRevenue = $stmt->fetchColumn() ?? 0;
        } else {
            $totalRevenue = 0;
        }

        // Feedback count
        $feedbackCount = $this->tableExists($pdo, 'feedback')
            ? ($pdo->query("SELECT COUNT(*) FROM feedback")->fetchColumn() ?? 0)
            : 0;

        // --- Pass to view ---
        $metrics = [
            'users'        => (int)$totalUsers,
            'appointments' => (int)$totalAppointments,
            'completed'    => (int)$completedServices,
            'revenue'      => (float)$totalRevenue,
            'feedback'     => (int)$feedbackCount,
        ];

        $this->view('admin/admin-dashboard/index', [
            'user'     => $user,
            'metrics'  => $metrics,
        ]);
    }

    /** Utility: check if table exists safely */
    private function tableExists(PDO $pdo, string $table): bool
    {
        // Escape any backticks in case a table name is unusual
        $safeTable = str_replace('`', '``', $table);
        $sql = "SHOW TABLES LIKE " . $pdo->quote($safeTable);

        $stmt = $pdo->query($sql);
        return (bool)$stmt->fetchColumn();
    }


    /** Guard: must be logged in and role=admin */
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u || ($u['role'] ?? '') !== 'admin') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
