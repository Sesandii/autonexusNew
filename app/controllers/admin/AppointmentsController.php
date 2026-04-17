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
        $dateFrom = trim($_GET['dateFrom'] ?? '');
        $dateTo   = trim($_GET['dateTo'] ?? '');
        $date     = trim($_GET['date'] ?? date('Y-m-d'));

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $date = date('Y-m-d');
        }

        $validDateRange = false;
        if (
            $dateFrom !== '' &&
            $dateTo !== '' &&
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom) &&
            preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo) &&
            $dateFrom <= $dateTo
        ) {
            $validDateRange = true;
        }

        if ($validDateRange) {
            $rows = $this->appointments->getAppointmentsByDateRange($dateFrom, $dateTo);
            $selectedDate = $dateFrom;
        } else {
            $rows = $this->appointments->getAppointmentsByDate($date);
            $selectedDate = $date;
        }

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

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $user = $_SESSION['user'] ?? [];

        $this->view('admin/admin-appointments/index', [
            'appointments' => $appointments,
            'branches' => $branches,
            'services' => $services,
            'selectedDate' => $selectedDate,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
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