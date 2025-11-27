<?php
namespace app\controllers\mechanic;

use app\core\Controller;

class HistoryController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }
    public function index()
    {
        // Later you can fetch mechanic's completed jobs or vehicle history from DB here
        $this->view('mechanic/history/index');
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
