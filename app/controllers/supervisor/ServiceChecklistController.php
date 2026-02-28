<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\ServiceChecklistTemplate;
use app\model\supervisor\Service;

class ServiceChecklistController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireSupervisor();
    }

    /**
     * Show checklist management page
     */
    public function index()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $service_id = (int)($_GET['service_id'] ?? 0);

        $serviceModel   = new Service();
        $checklistModel = new ServiceChecklistTemplate();

        $data = [
            'services'  => $serviceModel->getAll(),
            'serviceId' => $service_id,
            'steps'     => $service_id ? $checklistModel->getByService($service_id) : []
        ];

        $this->view('supervisor/checklist/index', $data);
    }

    /**
     * Add checklist step
     */
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') return;

        $service_id = (int)$_POST['service_id'];
        $step_name  = trim($_POST['step_name']);

        if (!$service_id || $step_name === '') {
            $_SESSION['message'] = 'Invalid input';
            header('Location: ' . BASE_URL . '/supervisor/checklist');
            exit;
        }

        $model = new ServiceChecklistTemplate();
        $model->create($service_id, $step_name);

        header('Location: ' . BASE_URL . '/supervisor/checklist?service_id=' . $service_id);
        exit;
    }

    /**
     * Delete checklist step
     */
    public function delete($id)
    {
        $model = new ServiceChecklistTemplate();
        $model->delete((int)$id);

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    /**
     * Guard
     */
    private function requireSupervisor(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $u = $_SESSION['user'] ?? null;
        if (!$u || $u['role'] !== 'supervisor') {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }
}
