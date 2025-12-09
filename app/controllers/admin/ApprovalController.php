<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\ServiceApproval;

class ApprovalController extends Controller
{
    private ServiceApproval $approval;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->approval = new ServiceApproval();
    }

    /** GET /admin/admin-serviceapproval */
    public function index(): void
    {
        $filters = [
            'q'         => trim($_GET['q'] ?? ''),
            'from'      => $_GET['from']      ?? '',
            'to'        => $_GET['to']        ?? '',
            'branch_id' => $_GET['branch_id'] ?? '',
            'type_id'   => $_GET['type_id']   ?? '',
        ];

        $rows         = $this->approval->listPending($filters);
        $branches     = $this->approval->getBranches();
        $serviceTypes = $this->approval->getServiceTypes();

        // simple flash message
        $message = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        // map DB rows → card data
        $cards = [];
        foreach ($rows as $r) {
            $cards[] = [
                'id'           => (int)$r['service_id'],
                'code'         => $r['service_code'],
                'name'         => $r['service_name'],
                'type'         => $r['type_name'],
                'branches'     => $r['branch_names'] ?: '—',
                'submitted_by' => trim(($r['submitted_first'] ?? '') . ' ' . ($r['submitted_last'] ?? '')) ?: '—',
                'created_at'   => $r['created_at'],
                'duration'     => (int)$r['base_duration_minutes'],
                'price'        => (float)$r['default_price'],
            ];
        }

        $this->view('admin/admin-serviceapproval/index', [
            'pageTitle'    => 'Service Approval Queue',
            'current'      => 'approval',
            'filters'      => $filters,
            'branches'     => $branches,
            'serviceTypes' => $serviceTypes,
            'cards'        => $cards,
            'message'      => $message,
        ]);
    }

    /** GET /admin/admin-serviceapproval/show?id=123 */
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Missing service id.";
            return;
        }

        $service = $this->approval->find($id);
        if (!$service) {
            http_response_code(404);
            echo "Service not found.";
            return;
        }

        $this->view('admin/admin-serviceapproval/show', [
            'pageTitle' => 'Service Details',
            'current'   => 'approval',
            'service'   => $service,
        ]);
    }

    /** GET /admin/admin-serviceapproval/edit?id=123 */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Missing service id.";
            return;
        }

        $service = $this->approval->find($id);
        if (!$service) {
            http_response_code(404);
            echo "Service not found.";
            return;
        }

        $this->view('admin/admin-serviceapproval/edit', [
            'pageTitle' => 'Review Service',
            'current'   => 'approval',
            'service'   => $service,
        ]);
    }

    /** POST /admin/admin-serviceapproval/update */
    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-serviceapproval');
            return;
        }

        $id     = (int)($_POST['id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if ($id <= 0 || !in_array($action, ['approve','reject'], true)) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-serviceapproval');
            return;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $adminId = (int)($_SESSION['user']['user_id'] ?? 0);

        if ($adminId <= 0) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            return;
        }

        if ($action === 'approve') {
            $this->approval->approve($id, $adminId);
            $_SESSION['flash'] = 'Service approved successfully.';
        } else {
            $this->approval->reject($id, $adminId);
            $_SESSION['flash'] = 'Service rejected.';
        }

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-serviceapproval');
        exit;
    }

    /** Guard: only admins can access */
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
