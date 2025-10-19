<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class ServicesController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/services */
    public function index(): void
    {
        // No DB calls — just render the UI view
        $this->view('admin/admin-viewservices/index', [
            'pageTitle' => 'Service Management',
            'current'   => 'services',
        ]);
    }

    /** Guard: must be logged in and role=admin */
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
