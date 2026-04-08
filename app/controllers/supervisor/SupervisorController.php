<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Dashboard;

class SupervisorController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }
    
    public function index()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        // 1. Define the variable as $supervisor_id (to match your line 36)
        $supervisor_id = $_SESSION['user']['user_id'] ?? 0;
        
        $model = new Dashboard();
    
        // 2. Fetch the branch_id from the supervisor table
        $branch_id = $model->getSupervisorBranch((int)$supervisor_id);
        
    
        // 3. Save it to session so it's available for other pages
        $_SESSION['user']['branch_id'] = $branch_id;
    
        $data = [
            // Line 36: Now $supervisor_id is defined and won't be NULL
            'stats'          => $model->getWorkorderStats((int)$supervisor_id, (int)$branch_id),
            'appointments'   => $model->getTodayAppointments((int)$branch_id), 
            'inProgressJobs' => $model->getInProgressJobs((int)$supervisor_id),
            'weeklyTrend'    => $model->getWeeklyAppointments((int)$branch_id) 
        ];
    
        $this->view('supervisor/dashboard/index', $data);
    }


    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
