<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\ServiceHistory;

class ServiceHistoryController extends Controller
{
    private ServiceHistory $history;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->history = new ServiceHistory();
    }

    /** GET /admin/admin-servicehistory */
    public function index(): void
    {
        $filters = [
            'search'    => trim($_GET['q']        ?? ''),
            'from'      => $_GET['from']          ?? '',
            'to'        => $_GET['to']            ?? '',
            'branch_id' => $_GET['branch_id']     ?? '',
            'type_id'   => $_GET['service_type']  ?? '',
        ];

        $records      = $this->history->list($filters);
        $branches     = $this->history->getBranches();
        $serviceTypes = $this->history->getServiceTypes();

        $cards = [];
        foreach ($records as $r) {
            $completedAt = $r['completed_at'] ?? null;
            $completedDt = $completedAt ? new \DateTime($completedAt) : null;

            $cards[] = [
                'id'            => (int)$r['work_order_id'],
                'service_name'  => $r['service_name'],
                'service_type'  => $r['service_type'] ?? 'Service',
                'branch_name'   => $r['branch_name'],
                'customer_name' => $r['customer_name'],
                'vehicle_label' => trim(($r['license_plate'] ?? '') !== ''
                                        ? $r['license_plate']
                                        : trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? ''))),
                'mechanic_name' => $r['mechanic_name'] ?? '—',
                'total_cost'    => (float)$r['total_cost'],
                'completed_at'  => $completedDt ? $completedDt->format('M j, Y g:i A') : '—',
                'completed_date'=> $completedDt ? $completedDt->format('Y-m-d') : '',
            ];
        }

        $this->view('admin/admin-servicehistory/index', [
            'cards'        => $cards,
            'branches'     => $branches,
            'serviceTypes' => $serviceTypes,
            'filters'      => $filters,
            'pageTitle'    => 'Service History',
            'current'      => 'history',
        ]);
    }

    /** GET /admin/admin-servicehistory/show?id=123 */
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid work order ID';
            return;
        }

        $record = $this->history->find($id);
        if (!$record) {
            http_response_code(404);
            echo 'Service record not found';
            return;
        }

        $this->view('admin/admin-servicehistory/show', [
            'record'    => $record,
            'pageTitle' => 'Service #' . $id,
            'current'   => 'history',
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
