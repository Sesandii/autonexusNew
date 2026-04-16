<?php
namespace app\controllers\Manager;
use app\core\Controller;
use app\model\Manager\DashboardModel;

class DashboardController extends Controller
{
    private function guardManager(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'manager') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
       $stmt = db()->prepare('SELECT branch_id FROM managers WHERE user_id = :uid LIMIT 1');
        $stmt->execute(['uid' => $u['user_id']]);
        $manager = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$manager) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $manager['branch_id'];
    }
}

public function __construct()
{
    parent::__construct();
    $this->guardManager(); // 🔐 enforce manager login & branch
}    

 public function index(): void
    {
        // Require the user to be logged in as a customer (or receptionist)
        $this->requireLogin();

        $dashboard = new DashboardModel();

        // Fetch counts and recent activities
        $pendingCount      = $dashboard->getPendingServicesCount();
        $ongoingCount      = $dashboard->getOngoingServicesCount();
        $todayAppointments = $dashboard->getTodayAppointmentsCount();
        $recentActivities  = $dashboard->getRecentActivities();

        // Pass to the view
        $this->view('manager/Dashboard/dashboard', [
            'pendingCount'      => $pendingCount,
            'ongoingCount'      => $ongoingCount,
            'todayAppointments' => $todayAppointments,
            'recentActivities'  => $recentActivities
        ]);
    }
}
