<?php
namespace app\controllers\mechanic;

use app\core\Controller;
use \app\model\mechanic\WorkOrder;

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

        // Get mechanic_id from session
        $mechanic_id = $_SESSION['user']['mechanic_id'] ?? null;

        if (!$mechanic_id) {
            die("Mechanic ID missing in session");
        }

        // Fetch assigned work orders
        $workOrders = WorkOrder::getAssignedJobs($mechanic_id);

        // Pass to view
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
