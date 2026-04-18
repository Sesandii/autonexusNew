<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\PerformanceModel;

class PerformanceController extends Controller
{
    protected PerformanceModel $model;

        private function guardManager(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'manager') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
        $stmt = db()->prepare('SELECT branch_id FROM managers WHERE user_id = :uid LIMIT 1');
       
        $stmt->execute(['uid' => $u['user_id']]);
        $manager = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$manager) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $manager['branch_id'];
    }
}

    public function __construct()
    {
        parent::__construct();
        $this->guardManager(); // 🔐 enforce manager login & branch
        $this->model = new PerformanceModel(db());
    }

    /* ==========================
       MAIN TEAM PERFORMANCE PAGE
       ========================== */
    public function index(): void
    {
        $branchId = $_SESSION['user']['branch'] ?? 2;

        $mechanics = $this->model->getMechanicsWithCompletedJobs($branchId);

        $this->view('Manager/Team Performance/performance', [
            'pageTitle' => 'Team Performance',
            'mechanics' => $mechanics
        ]);
    }

    /* ==========================
       STATS API
       ========================== */
    public function stats(): void
    {
        $branchId = $_SESSION['user']['branch'] ?? 2;
        $date = $_GET['date'] ?? null;

        $stats = [
            'completed_jobs' => $this->model->getTotalCompletedJobs($branchId, $date),
            'customer_satisfaction' => $this->model->getAvgCustomerSatisfaction($branchId, $date),
            'avg_service_time' => $this->model->getAvgServiceTime($branchId, $date),
            'return_rate' => $this->model->getReturnRate($branchId, $date),
            'revenue' => $this->model->getTotalRevenue($branchId, $date),
        ];

        header('Content-Type: application/json');
        echo json_encode($stats);
    }

    /* ==========================
       CHART API
       ========================== */
    public function jobsByDay(): void
    {
        $branchId = $_SESSION['user']['branch'] ?? 2;
        $date = $_GET['date'] ?? null;

        $data = $this->model->getCompletedJobsByDay($branchId, $date);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /* ==========================
       INDIVIDUAL MECHANIC PAGE
       ========================== */
    public function viewMechanic(): void
    {
        $mechanicId = isset($_GET['id']) ? (int)$_GET['id'] : null;
        if (!$mechanicId) {
            header('Location: ' . BASE_URL . '/manager/performance');
            exit;
        }

        $month = $_GET['month'] ?? date('Y-m');
        $startDate = $month . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $mechanic = $this->model->getMechanicById($mechanicId);
        if (!$mechanic) {
            header('Location: ' . BASE_URL . '/manager/performance');
            exit;
        }

        $stats = $this->model->getMechanicStats($mechanicId, $startDate, $endDate);
        $jobsByDay = $this->model->getMechanicJobsByDay($mechanicId, $startDate, $endDate);

        $this->view('Manager/Team Performance/individual', [
            'pageTitle' => 'Mechanic Performance',
            'mechanic' => $mechanic,
            'stats' => $stats,
            'jobsByDay' => $jobsByDay,
            'month' => $month
        ]);
    }
}
