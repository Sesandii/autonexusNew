<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class ReceptionistsController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/viewreceptionist */
    public function index(): void
    {
        // Pure UI render (no DB)
        $this->view('admin/admin-viewreceptionist/index', [
            'pageTitle' => 'Receptionists Management',
            'current'   => 'receptionists',
        ]);
        // If your view() requires .php explicitly, use:
        // $this->view('admin/viewreceptionist/index.php', [...]);
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
