<?php
namespace app\controllers\mechanic;

use app\core\Controller;
use app\model\mechanic\Dashboard;

class DashboardController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }

    public function index()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $mechanic_id = $_SESSION['user']['user_id']; // Mechanic's user ID

        $model = new Dashboard();

        $data = [
            'stats' => $model->getWorkorderStats($mechanic_id),
            'appointments' => $model->getTodayAppointments($mechanic_id)
        ];

        $this->view('mechanic/dashboard/index', $data);
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
