<?php
namespace app\controllers\mechanic;

use app\core\Controller;
use app\model\mechanic\WorkOrder;


class JobsMController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }

public function index(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $user_id = $_SESSION['user']['user_id'] ?? null;
    if (!$user_id) die("Unauthorized");

    $allJobs = [];
    $myJobs = [];

    if (!isset($_SESSION['user']['branch_id'])) {
        $db = db();
        $stmt = $db->prepare("SELECT branch_id FROM mechanics WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $mech = $stmt->fetch();
        if ($mech) {
            $_SESSION['user']['branch_id'] = $mech['branch_id'];
        }
    }

    $branch_id = $_SESSION['user']['branch_id'] ?? null;

    if ($branch_id) {
        $workOrderModel = new WorkOrder(); 
        
        $allJobs = WorkOrder::getAllJobs((int)$branch_id);
        $myJobs  = WorkOrder::getAssignedJobs((int)$user_id);

        foreach ($allJobs as &$job) {

            $totalSeconds = $job['base_duration_minutes'] * 60;
            
            if ($job['status'] === 'on_hold') {
                $job['seconds_left'] = $job['paused_remaining_seconds'] ?? $totalSeconds;
            } elseif ($job['status'] === 'in_progress' && !empty($job['job_start_time'])) {
                $elapsed = time() - strtotime($job['job_start_time']);
                $job['seconds_left'] = max(0, $totalSeconds - $elapsed);
            } else {
                $job['seconds_left'] = $totalSeconds;
            }
            $progress = 0;
            switch ($job['status']) {
                case 'open': $progress = 20; break;
                case 'in_progress': $progress = 50; break;
                case 'on_hold': $progress = 50; break;
                case 'completed': $progress = 100; break;
            }
    
            if (!empty($job['photo_count'])) $progress += 25;
    
            $counts = $workOrderModel->getProgressCounts((int)$job['work_order_id']);
            if ($counts['total'] > 0) {
                $itemWeight = 25 / $counts['total'];
                $checklistProgress = $counts['completed'] * $itemWeight;
                $progress += $checklistProgress;
            }
            $job['progress'] = min($progress, 100);
    
            $job['owner'] = ($job['mechanic_user_id'] ?? 0) == $user_id ? 'mine' : 'others';
        }
    }

    $this->view('mechanic/jobs/index', [
        'allJobs' => $allJobs,
        'myJobs'  => $myJobs,
        'user_id' => $user_id
    ]);
}

    private function requireMechanic(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'mechanic')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
