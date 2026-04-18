<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Dashboard;

class DashboardController extends Controller
{
    private Dashboard $dashboard;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->dashboard = new Dashboard();
    }

    /** GET /admin-dashboard */
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $user = $_SESSION['user'] ?? null;

        $this->view('admin/admin-dashboard/index', [
            'user'                => $user,
            'metrics'             => $this->dashboard->metrics(),
            'todayAppointments'   => $this->dashboard->todayAppointments(),
            'pendingApprovals'    => $this->dashboard->pendingServiceApprovals(),
            'overdueWorkOrders'   => $this->dashboard->overdueWorkOrders(),
            'recentNotifications' => $this->dashboard->recentNotifications(),
            'recentComplaints'    => $this->dashboard->recentComplaints(),
            'recentFeedback'      => $this->dashboard->recentFeedback(),
        ]);
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