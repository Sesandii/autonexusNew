<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class VehicleReportsController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }
    public function index()
    {
        // Later you can add database logic via a Report model
        $this->view('supervisor/reports/index');
    }
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
