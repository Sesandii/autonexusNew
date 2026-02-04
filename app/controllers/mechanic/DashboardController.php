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

        $userId = $_SESSION['user']['user_id']; // users.user_id
        $model = new Dashboard();
 


        // ✅ Convert user_id → mechanic_id
        $mechanicId = $model->getMechanicIdByUser($userId);
     
        if (!$mechanicId) {
            die('Mechanic profile not found');
        }

        $data = [
            'stats' => $model->getWorkorderStatsByUser($userId),
            'appointments' => $model->getTodayAppointments()
        ];

        $this->view('mechanic/dashboard/index', $data);
    }

    private function requireMechanic(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'mechanic') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}