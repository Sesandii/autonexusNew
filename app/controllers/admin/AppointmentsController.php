<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Appointment;

class AppointmentsController extends Controller
{
    private Appointment $appointments;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->appointments = new Appointment();
    }

    /** GET /admin/appointments */
    public function index(): void
    {
        // Get date range parameters for filtering
        $dateFrom = $_GET['dateFrom'] ?? '';
        $dateTo = $_GET['dateTo'] ?? '';
        $date = $_GET['date'] ?? date('Y-m-d');

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        // Validate date range parameters
        $validDateRange = false;
        if (
            $dateFrom && $dateTo &&
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) &&
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo) &&
            $dateFrom <= $dateTo
        ) {
            $validDateRange = true;
        }

        // Get appointments - use date range if provided, otherwise use single date
        if ($validDateRange) {
            $rows = $this->appointments->getAppointmentsByDateRange($dateFrom, $dateTo);
            $selectedDate = $dateFrom; // For display purposes
        } else {
            $rows = $this->appointments->getAppointmentsByDate($date);
            $selectedDate = $date;
        }

        // Add helper fields for view
        $appointments = [];
        foreach ($rows as $r) {
            $datetime = $r['appointment_date'] . ' ' . $r['appointment_time'];

            $appointments[] = [
                'id' => (int) $r['appointment_id'],
                'customer' => $r['customer_name'],
                'service' => $r['service_name'],
                'service_id' => (int) $r['service_id'],
                'branch' => $r['branch_name'],
                'branch_id' => (int) $r['branch_id'],
                'date' => $r['appointment_date'],
                'time' => $r['appointment_time'],
                'datetime' => $datetime,
                'status' => Appointment::statusLabel($r['db_status']),
                'db_status' => $r['db_status'],
                'supervisor' => $r['supervisor_name'] ?? 'Not assigned',
                'assigned_to' => $r['assigned_to'] ?? null,
            ];
        }

        $branches = $this->appointments->getBranches();
        $services = $this->appointments->getServices();

        // Get user from session
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $user = $_SESSION['user'] ?? [];

        $this->view('admin/admin-appointments/index', [
            'appointments' => $appointments,
            'branches' => $branches,
            'services' => $services,
            'selectedDate' => $date,
            'user' => $user,
            'pageTitle' => 'Appointments - AutoNexus',
            'current' => 'appointments',
        ]);
    }

    /** GET /admin/appointments/show?id=123 */
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Invalid appointment id.";
            return;
        }

        $appointment = $this->appointments->findWithDetails($id);
        if (!$appointment) {
            http_response_code(404);
            echo "Appointment not found.";
            return;
        }

        $this->view('admin/admin-appointments/show', [
            'appointment' => $appointment,
            'pageTitle' => 'Appointment #' . $id,
            'current' => 'appointments',
        ]);
    }

    /** GET /admin/appointments/edit?id=123 */
    public function edit(): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Invalid appointment id.";
            return;
        }

        $appointment = $this->appointments->findWithDetails($id);
        if (!$appointment) {
            http_response_code(404);
            echo "Appointment not found.";
            return;
        }

        $branches = $this->appointments->getBranches();
        $services = $this->appointments->getServices();

        $this->view('admin/admin-appointments/edit', [
            'appointment' => $appointment,
            'branches' => $branches,
            'services' => $services,
            'pageTitle' => 'Edit Appointment #' . $id,
            'current' => 'appointments',
        ]);
    }

    /** POST /admin/appointments/update */
    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/appointments');
            return;
        }

        $id = (int) ($_POST['appointment_id'] ?? 0);
        if ($id <= 0) {
            http_response_code(400);
            echo "Invalid appointment id.";
            return;
        }

        $data = [
            'branch_id' => (int) ($_POST['branch_id'] ?? 0),
            'service_id' => (int) ($_POST['service_id'] ?? 0),
            'appointment_date' => trim($_POST['appointment_date'] ?? ''),
            'appointment_time' => trim($_POST['appointment_time'] ?? ''),
            'status' => trim($_POST['status'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
        ];

        $this->appointments->update($id, $data);

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/appointments');
        exit;
    }

    /** POST /admin/appointments/delete */
    public function delete(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/appointments');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            $this->appointments->delete($id);
        }

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/appointments');
        exit;
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u || ($u['role'] ?? '') !== 'admin') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
