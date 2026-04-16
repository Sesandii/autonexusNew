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
    $branchId = $_SESSION['user']['branch_id'] ?? null;

    if (!$branchId) {
        die("Error: No branch assigned to this user.");
    }

    $m = new WorkOrder();

    $workOrders = $m->getAll((int)$branchId);

    $data = [
        'workOrders'            => $workOrders,
        'currentSupervisorId'   => $currentSupervisorId,
        'availableAppointments' => $m->getAvailableAppointments($currentSupervisorId),
        'activeMechanics'       => $m->getActiveMechanicsByBranch($branchId)
    ];

    $this->view('supervisor/workorders/index', $data);
}

    public function createForm()
    {
        $m = new WorkOrder();
        $checklistModel = new Checklist();
        
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        $branchId = $_SESSION['user']['branch_id'] ?? null;
    
        $appointmentId = $_GET['appointment_id'] ?? null;
    
        if ($appointmentId) {
            $appointment = $m->getAppointmentById($appointmentId);
            if (!$appointment) {
                header("Location: " . BASE_URL . "/supervisor/dashboard");
                exit;
            }
            $availableAppointments = [$appointment];
            $serviceId = (int) $appointment['service_id'];
            $allTemplates = [
                $serviceId => $checklistModel->createFromServiceTemplateArray($serviceId)
            ];
        } else {
            $supervisorId = $_SESSION['user']['user_id'] ?? null;
            $availableAppointments = $m->getAvailableAppointments($supervisorId);
            $allTemplates = [];
    
            foreach ($availableAppointments as $appt) {
                $serviceId = (int)($appt['service_id'] ?? 0);
                if ($serviceId > 0 && !isset($allTemplates[$serviceId])) {
                    $allTemplates[$serviceId] = $checklistModel->createFromServiceTemplateArray($serviceId);
                }
            }
        }

        $activeMechanics = $m->getActiveMechanicsByBranch($branchId);
    
        $mechanicLimits = [];
        foreach ($activeMechanics as $mech) {
            $mechanicCode = $mech['mechanic_code'] ?? null;
            if ($mechanicCode) {
                $mechanicLimits[$mechanicCode] = $m->countActiveByMechanicCode($mechanicCode);
            }
        }
    
        $selectedMechanicId   = $_GET['mechanic_id'] ?? null;
        $selectedMechanicSpec = $_GET['mechanic_spec'] ?? null;
    
        $message = $_SESSION['message'] ?? null;
        unset($_SESSION['message']);
    
        $data = [
            'availableAppointments' => $availableAppointments,
            'activeMechanics'       => $activeMechanics,
            'mechanicLimits'        => $mechanicLimits,
            'selectedMechanicId'    => $selectedMechanicId,
            'selectedMechanicSpec'  => $selectedMechanicSpec,
            'allTemplates'          => $allTemplates,
            'selectedAppointmentId' => $appointmentId,
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

    if (!$mechanic_id) {
        $this->flash('danger', 'Please select a mechanic.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders/create');
        exit;
    }

    $mechanic = $m->getMechanicById($mechanic_id);
    $mechanicCode = $mechanic['mechanic_code'] ?? null;

    if ($mechanicCode) {
        $activeCount = $m->countActiveByMechanicCode($mechanicCode);
        if ($activeCount >= 5) {
            $this->flash('danger', "This mechanic ({$mechanicCode}) already has 5 active work orders.");
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
            exit;
        }
    }

    if ($m->getAppointmentExists($appointment_id)) {
        $this->flash('danger', 'This appointment already has a work order.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
        exit;
    }

$status = $_POST['status'] ?? 'open';

if ($mechanic_id && ($status === 'in_progress')) {
        
    if ($m->hasActiveJobInRestrictedStatuss($mechanic_id)) {
        $this->flash('danger', "This mechanic already has a job 'In Progress'. Please pause or complete it first.");
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
        exit;
    }
}
$workOrderId = $m->create([
    'appointment_id'  => $appointment_id,
    'mechanic_id'     => $mechanic_id,
    'service_summary' => trim($_POST['service_summary'] ?? ''),
    'total_cost'      => 0,
    'status'          => $status,
    'supervisor_id'   => $supervisor_id,
]);


if ($mechanic_id) {
    $mechanic = $m->getMechanicById($mechanic_id);
    $mechanicCode = $mechanic['mechanic_code'] ?? null;

    if ($mechanicCode) {
        $m->updateMechanicStatus($mechanicCode);
    }
}

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

    $apptData = $m->getAppointmentById($appointment_id);
$vehicleId = $apptData['vehicle_id'] ?? null;

$appt = $m->getAppointmentById($appointment_id);
if ($appt && !empty($appt['vehicle_id'])) {
    $vStatus = ($status === 'completed') ? 'available' : 'in_service';
    $m->updateVehicleStatus((int)$appt['vehicle_id'], $vStatus);
}

    $this->flash('success', 'Work order created.');
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

    $branchId = $_SESSION['user']['branch_id'] ?? null;

    $m = new WorkOrder();
    $wo = $m->find((int)$id);

    if (!$wo || ($wo['supervisor_id'] ?? 0) !== $supervisor_id) {
        $this->flash('danger', 'Work order unauthorized.');
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); 
        exit;
    }

    $checklistModel = new \app\model\supervisor\Checklist();

    $checklist = $checklistModel->getByWorkOrder((int)$id);

    $serviceId = $wo['service_id'] ?? null;
    $templateItems = [];
    if ($serviceId) {
        $templateItems = $checklistModel->getTemplateByService($serviceId);
    }

    $finalChecklist = [];
    $existingNames = array_column($checklist, 'item_name');

    foreach ($templateItems as $t) {
        if (!in_array($t['step_name'], $existingNames)) {
            $finalChecklist[] = ['item_name' => $t['step_name']];
        }
    }
    $finalChecklist = array_merge($checklist, $finalChecklist);

    $availableAppointments = $m->getAvailableAppointments($supervisor_id);

    $activeMechanics = $m->getActiveMechanicsByBranch($branchId);

    $mechanicLimits = [];
    foreach ($activeMechanics as $mech) {
        $mechanicCode = $mech['mechanic_code'] ?? null;
        if ($mechanicCode) {
            $mechanicLimits[$mechanicCode] = 
                $m->countActiveByMechanicCode($mechanicCode, $wo['work_order_id']);
        }
    }

    $data = [
        'wo'                    => $wo,
        'availableAppointments' => $availableAppointments,
        'activeMechanics'       => $activeMechanics, 
        'mechanicLimits'        => $mechanicLimits,
        'checklist'             => $finalChecklist,
        'selectedMechanicId'    => $wo['mechanic_id']
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
    
        $newMechanicId = !empty($_POST['mechanic_id']) ? (int)$_POST['mechanic_id'] : null;
        if (!$newMechanicId) {
            $this->flash('danger', 'Please select a mechanic.');
            header('Location: ' . rtrim(BASE_URL,'/') . "/supervisor/workorders/{$id}/edit");
            exit;
        }
    
        $newStatus = $_POST['status'] ?? 'open';
    $currentStatus = strtolower($wo['status'] ?? '');

    $isActiveState = ($newStatus === 'in_progress');
    $mechanicChanged = ($newMechanicId != $wo['mechanic_id']);

    if ($isActiveState || ($mechanicChanged && $isActiveState)) {
        
        if ($m->hasActiveJobInRestrictedStatus($newMechanicId, (int)$id)) {
            $this->flash('danger', "This mechanic already has a job 'In Progress' or 'On Hold'. You cannot assign them another active task.");
            header('Location: ' . rtrim(BASE_URL,'/') . "/supervisor/workorders");
            exit;
        }
    }

    if ($mechanicChanged) {
        $mechanic = $m->getMechanicById($newMechanicId);
        $mechanicCode = $mechanic['mechanic_code'] ?? null;
        if ($mechanicCode) {
            $activeCount = $m->countActiveByMechanicCode($mechanicCode, $id);
            if ($activeCount >= 5) {
                $this->flash('danger', "This mechanic ({$mechanicCode}) already has 5 total work orders.");
                header('Location: ' . rtrim(BASE_URL,'/') . "/supervisor/workorders/{$id}/edit");
                exit;
            }
        }
    }

if ($newMechanicId) {
    $newMechanic = $m->getMechanicById($newMechanicId);
    $newMechanicCode = $newMechanic['mechanic_code'] ?? null;

    if ($newMechanicCode) {
        $m->updateMechanicStatus($newMechanicCode);
    }
}

$oldMechanicId = $wo['mechanic_id'] ?? null;
if ($oldMechanicId && $oldMechanicId != $newMechanicId) {
    $oldMechanic = $m->getMechanicById($oldMechanicId);
    $oldMechanicCode = $oldMechanic['mechanic_code'] ?? null;
    if ($oldMechanicCode) {
        $m->updateMechanicStatus($oldMechanicCode);
    }
}

    
        $payload = [
            'appointment_id'  => (int)($_POST['appointment_id'] ?? 0),
            'mechanic_id'     => $newMechanicId,
            'service_summary' => trim($_POST['service_summary'] ?? ''),
            'total_cost'      => (float)($_POST['total_cost'] ?? 0),
        ];
        $m->update((int)$id, $payload, $supervisor_id);
    
$newStatus = $_POST['status'] ?? 'open';
$currentStatus = strtolower($wo['status'] ?? '');


if (($newStatus === 'in_progress') && $currentStatus !== $newStatus) {
    if ($m->hasActiveInProgressJob($newMechanicId, (int)$id)) {
        $this->flash('danger', "This mechanic already has a job 'In Progress'. Please pause or complete it first.");
        header('Location: ' . rtrim(BASE_URL,'/') . "/supervisor/workorders");
        exit;
    }
}



if ($newStatus !== $currentStatus) {

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

    if ($newStatus === 'in_progress' && $wo['status'] === 'on_hold') {
        $paused = (int)($wo['paused_remaining_seconds'] ?? 0);

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

    $m->setStatusFromActor((int)$id, $newStatus, $supervisor_id);
}

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

$vehicleId = $m->getVehicleIdByWorkOrder((int)$id);
if ($vehicleId) {
    $vStatus = ($newStatus === 'completed') ? 'available' : 'in_service';
    $m->updateVehicleStatus($vehicleId, $vStatus);
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

    if (!empty($wo['appointment_id'])) {
        $m->setAppointmentStatus((int)$wo['appointment_id'], 'confirmed');
    }

    $m->delete((int)$id, $supervisor_id);

    if (!empty($wo['mechanic_id'])) {
        $mechanic = $m->getMechanicById((int)$wo['mechanic_id']);
        if ($mechanic) {
            $m->updateMechanicStatus($mechanic['mechanic_code']);
        }
    }


$vehicleId = $m->getVehicleIdByWorkOrder((int)$id);

if ($vehicleId) {
    $m->updateVehicleStatus($vehicleId, 'available');
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


public function updateVehicleStatus($vehicleId, $status)
{
    $stmt = db()->prepare("UPDATE vehicles SET status = ? WHERE vehicle_id = ?");
    return $stmt->execute([$status, $vehicleId]);
}

}
