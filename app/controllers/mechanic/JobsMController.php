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

        // Logged-in user ID
        $user_id = $_SESSION['user']['user_id'] ?? null;
        if (!$user_id) {
            die("Unauthorized");
        }

        // Fetch all jobs for display
        $allJobs = WorkOrder::getAllJobs();

        // Fetch only this mechanic's jobs (optional)
        $myJobs = WorkOrder::getAssignedJobs($user_id);

        // Compute progress and owner
        foreach ($allJobs as &$job) {
            // Progress calculation
            $progress = 0;
            switch ($job['status']) {
                case 'open': $progress = 20; break;
                case 'in_progress': $progress = 50; break;
                case 'completed': $progress = 100; break;
            }
            if (!empty($job['photo_count'])) $progress += 25;
            if (!empty($job['checklist_completed'])) $progress += 25;
            $job['progress'] = min($progress, 100);

            // Owner flag using user_id
            // Assuming w.mechanic_id joins to users.user_id as mechanic_user_id
            $job['owner'] = ($job['user_id'] ?? $job['mechanic_user_id'] ?? 0) == $user_id ? 'mine' : 'others';
        }

        // Send data to view
        $this->view('mechanic/jobs/index', [
            'allJobs' => $allJobs,
            'myJobs' => $myJobs,
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
