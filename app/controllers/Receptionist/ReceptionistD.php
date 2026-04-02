<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\DashboardModel;

class ReceptionistD extends Controller
{
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