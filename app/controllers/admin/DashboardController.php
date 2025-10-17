<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class DashboardController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin-dashboard */
    public function index(): void
    {
        // Optional: pass the logged-in user to the view
        $user = $_SESSION['user'] ?? null;

        // If you later want dynamic KPIs, you can query here and pass to the view.
        // $pdo = db(); ... fetch counts ...

        $this->view('admin/admin-dashboard/index', [
            'user' => $user,
        ]);
    }

    /** Guard: must be logged in and role=admin */
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        if (($u['role'] ?? '') !== 'admin') {
            // send non-admins to their role landing, or show 403
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
