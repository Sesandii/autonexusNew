<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\WorkOrder;

class WorkOrdersController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }
    public function index()
    {
        $m = new WorkOrder();
        $data = [
            'workOrders'            => $m->getAll(),
            'availableAppointments' => $m->getAvailableAppointments(),
            'activeMechanics'       => $m->getActiveMechanics(),
            'message'               => $_SESSION['message'] ?? null,
        ];
        unset($_SESSION['message']);

        $this->view('supervisor/workorders/index', $data);
    }

    /** GET /supervisor/workorders/create */
    public function createForm()
    {
        $m = new WorkOrder();
        $data = [
            'availableAppointments' => $m->getAvailableAppointments(),
            'activeMechanics'       => $m->getActiveMechanics(),
        ];
        $this->view('supervisor/workorders/create', $data);
    }

    /** POST /supervisor/workorders */
    public function store()
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders/create'); exit;
        }

        $m = new WorkOrder();
        $payload = [
            'appointment_id'  => (int)($_POST['appointment_id'] ?? 0),
            'mechanic_id'     => ($_POST['mechanic_id'] ?? '') !== '' ? (int)$_POST['mechanic_id'] : null,
            'service_summary' => trim($_POST['service_summary'] ?? ''),
            'total_cost'      => (float)($_POST['total_cost'] ?? 0),
            'status'          => $_POST['status'] ?? 'open',
        ];

        if ($m->getAppointmentExists($payload['appointment_id'])) {
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders');
            $_SESSION['message'] = ['type' => 'error', 'text' => '⚠️ This appointment already has a work order.'];
            exit;
        }

        $m->create($payload);
        $_SESSION['message'] = ['type' => 'success', 'text' => '✅ Work order created.'];
        // go to supervisor index (list)
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); exit;
    }

    /** GET /supervisor/workorders/{id} */
    public function show($id)
    {
        $m = new WorkOrder();
        $wo = $m->find((int)$id);
        if (!$wo) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Work order not found.'];
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); exit;
        }
        $this->view('supervisor/workorders/show', ['wo' => $wo]);
    }

    /** GET /supervisor/workorders/{id}/edit */
    public function editForm($id)
    {
        $m = new WorkOrder();
        $wo = $m->find((int)$id);
        if (!$wo) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Work order not found.'];
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); exit;
        }

        $data = [
            'wo'                   => $wo,
            'availableAppointments'=> $m->getAvailableAppointments(), // needed to change appointment
            'activeMechanics'      => $m->getActiveMechanics(),
        ];
        $this->view('supervisor/workorders/edit', $data);
    }

    /** POST /supervisor/workorders/{id} */
    public function update($id)
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); exit;
        }

        $m = new WorkOrder();

        $payload = [
            'appointment_id'  => (int)($_POST['appointment_id'] ?? 0), // allow changing appointment
            'mechanic_id'     => ($_POST['mechanic_id'] ?? '') !== '' ? (int)$_POST['mechanic_id'] : null,
            'service_summary' => trim($_POST['service_summary'] ?? ''),
            'total_cost'      => (float)($_POST['total_cost'] ?? 0),
            'status'          => $_POST['status'] ?? 'open',
        ];

        $m->update((int)$id, $payload);

        $_SESSION['message'] = ['type' => 'success', 'text' => 'Work order updated.'];
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); exit;
    }

    /** POST /supervisor/workorders/{id}/delete */
    public function destroy($id)
    {
        $m = new WorkOrder();
        $m->delete((int)$id);
        $_SESSION['message'] = ['type' => 'success', 'text' => 'Work order deleted.'];
        header('Location: ' . rtrim(BASE_URL,'/') . '/supervisor/workorders'); exit;
    }
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
