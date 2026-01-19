<?php
namespace app\controllers\mechanic;

use app\core\Controller;
use app\model\mechanic\WorkOrder;

class JobsMVController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }

    /** Show a single job detail */
    public function show($id)
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $logged_user_id = $_SESSION['user']['user_id'] ?? null;
    if (!$logged_user_id) {
        die("Unauthorized");
    }

    $job = WorkOrder::getSingleJob((int)$id);
    if (!$job) die("Job not found.");

    // ✅ Fetch the user_id of the assigned mechanic
    $pdo = db();
    $stmt = $pdo->prepare("SELECT user_id FROM mechanics WHERE mechanic_id = ?");
    $stmt->execute([$job['mechanic_id']]);
    $job_user_id = $stmt->fetchColumn();

    // ✅ Determine if the logged-in mechanic can edit
    $can_edit = ((int)$logged_user_id === (int)$job_user_id);

    // Render the job view page
    $this->view('mechanic/jobs/view', [
        'job' => $job,
        'can_edit' => $can_edit
    ]);
}



    /** Update job status by mechanic */
    public function updateStatus()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $logged_user_id = $_SESSION['user']['user_id'] ?? null;
    $work_order_id  = (int)($_POST['work_order_id'] ?? 0);
    $newStatus      = $_POST['status'] ?? 'open';

    $job = WorkOrder::getSingleJob($work_order_id);
    if (!$job) die("Job not found.");

    // ✅ Fetch the user_id of the assigned mechanic
    $pdo = db();
    $stmt = $pdo->prepare("SELECT user_id FROM mechanics WHERE mechanic_id = ?");
    $stmt->execute([$job['mechanic_id']]);
    $job_user_id = $stmt->fetchColumn();

    // ✅ Only allow mechanic with matching user_id to update
    if ((int)$job_user_id !== (int)$logged_user_id) {
        die("Unauthorized");
    }

    // ✅ Update status
    $m = new WorkOrder();
    $m->setStatusMechanic($work_order_id, $newStatus, $job['mechanic_id']);

    // Redirect back to the job view page
    header("Location: " . rtrim(BASE_URL, '/') . "/mechanic/jobs/view/" . $work_order_id);
    exit;
}

    /** Require mechanic to be logged in */
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
