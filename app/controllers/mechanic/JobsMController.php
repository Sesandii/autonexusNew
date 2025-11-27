<?php
namespace app\controllers\mechanic;

use app\core\Controller;
use app\core\Auth;

class JobsMController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }
    public function index(): void
    {
        $this->view('mechanic/jobs/index');  // ← use view(), not render()
    }
    private function requireMechanic(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'mechanic')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
