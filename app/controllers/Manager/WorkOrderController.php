<?php

namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\WorkOrderModel;


class WorkOrderController extends Controller
{
    private WorkOrderModel $workOrderModel;

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

    public function __construct(array $config = [])
    {
        parent::__construct($config);
          $this->guardManager();
        $this->workOrderModel = new WorkOrderModel();
    }

    /**
     * List all work orders
     * Route: GET /work-orders
     */
    public function index(): void
    {
        // Optional: Require login based on your auth needs
        // $this->requireLogin();
        
        $workOrders = $this->workOrderModel->getAllWorkOrders();
        
        $this->view('manager/WorkOrder/workOrder', [
            'workOrders' => $workOrders,
            'title' => 'Work Orders',
            'base' => $this->baseUrl()
        ]);
    }

    /**
     * Show single work order details
     * Route: GET /work-orders/{id}
     */
    public function show(int $id): void
    {
        $workOrder = $this->workOrderModel->getWorkOrderById($id);
        
        if (!$workOrder) {
            $_SESSION['flash'] = 'Work order not found.';
            $this->redirect($this->baseUrl() . '/work-orders');
            return;
        }
        
        $this->view('work-orders/show', [
            'workOrder' => $workOrder,
            'title' => 'Work Order #' . $id,
            'base' => $this->baseUrl()
        ]);
    }

    /**
     * Filter work orders by status
     * Route: GET /work-orders/status/{status}
     */
    public function byStatus(string $status): void
    {
        $workOrders = $this->workOrderModel->getWorkOrdersByStatus($status);
        
        $this->view('work-orders/index', [
            'workOrders' => $workOrders,
            'title' => 'Work Orders - ' . ucfirst($status),
            'currentStatus' => $status,
            'base' => $this->baseUrl()
        ]);
    }

    /**
 * Show work order detail page
 * Route: GET /manager/work-orders/detail/{id}
 */
public function detail(int $id): void
{
    $data = $this->workOrderModel->getWorkOrderDetail($id);

    if (!$data) {
        $_SESSION['flash'] = 'Work order not found.';
        header('Location: ' . BASE_URL . '/manager/work-orders');
        exit;
    }

    $this->view('manager/WorkOrder/workOrderDetail', [
        'pageTitle'  => 'Work Order #' . $id,
        'workOrder'  => $data['work_order'],
        'appointment' => $data['appointment'],
        'customer'   => $data['customer'],
        'vehicle'    => $data['vehicle'],
        'service'    => $data['service'],
        'mechanic'   => $data['mechanic'],
        'supervisor' => $data['supervisor'],
        'complaints' => $data['complaints'],
        'report'     => $data['report'],
        'activePage' => 'workOrders',
        'base'       => $this->baseUrl()
    ]);
}
}