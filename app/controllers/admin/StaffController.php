<?php

declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Staff;

class StaffController extends Controller
{
    private Staff $staff;

    // Initialize controller dependencies and request context.
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->staff = new Staff();
    }

    // Display the main listing or dashboard page.
    public function index(): void
    {
        $filters = [
            'q'            => trim((string)($_GET['q'] ?? '')),
            'branch_id'    => trim((string)($_GET['branch_id'] ?? '')),
            'role'         => trim((string)($_GET['role'] ?? '')),
            'staff_status' => trim((string)($_GET['staff_status'] ?? '')),
        ];

        $this->view('admin/admin-viewstaff/index', [
            'current'     => 'staff',
            'pageTitle'   => 'Staff Management',
            'records'     => $this->staff->all($filters),
            'filters'     => $filters,
            'branches'    => $this->staff->getBranches(),
            'counts'      => $this->staff->roleCounts(),
            'summary'     => $this->staff->summaryCards(),
        ]);
    }

    // Handle updateStatus operation.
    public function updateStatus(): void
    {
        $role   = trim((string)($_POST['role'] ?? ''));
        $id     = (int)($_POST['staff_id'] ?? 0);
        $userId = (int)($_POST['user_id'] ?? 0);
        $status = trim((string)($_POST['status'] ?? ''));

        if ($role === '' || $id <= 0 || $userId <= 0 || $status === '') {
            http_response_code(400);
            echo 'Invalid request';
            return;
        }

        $this->staff->updateStatus($role, $id, $userId, $status);

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewstaff');
        exit;
    }

    // Handle transfer operation.
    public function transfer(): void
    {
        $role     = trim((string)($_POST['role'] ?? ''));
        $id       = (int)($_POST['staff_id'] ?? 0);
        $branchId = (int)($_POST['branch_id'] ?? 0);

        if ($role === '' || $id <= 0 || $branchId <= 0) {
            http_response_code(400);
            echo 'Invalid request';
            return;
        }

        $this->staff->transferBranch($role, $id, $branchId);

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewstaff');
        exit;
    }

    // Ensure the current session belongs to an admin user.
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