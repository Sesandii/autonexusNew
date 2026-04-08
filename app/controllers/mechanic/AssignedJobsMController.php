<?php
namespace app\controllers\mechanic;

use PDO;

use app\core\Controller;
use app\model\mechanic\WorkOrder;

class AssignedJobsMController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }


    public function index()
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    date_default_timezone_set('Asia/Colombo'); // CRITICAL: Match DB timezone

    $user_id = $_SESSION['user']['user_id'] ?? null;
    $db = db();
    $stmt = $db->prepare("SELECT mechanic_id FROM mechanics WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $mechanic_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (empty($mechanic_ids)) {
        $this->view('mechanic/assignedjobs/index', ['currentJobs' => [], 'openJobs' => []]);
        return;
    }

    $workOrderModel = new \app\model\mechanic\WorkOrder();
    $workOrders = $workOrderModel->getAssignedJobsMultiple($mechanic_ids);

    $currentJobs = [];
    $openJobs = [];

    foreach ($workOrders as $job) {
        $totalSeconds = $job['base_duration_minutes'] * 60;

        // --- TIMER CALCULATION ---
        if ($job['status'] === 'on_hold') {
            $job['seconds_left'] = $job['paused_remaining_seconds'] ?? $totalSeconds;
        } elseif ($job['status'] === 'in_progress' && !empty($job['job_start_time'])) {
            $elapsed = time() - strtotime($job['job_start_time']);
            
            // If we resumed from a pause, we subtract the elapsed time from the saved remaining time
            $availableTime = $job['paused_remaining_seconds'] ?? $totalSeconds;
            $job['seconds_left'] = max(0, $availableTime - $elapsed);
        } else {
            $job['seconds_left'] = $totalSeconds;
        }

        // --- PROGRESS CALCULATION ---
        $progress = 0;
        // Base status weight
        switch ($job['status']) {
            case 'open': $progress = 20; break;
            case 'in_progress': $progress = 50; break;
            case 'on_hold': $progress = 50; break;
            case 'completed': $progress = 100; break;
        }

        // Checklist Weight (25%)
        $counts = $workOrderModel->getProgressCounts((int)$job['work_order_id']);
        if ($counts['total'] > 0) {
            $itemWeight = 25 / $counts['total'];
            $checklistProgress = $counts['completed'] * $itemWeight;
            $progress += $checklistProgress;
        }

        // Photos Weight (30%)
        if (!empty($job['photo_count']) && $job['photo_count'] > 0) {
            $progress += 25;
        }

        $job['progress'] = min(round($progress), 100);

        // --- SORTING ---
        if ($job['status'] === 'in_progress') {
            $currentJobs[] = $job;
        } else {
            $openJobs[] = $job;
        }
    }

    $this->view('mechanic/assignedjobs/index', [
        'currentJobs' => $currentJobs,
        'openJobs'    => $openJobs
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

    // WorkOrdersController.php
public function setStatus($workOrderId) {
    $data = json_decode(file_get_contents('php://input'), true);
    $status = $data['status'] ?? null;
    if (!$status) return;

    $mechanicId = $_SESSION['user']['user_id'] ?? 0;
    $model = new \app\model\mechanic\WorkOrder();
    $model->setStatusMechanic($workOrderId, $status, $mechanicId);

    echo json_encode(['success'=>true]);
}

}
