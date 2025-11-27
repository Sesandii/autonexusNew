<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\OngoingService;

class OngoingServicesController extends Controller
{
    private OngoingService $ongoing;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->ongoing = new OngoingService();
    }

    /** GET /admin/admin-ongoingservices */
    public function index(): void
    {
        // date comes from query ?date=Y-m-d, fallback = today
        $date = $_GET['date'] ?? date('Y-m-d');
        $rows = $this->ongoing->getForDate($date);
        $branches = $this->ongoing->getBranches();

        $workOrders = [];
        foreach ($rows as $r) {
            $datetime = $r['appointment_date'] . ' ' . $r['appointment_time'];

            $workOrders[] = [
                'id'               => (int)$r['work_order_id'],
                'service'          => $r['service_name'],
                'duration_minutes' => (int)($r['base_duration_minutes'] ?? 0),
                'customer'         => $r['customer_name'],
                'branch'           => $r['branch_name'],
                'branch_id'        => (int)$r['branch_id'],
                'mechanic'         => $r['mechanic_name'] ?? 'Unassigned',
                'status_db'        => $r['work_status'],
                'status_ui'        => OngoingService::uiStatus($r['work_status']),
                'appointment_time' => $r['appointment_time'],
                'datetime'         => $datetime,
            ];
        }

        $this->view('admin/admin-ongoingservices/index', [
            'workOrders'      => $workOrders,
            'branches'        => $branches,
            'selectedDate'    => $date,
            'currentDateText' => (new \DateTime($date))->format('M j, Y'),
            'pageTitle'       => 'Ongoing Services',
            'current'         => 'progress',
        ]);
    }

    /** GET /admin/admin-ongoingservices/show?id=123 */
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Invalid work order id.";
            return;
        }

        $workOrder = $this->ongoing->findWithDetails($id);
        if (!$workOrder) {
            http_response_code(404);
            echo "Work order not found.";
            return;
        }

        $this->view('admin/admin-ongoingservices/show', [
            'workOrder' => $workOrder,
            'pageTitle' => 'Work Order #' . $id,
            'current'   => 'progress',
        ]);
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
