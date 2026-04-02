<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ScheduleModel;

class ScheduleController extends Controller
{
    protected ScheduleModel $model;

    public function __construct()
    {
        parent::__construct();
        $db = db(); // your PDO helper function
        $this->model = new ScheduleModel($db);
    }

    // Default schedule view (all team members for a branch)
    public function index(): void
    {
        $branchId = 3; // example branch
        $users = $this->model->getTeamMembers($branchId);

        $this->view('manager/Schedule/schedule', [
            'pageTitle' => 'Team Schedule',
            'users'     => $users,
            'branchId'  => $branchId
        ]);
    }

    // Daily schedule view
    public function day(): void
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $mechanicId = $_GET['mechanic'] ?? null;

        $mechanics = $this->model->getMechanicsForDay($date);
        $workOrders = [];

        if ($mechanicId) {
            $workOrders = $this->model->getWorkOrdersForMechanic($date, (int)$mechanicId);
        }

        $this->view('manager/Schedule/daySchedule', [
            'pageTitle'  => 'Daily Schedule',
            'date'       => $date,
            'mechanics'  => $mechanics,
            'workOrders' => $workOrders,
            'activeMech' => $mechanicId
        ]);
    }

    public function member(): void
{
    $userId = $_GET['id'] ?? null;

    if (!$userId) {
        header('Location: /manager/schedule');
        exit;
    }

    $member = $this->model->getTeamMemberDetails((int)$userId);

    $this->view('manager/Schedule/memberProfile', [
        'pageTitle' => 'Team Member Details',
        'member'    => $member
    ]);
}

}
