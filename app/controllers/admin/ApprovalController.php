<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class ApprovalController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/approval */
    public function index(): void
    {
        // No database calls yet — purely UI preview
        $this->view('admin/admin-serviceapproval/index', [
            'pageTitle' => 'Service Approval Queue',
            'current'   => 'approval',
        ]);
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
