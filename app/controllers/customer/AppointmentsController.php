<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Appointments;

class AppointmentsController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        // IMPORTANT: use the helper so it works with either session shape
        $userId = $this->userId();

        $model = new Appointments();
        $rows  = $model->getByCustomer($userId);

        // Map DB rows to the keys your view expects
        $items = array_map(function ($r) {
            $status       = (string)($r['status'] ?? 'requested');
            $statusClass  = match ($status) {
                'completed' => 'completed',
                'cancelled' => 'cancelled',
                'ongoing'   => 'ongoing',
                'confirmed' => 'upcoming',
                default     => 'upcoming',
            };
            return [
                'appointment_id' => (int)$r['appointment_id'],
                'service'        => $r['service_name'] ?? 'Service',
                'branch'         => $r['branch_name']  ?? '—',
                'date'           => $r['appointment_date'] ?? '',
                'time'           => substr((string)($r['appointment_time'] ?? ''), 0, 5),
                'status'         => ucfirst($status),
                'status_class'   => $statusClass,
                'est_completion' => null, // fill if you compute ETA elsewhere
            ];
        }, $rows);

        $this->view('customer/appointments/index', [
            'title'        => 'Your Appointments',
            'appointments' => $items,
        ]);
    }

    public function list(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        header('Content-Type: application/json; charset=utf-8');

        $userId = $this->userId();
        $model  = new Appointments();
        $rows   = $model->getByCustomer($userId);

        echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
    }

    public function cancel(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        $userId        = $this->userId();
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);

        $model = new Appointments();
        $ok    = $model->cancelIfCustomerOwns($userId, $appointmentId);

        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => $ok]);
            return;
        }

        $base = rtrim(BASE_URL, '/');
        header("Location: {$base}/customer/appointments");
    }
}
