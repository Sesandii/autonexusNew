<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class OngoingServicesController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/admin-ongoingservices */
    public function index(): void
    {
        // No DB yet — just render your UI file
        $this->view('admin/admin-ongoingservices/index', [
            'pageTitle' => 'Ongoing Services',
            'current'   => 'progress',
        ]);
        // If your base Controller::view requires the extension, use:
        // $this->view('admin/admin-ongoingservices/index.php', [...]);
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
