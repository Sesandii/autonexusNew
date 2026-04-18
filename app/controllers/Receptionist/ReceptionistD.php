<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\DashboardModel;

class ReceptionistD extends Controller
{
     private function guardReceptionist(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'receptionist') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
        $stmt = db()->prepare('SELECT branch_id FROM receptionists WHERE user_id = :uid LIMIT 1');
       
        $stmt->execute(['uid' => $u['user_id']]);
        $receptionist = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$receptionist) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $receptionist['branch_id'];
    }
}

public function __construct()
{
    parent::__construct();
    $this->guardReceptionist(); // 🔐 enforce manager login & branch
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
        $this->view('Receptionist/Dashboard/index', [
            'pendingCount'      => $pendingCount,
            'ongoingCount'      => $ongoingCount,
            'todayAppointments' => $todayAppointments,
            'recentActivities'  => $recentActivities
        ]);
    }
}