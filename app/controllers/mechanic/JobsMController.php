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

        $mechanic_id = $_SESSION['user']['user_id'] ?? null;
        if (!$mechanic_id) {
            die("Unauthorized");
        }

        // Fetch all jobs for display
        $allJobs = WorkOrder::getAllJobs();

        // Fetch only this mechanic's jobs for editable status
        $myJobs = WorkOrder::getAssignedJobs($mechanic_id);

        $this->view('mechanic/jobs/index', [
            'allJobs' => $allJobs,
            'myJobs' => $myJobs,
            'mechanic_id' => $mechanic_id
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
