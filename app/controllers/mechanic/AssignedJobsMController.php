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

    $user_id = $_SESSION['user']['user_id'] ?? null;
    if (!$user_id) die("User ID missing in session");

    // Get all mechanic IDs for this user
    $db = db();
    $stmt = $db->prepare("SELECT mechanic_id FROM mechanics WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $mechanic_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Fetch assigned jobs for all mechanic IDs
    $workOrders = \app\model\mechanic\WorkOrder::getAssignedJobsMultiple($mechanic_ids);

    foreach ($workOrders as &$job) {

        $progress = 0;

        switch ($job['status']) {
            case 'open':
                $progress = 20;
                break;

            case 'in_progress':
                $progress = 50;
                break;

            case 'completed':
                $progress = 100;
                break;
        }

        if (!empty($job['photo_count']) && $job['photo_count'] > 0) {
            $progress += 25;
        }

        if (!empty($job['checklist_completed']) && $job['checklist_completed'] > 0) {
            $progress += 25;
        }

        $job['progress'] = min($progress, 100);
    }

    $this->view('mechanic/assignedjobs/index', [
        'workOrders' => $workOrders
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
