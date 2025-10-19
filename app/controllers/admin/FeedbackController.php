<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class FeedbackController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/admin-viewfeedback */
    public function index(): void
    {
        // Pure UI render (no DB yet)
        $this->view('admin/admin-viewfeedback/index', [
            'pageTitle' => 'Customer Feedback - AutoNexus',
            'current'   => 'feedback',
        ]);
        // If your view helper requires the extension explicitly, use:
        // $this->view('admin/admin-viewfeedback/index.php', [...]);
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
