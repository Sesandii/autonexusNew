<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class ReportsController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/admin-viewreports */
    public function index(): void
    {
        // Just render the UI (no DB yet)
        $this->view('admin/admin-viewreports/index', [
            'pageTitle' => 'Reports - AutoNexus',
            'current'   => 'reports',
        ]);
        // If your view() requires .php explicitly:
        // $this->view('admin/admin-viewreports/index.php', [...]);
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
