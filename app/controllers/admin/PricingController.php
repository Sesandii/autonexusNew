<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class PricingController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/pricing */
    public function index(): void
    {
        // No database logic — just display the pricing UI view
        $this->view('admin/admin-updateserviceprice/index', [
            'pageTitle' => 'Service Pricing Management',
            'current'   => 'pricing',
        ]);
    }

    /** Guard: must be logged in and have admin role */
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
