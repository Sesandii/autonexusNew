<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\WorkOrder;
use app\model\supervisor\Checklist;

class WorkOrdersController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireSupervisor();
    }

    public function index()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $supervisor_id = $_SESSION['user']['user_id'] ?? null;

        $m = new WorkOrder();
        $workOrders = $m->getAll($supervisor_id);

        $data = [
            'workOrders'            => $workOrders,
            'availableAppointments' => $m->getAvailableAppointments(),
            'activeMechanics'       => $m->getActiveMechanics()
        ];

        $this->view('supervisor/workorders/index', $data);
    }

    public function createForm()
    {
        $m = new WorkOrder();
        $availableAppointments = $m->getAvailableAppointments();
        $activeMechanics = $m->getActiveMechanics();
    
        // Load checklist templates for all services of available appointments
        $checklistModel = new Checklist();
        $allTemplates = [];
    
        foreach ($availableAppointments as $appt) {
            $serviceId = (int)($appt['service_id'] ?? 0);
            if ($serviceId > 0 && !isset($allTemplates[$serviceId])) {
                $allTemplates[$serviceId] = $checklistModel->createFromServiceTemplateArray($serviceId);
            }
        }
    
        $data = [
            'availableAppointments' => $availableAppointments,
            'activeMechanics'       => $activeMechanics,
            'allTemplates'          => $allTemplates
        ];
    
        $this->view('supervisor/workorders/create', $data);
    }
    
    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders/create'); 
            exit;
        }

        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $supervisor_id = $_SESSION['user']['user_id'] ?? null;

        $m = new WorkOrder();

        $appointment_id = (int)($_POST['appointment_id'] ?? 0);
        $mechanic_id = !empty($_POST['mechanic_id']) ? (int)$_POST['mechanic_id'] : null;

        if ($m->getAppointmentExists($appointment_id)) {
            $this->flash('danger', '⚠️ This appointment already has a work order.');
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
            exit;
        }

        /** 1️⃣ CREATE WORK ORDER (SYNC APPOINTMENT STATUS AUTOMATICALLY) */
$workOrderId = $m->create([
    'appointment_id'  => $appointment_id,
    'mechanic_id'     => $mechanic_id,
    'service_summary' => trim($_POST['service_summary'] ?? ''),
    'total_cost'      => 0,
    'status'          => $_POST['status'] ?? 'open',
    'supervisor_id'   => $supervisor_id,
]);


        /** 2️⃣ CREATE CHECKLIST FROM SERVICE TEMPLATE */
        $serviceId = $m->getServiceIdByAppointment($appointment_id);

        if ($serviceId) {
            $checklistModel = new Checklist();
            $templateItems = $checklistModel->getTemplateByService($serviceId);

            foreach ($templateItems as $item) {
                $checklistModel->create([
                    'work_order_id' => $workOrderId,
                    'item_name'     => $item['step_name'],
                    'status'        => 'pending'
                ]);
            }
        }

        $this->flash('success', '✅ Work order and checklist created.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
        exit;
    }

    public function show($id)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $supervisor_id = $_SESSION['user']['user_id'] ?? null;

        $m = new WorkOrder();
        $wo = $m->find((int)$id);

        if (!$wo || ($wo['supervisor_id'] ?? 0) !== $supervisor_id) {
            $this->flash('danger', 'Work order not found or unauthorized.');
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
            exit;
        }

        $this->view('supervisor/workorders/show', ['wo' => $wo]);
    }

    public function editForm($id)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $supervisor_id = $_SESSION['user']['user_id'] ?? null;
    
        $m = new WorkOrder();
        $wo = $m->find((int)$id);
    
        if (!$wo || ($wo['supervisor_id'] ?? 0) !== $supervisor_id) {
            $this->flash('danger', 'Work order not found or unauthorized.');
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
            exit;
        }
    
        $checklistModel = new \app\model\supervisor\Checklist();
        
        // Get existing checklist items for the work order
        $checklist = $checklistModel->getByWorkOrder((int)$id);
    
        // Get service template items for this work order's service
        $serviceId = $wo['service_id'] ?? null;
        $templateItems = [];
        if ($serviceId) {
            $templateItems = $checklistModel->getTemplateByService($serviceId); // returns array of step_name
        }
    
        // Merge template with existing checklist items
        $finalChecklist = [];
        $existingNames = array_column($checklist, 'item_name');
    
        foreach ($templateItems as $t) {
            if (!in_array($t['step_name'], $existingNames)) {
                $finalChecklist[] = ['item_name' => $t['step_name']];
            }
        }
    
        // Combine existing + template-only items
        $finalChecklist = array_merge($checklist, $finalChecklist);
    
        $data = [
            'wo'                    => $wo,
            'availableAppointments' => $m->getAvailableAppointments(),
            'activeMechanics'       => $m->getActiveMechanics(),
            'checklist'             => $finalChecklist
        ];
    
        $this->view('supervisor/workorders/edit', $data);
    }
    

    public function update($id)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
        exit;
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $supervisor_id = $_SESSION['user']['user_id'] ?? null;

    $m = new WorkOrder();
    $wo = $m->find((int)$id);

    if (!$wo || ($wo['supervisor_id'] ?? 0) !== $supervisor_id) {
        $this->flash('danger', 'Unauthorized update.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
        exit;
    }

    // 🔹 Update work order
    $payload = [
        'appointment_id'  => (int)($_POST['appointment_id'] ?? 0),
        'mechanic_id'     => ($_POST['mechanic_id'] ?? '') !== '' ? (int)$_POST['mechanic_id'] : null,
        'service_summary' => trim($_POST['service_summary'] ?? ''),
        'total_cost'      => (float)($_POST['total_cost'] ?? 0),
    ];
    $m->update((int)$id, $payload, $supervisor_id);

    // 🔹 Update status
    $newStatus = $_POST['status'] ?? 'open';
    $m->setStatusFromActor((int)$id, $newStatus, $supervisor_id);

    // 🔹 SYNC CHECKLIST (ADD / REMOVE / EDIT)
    $checklistModel = new \app\model\supervisor\Checklist();

    // Always clear old checklist
    $checklistModel->deleteByWorkOrder((int)$id);

    // Re-insert updated checklist
    if (!empty($_POST['checklist']) && is_array($_POST['checklist'])) {
        foreach ($_POST['checklist'] as $itemName) {
            $itemName = trim($itemName);
            if ($itemName === '') continue;

            $checklistModel->create([
                'work_order_id' => (int)$id,
                'item_name'     => $itemName,
                'status'        => 'pending'
            ]);
        }
    }

    $this->flash('success', 'Work order and checklist updated.');
    header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
    exit;
}

    private function requireSupervisor(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;

        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }

    public function destroy($id)
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $supervisor_id = $_SESSION['user']['user_id'] ?? null;

    $m = new WorkOrder();
    $wo = $m->find((int)$id);

    if (!$wo || ($wo['supervisor_id'] ?? 0) !== $supervisor_id) {
        $this->flash('danger', 'Unauthorized delete.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
        exit;
    }

    $m->delete((int)$id, $supervisor_id);

    $this->flash('success', 'Work order deleted.');
    header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
    exit;
}

private function flash(string $type, string $text): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['message'] = [
        'type' => $type,
        'text' => $text
    ];
}


}
