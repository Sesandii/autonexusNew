<?php

namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\WorkOrderModel;


class WorkOrderController extends BaseManagerController
{
    private WorkOrderModel $workOrderModel;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->workOrderModel = new WorkOrderModel();
    }

    /**
     * List all work orders
     * Route: GET /work-orders
     */
   public function index(): void
{
    $workOrders = $this->workOrderModel->getAllWorkOrders();

    $statusFilter = $_GET['status'] ?? '';
    $dateFilter   = $_GET['date']   ?? 'all';
    $activeTab    = $_GET['tab']    ?? 'all-orders';

    // Filter by date helper
    $filterByDate = function(array $orders, string $dateField = 'appointment_date') use ($dateFilter): array {
        return array_values(array_filter($orders, function($wo) use ($dateFilter, $dateField) {
            $raw  = $wo[$dateField] ?? null;
            if (!$raw) return $dateFilter === 'all';
            $date  = date('Y-m-d', strtotime($raw));
            $today = date('Y-m-d');
            return match($dateFilter) {
                'today' => $date === $today,
                'week'  => $date >= date('Y-m-d', strtotime('monday this week'))
                        && $date <= date('Y-m-d', strtotime('sunday this week')),
                'month' => date('Y-m', strtotime($date)) === date('Y-m'),
                default => true
            };
        }));
    };

    // All orders — status + date
    $allFiltered = !empty($statusFilter)
        ? array_filter($workOrders, fn($wo) => strtolower($wo['work_order_status'] ?? '') === $statusFilter)
        : $workOrders;
    $allFiltered = $filterByDate(array_values($allFiltered));

    // In Progress — date only
    $inProgressOrders = $filterByDate(array_values(
        array_filter($workOrders, fn($wo) => strtolower($wo['work_order_status'] ?? '') === 'in_progress')
    ));

    // Completed — date only
    $completedOrders = $filterByDate(array_values(
        array_filter($workOrders, fn($wo) => strtolower($wo['work_order_status'] ?? '') === 'completed')
    ));

    $this->view('manager/WorkOrder/workOrder', [
        'workOrders'       => $workOrders,
        'allFiltered'      => $allFiltered,
        'inProgressOrders' => $inProgressOrders,
        'completedOrders'  => $completedOrders,
        'statusFilter'     => $statusFilter,
        'dateFilter'       => $dateFilter,
        'activeTab'        => $activeTab,
        'title'            => 'Work Orders',
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