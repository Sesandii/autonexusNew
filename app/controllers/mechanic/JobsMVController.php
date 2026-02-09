<?php
namespace app\controllers\mechanic;

Use PDO;

use app\core\Controller;
use app\model\mechanic\WorkOrder;


class JobsMVController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }

    public function show($id)
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $logged_user_id = $_SESSION['user']['user_id'] ?? null;
    if (!$logged_user_id) {
        die("Unauthorized");
    }

    $job = WorkOrder::getSingleJob((int)$id);
    if (!$job) die("Job not found.");

    $pdo = db();
    $stmt = $pdo->prepare("SELECT user_id FROM mechanics WHERE mechanic_id = ?");
    $stmt->execute([$job['mechanic_id']]);
    $job_user_id = $stmt->fetchColumn();

    $can_edit = ((int)$logged_user_id === (int)$job_user_id);
    $woModel = new WorkOrder();
    $services = $woModel->getSummaryFromChecklist(
        (int)$job['work_order_id']
        );

        $stmt = $pdo->prepare("SELECT id, file_name FROM service_photos WHERE work_order_id = ?");
        $stmt->execute([$job['work_order_id']]);
        $photos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $this->view('mechanic/jobs/view', [
        'job' => $job,
        'services' => $services,
        'photos' => $photos,
        'can_edit' => $can_edit
    ]);
}

    public function updateStatus()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $logged_user_id = $_SESSION['user']['user_id'] ?? null;
    $work_order_id  = (int)($_POST['work_order_id'] ?? 0);
    $newStatus      = $_POST['status'] ?? 'open';

    $job = WorkOrder::getSingleJob($work_order_id);
    if (!$job) die("Job not found.");

    $pdo = db();
    $stmt = $pdo->prepare("SELECT user_id FROM mechanics WHERE mechanic_id = ?");
    $stmt->execute([$job['mechanic_id']]);
    $job_user_id = $stmt->fetchColumn();


    if ((int)$job_user_id !== (int)$logged_user_id) {
        die("Unauthorized");
    }


    $m = new WorkOrder();
    $m->setStatusMechanic($work_order_id, $newStatus, $job['mechanic_id']);

    header("Location: " . rtrim(BASE_URL, '/') . "/mechanic/jobs/view/" . $work_order_id);
    exit;
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
