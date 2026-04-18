<?php
namespace app\controllers\Manager;
use app\core\Controller;
use app\model\Manager\DashboardModel;

class DashboardController extends BaseManagerController
{

public function __construct()
{
    parent::__construct();
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
