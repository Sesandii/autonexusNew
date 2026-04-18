<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\IndividualPerformanceModel;

class IndividualPerformanceController extends Controller
{
    protected IndividualPerformanceModel $model;

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

        $this->model = new IndividualPerformanceModel(db());
    }

 public function index()
{
    $mechanic_id = $_GET['mechanic_id'] ?? null;

    if (!$mechanic_id) {
        die('Mechanic ID not provided');
    }

    $mechanicModel = new \app\models\IndividualPerformanceModel();

    $mechanic = $mechanicModel->getById($mechanic_id);
    $workOrders = $mechanicModel->getWorkOrders($mechanic_id);
    $feedback = $mechanicModel->getFeedback($mechanic_id);

    $this->view('manager/individual-performance', [
        'mechanic' => $mechanic,
        'workOrders' => $workOrders,
        'feedback' => $feedback
    ]);
}

}
