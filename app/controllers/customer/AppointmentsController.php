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

        $userId = (int)($_SESSION['user_id'] ?? 0);

        $model = new Appointments();
        // Pull all for now; you can paginate later
        $rows = $model->getByCustomer($userId);

        $this->view('customer/appointments/index', [
            'title'       => 'Your Appointments',
            'appointments'=> $rows,
        ]);
    }

    // JSON endpoint (handy if you later add client-side filters)
    public function list(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $model = new Appointments();
        $rows  = $model->getByCustomer($userId);

        echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
    }

    // Simple cancel action
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

        $userId        = (int)($_SESSION['user_id'] ?? 0);
        $appointmentId = (int)($_POST['appointment_id'] ?? 0);

        $model = new Appointments();
        $ok = $model->cancelIfCustomerOwns($userId, $appointmentId);

        if (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['success' => $ok]);
            return;
        }

        // Redirect back
        $base = rtrim(BASE_URL, '/');
        header("Location: {$base}/customer/appointments");
    }
}
