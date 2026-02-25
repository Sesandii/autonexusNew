<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\WorkOrder;
use app\model\supervisor\Checklist;
use app\model\supervisor\Appointment;

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

    $currentSupervisorId = $_SESSION['user']['user_id'] ?? null;

    $m = new WorkOrder();

    // ✅ Get ALL work orders (no filtering by supervisor)
    $workOrders = $m->getAll();

    $data = [
        'workOrders'            => $workOrders,
        'currentSupervisorId'   => $currentSupervisorId, // pass to view
        'availableAppointments' => $m->getAvailableAppointments(),
        'activeMechanics'       => $m->getActiveMechanics()
    ];

    $this->view('supervisor/workorders/index', $data);
}

public function createForm()
{
    $m = new WorkOrder();
    $checklistModel = new Checklist();

    // Appointment coming from dashboard
    $appointmentId = $_GET['appointment_id'] ?? null;

    if ($appointmentId) {
        // Fetch only the selected appointment
        $appointment = $m->getAppointmentById($appointmentId);

        if (!$appointment) {
            // Safety fallback if invalid appointment
            header("Location: " . BASE_URL . "/supervisor/dashboard");
            exit;
        }

        $availableAppointments = [$appointment];

        // Load checklist template for this service only
        $serviceId = (int) $appointment['service_id'];
        $allTemplates = [
            $serviceId => $checklistModel->createFromServiceTemplateArray($serviceId)
        ];
    } else {
        // Existing behavior (all available appointments)
        $availableAppointments = $m->getAvailableAppointments();
        $allTemplates = [];

        foreach ($availableAppointments as $appt) {
            $serviceId = (int)($appt['service_id'] ?? 0);
            if ($serviceId > 0 && !isset($allTemplates[$serviceId])) {
                $allTemplates[$serviceId] =
                    $checklistModel->createFromServiceTemplateArray($serviceId);
            }
        }
    }

    // Mechanics
    $activeMechanics = $m->getActiveMechanics();

    // ✅ Count active work orders per mechanic_code
    $mechanicLimits = [];
    foreach ($activeMechanics as $mech) {
        $mechanicCode = $mech['mechanic_code'] ?? null;
        if ($mechanicCode) {
            $mechanicLimits[$mechanicCode] = $m->countActiveByMechanicCode($mechanicCode);
        }
    }

    // Pre-selected mechanic (from coordination page)
    $selectedMechanicId   = $_GET['mechanic_id'] ?? null;
    $selectedMechanicSpec = $_GET['mechanic_spec'] ?? null;

    // ✅ Flash message
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $message = $_SESSION['message'] ?? null;
    unset($_SESSION['message']);

    $data = [
        'availableAppointments' => $availableAppointments,
        'activeMechanics'       => $activeMechanics,
        'mechanicLimits'        => $mechanicLimits,  // <-- new: pass counts to view
        'selectedMechanicId'    => $selectedMechanicId,
        'selectedMechanicSpec'  => $selectedMechanicSpec,
        'allTemplates'          => $allTemplates,
        'selectedAppointmentId' => $appointmentId, // Use in view for read-only display
        'message'               => $message,       
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

    // ✅ Validate mechanic selection
    if (!$mechanic_id) {
        $this->flash('danger', 'Please select a mechanic.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders/create');
        exit;
    }

    // 🔹 Mechanic limit check (max 5 active work orders per mechanic_code)
    $mechanic = $m->getMechanicById($mechanic_id);
    $mechanicCode = $mechanic['mechanic_code'] ?? null;

    if ($mechanicCode) {
        $activeCount = $m->countActiveByMechanicCode($mechanicCode);
        if ($activeCount >= 5) {
            $this->flash('danger', "This mechanic ({$mechanicCode}) already has 5 active work orders.");
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders/create');
            exit;
        }
    }

    if ($m->getAppointmentExists($appointment_id)) {
        $this->flash('danger', 'This appointment already has a work order.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
        exit;
    }

    // 🔹 CREATE WORK ORDER
   // 🔹 Determine correct status (only 1 in_progress allowed)
//$hasActive = $m->hasActiveInProgressJob($mechanic_id);
//$status = $hasActive ? 'open' : 'in_progress';
$status = $_POST['status'] ?? 'open';
// 🔹 CREATE WORK ORDER
$workOrderId = $m->create([
    'appointment_id'  => $appointment_id,
    'mechanic_id'     => $mechanic_id,
    'service_summary' => trim($_POST['service_summary'] ?? ''),
    'total_cost'      => 0,
    'status'          => $status,
    'supervisor_id'   => $supervisor_id,
]);


    // After creating the work order in WorkOrdersController->store()
if ($mechanic_id) {
    $mechanic = $m->getMechanicById($mechanic_id);
    $mechanicCode = $mechanic['mechanic_code'] ?? null;

    if ($mechanicCode) {
        $m->updateMechanicStatus($mechanicCode);
    }
}


    // 🔹 CREATE CHECKLIST (existing logic remains)
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

    $this->flash('success', 'Work order and checklist created.');
    header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
    exit;
}

    public function show($id)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $supervisor_id = $_SESSION['user']['user_id'] ?? null;

        $m = new WorkOrder();
        $wo = $m->find((int)$id);

        if (!$wo) {
            $this->flash('danger', 'Work order not found.');
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
        $templateItems = $checklistModel->getTemplateByService($serviceId);
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

    // Mechanics
    $activeMechanics = $m->getActiveMechanics();

    // ✅ Count active work orders per mechanic_code
    $mechanicLimits = [];
    foreach ($activeMechanics as $mech) {
        $mechanicCode = $mech['mechanic_code'] ?? null;
        if ($mechanicCode) {
            // Exclude current work order
            $mechanicLimits[$mechanicCode] = $m->countActiveByMechanicCode($mechanicCode, $wo['work_order_id']);
        }
    }

    $data = [
        'wo'                    => $wo,
        'availableAppointments' => $m->getAvailableAppointments(),
        'activeMechanics'       => $activeMechanics,
        'mechanicLimits'        => $mechanicLimits,  // <-- new
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
    
        // 🔹 Get the mechanic ID from form and validate
        $newMechanicId = !empty($_POST['mechanic_id']) ? (int)$_POST['mechanic_id'] : null;
        if (!$newMechanicId) {
            $this->flash('danger', 'Please select a mechanic.');
            header('Location: ' . rtrim(BASE_URL,'/') . "/supervisor/workorders/{$id}/edit");
            exit;
        }
    
        // 🔹 Mechanic limit check (max 5 active work orders per mechanic_code)
        if ($newMechanicId != $wo['mechanic_id']) { // only if mechanic is changed
            $mechanic = $m->getMechanicById($newMechanicId);
            $mechanicCode = $mechanic['mechanic_code'] ?? null;
    
            if ($mechanicCode) {
                // Exclude the current work order ID from the count
                $activeCount = $m->countActiveByMechanicCode($mechanicCode, $id);
                if ($activeCount >= 5) {
                    $this->flash('danger', "This mechanic ({$mechanicCode}) already has 5 active work orders.");
                    header('Location: ' . rtrim(BASE_URL,'/') . "/supervisor/workorders/{$id}/edit");
                    exit;
                }
            }
        }

        // After checking the mechanic limit and before updating
if ($newMechanicId) {
    $newMechanic = $m->getMechanicById($newMechanicId);
    $newMechanicCode = $newMechanic['mechanic_code'] ?? null;

    if ($newMechanicCode) {
        $m->updateMechanicStatus($newMechanicCode);
    }
}

// Optional: Also update old mechanic if reassigned
$oldMechanicId = $wo['mechanic_id'] ?? null;
if ($oldMechanicId && $oldMechanicId != $newMechanicId) {
    $oldMechanic = $m->getMechanicById($oldMechanicId);
    $oldMechanicCode = $oldMechanic['mechanic_code'] ?? null;
    if ($oldMechanicCode) {
        $m->updateMechanicStatus($oldMechanicCode);
    }
}

    
        // 🔹 Update work order
        $payload = [
            'appointment_id'  => (int)($_POST['appointment_id'] ?? 0),
            'mechanic_id'     => $newMechanicId,
            'service_summary' => trim($_POST['service_summary'] ?? ''),
            'total_cost'      => (float)($_POST['total_cost'] ?? 0),
        ];
        $m->update((int)$id, $payload, $supervisor_id);
    
        // 🔹 Update status
        // 🔹 Update status with timer logic
$newStatus = $_POST['status'] ?? 'open';
$currentStatus = strtolower($wo['status'] ?? '');

if ($newStatus !== $currentStatus) {

    // ===============================
    // WHEN PAUSING (in_progress → on_hold)
    // ===============================
    if ($newStatus === 'on_hold' && $currentStatus === 'in_progress') {

        if (!empty($wo['job_start_time'])) {

            $start = new \DateTime($wo['job_start_time']);
            $now   = new \DateTime();

            $elapsed = $now->getTimestamp() - $start->getTimestamp();
            $total   = ((int)$wo['base_duration_minutes']) * 60;

            $remaining = max(0, $total - $elapsed);

            $m->savePausedTime((int)$id, $remaining);
        }
    }

    // ===============================
    // WHEN RESUMING (on_hold → in_progress)
    // ===============================
    if ($newStatus === 'in_progress' && $wo['status'] === 'on_hold') {
        $paused = (int)($wo['paused_remaining_seconds'] ?? 0);
    
        // Fetch service duration
        $stmt = db()->prepare("
            SELECT s.base_duration_minutes
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN services s ON a.service_id = s.service_id
            WHERE w.work_order_id = ?
        ");
        $stmt->execute([$id]);
        $durationMinutes = (int)$stmt->fetchColumn();
    
        if ($paused > 0) {
            $stmt = db()->prepare("
                UPDATE work_orders
                SET job_start_time = DATE_SUB(NOW(), INTERVAL (?*60 - ?) SECOND),
                    paused_remaining_seconds = NULL
                WHERE work_order_id = ?
            ");
            $stmt->execute([$durationMinutes, $paused, $id]);
        } else {
            $stmt = db()->prepare("
                UPDATE work_orders
                SET paused_remaining_seconds = NULL
                WHERE work_order_id = ?
            ");
            $stmt->execute([$id]);
        }
    }

    // Finally update status
    $m->setStatusFromActor((int)$id, $newStatus, $supervisor_id);
}

        // 🔹 SYNC CHECKLIST (ADD / REMOVE / EDIT)
        $checklistModel = new \app\model\supervisor\Checklist();
        $checklistModel->deleteByWorkOrder((int)$id);
    
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
    
        $this->flash('success', 'Work order updated.');
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

    // ✅ Reset appointment status
    if (!empty($wo['appointment_id'])) {
        $m->setAppointmentStatus((int)$wo['appointment_id'], 'requested');
    }

    // ✅ Delete work order
    $m->delete((int)$id, $supervisor_id);

    // ✅ Update mechanic status AFTER deletion
    if (!empty($wo['mechanic_id'])) {
        $mechanic = $m->getMechanicById((int)$wo['mechanic_id']);
        if ($mechanic) {
            $m->updateMechanicStatus($mechanic['mechanic_code']);
        }
    }

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
