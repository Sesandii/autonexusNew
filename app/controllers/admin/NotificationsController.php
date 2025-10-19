<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class NotificationsController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/admin-notifications */
    public function index(): void
    {
        // Pure UI render (no DB yet)
        $this->view('admin/admin-notifications/index', [
            'pageTitle' => 'Notifications - AutoNexus',
            'current'   => 'notifications',
        ]);
        // If your view() requires the extension explicitly:
        // $this->view('admin/admin-notifications/index.php', [...]);
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
