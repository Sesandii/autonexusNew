<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class ServiceHistoryController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/admin-servicehistory */
    public function index(): void
    {
        // No DB yet — just render your static UI view
        $this->view('admin/admin-servicehistory/index', [
            'pageTitle' => 'Service History',
            'current'   => 'history',
        ]);
        // If your base Controller::view requires .php explicitly, use:
        // $this->view('admin/admin-servicehistory/index.php', [...]);
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
