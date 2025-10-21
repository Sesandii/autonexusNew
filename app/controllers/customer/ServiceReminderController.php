<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\ServiceReminder;

class ServiceReminderController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        $userId = $_SESSION['user_id'] ?? 0;

        $model = new ServiceReminder();
        $reminders = $model->getByCustomer($userId);

        $this->view('customer/service-reminder/index', [
            'title' => 'Service Reminder',
            'reminders' => $reminders
        ]);
    }

    public function updateMileage(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $vehicleId = $_POST['vehicle_id'] ?? null;
        $mileage   = $_POST['mileage'] ?? null;

        if (!$vehicleId || !$mileage) {
            echo json_encode(['error' => 'Missing data']);
            return;
        }

        $model = new ServiceReminder();
        $model->updateMileage((int)$vehicleId, (int)$mileage);

        echo json_encode(['success' => true]);
    }
}
