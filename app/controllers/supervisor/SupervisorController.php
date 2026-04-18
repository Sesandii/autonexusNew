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
    
    $userId = $_SESSION['user']['user_id'] ?? 0;

    $db = db();
    $stmt = $db->prepare("SELECT supervisor_id, branch_id FROM supervisors WHERE user_id = ?");
    $stmt->execute([$userId]);
    $sup = $stmt->fetch();
    
    // FIX: Define these variables from the database result
    $supervisor_id = $sup['supervisor_id'] ?? 0;
    $branch_id     = $sup['branch_id'] ?? 0;
    
    // Update session so other parts of the app know the branch
    $_SESSION['user']['branch_id'] = $branch_id;
    $_SESSION['user']['supervisor_id'] = $supervisor_id;

    $model = new Dashboard();

    $data = [
        // Now passing the actual IDs fetched from the DB
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
