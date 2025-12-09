<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\ServiceReminder;

class ServiceReminderController extends Controller
{
    private ServiceReminder $model;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->model = new ServiceReminder();
    }

    /**
     * GET /customer/service-reminder
     */
    public function index(): void
    {
        // Optional auth helper if your base Controller has it
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        // Your app seems to sometimes use user['user_id'] and sometimes user_id
        $userId =
            $_SESSION['user']['user_id'] ??
            ($_SESSION['user_id'] ?? 0);

        $reminders = $this->model->getForUser((int)$userId);

        $this->view('customer/service-reminder/index', [
            'title'     => 'Service Reminder',
            'reminders' => $reminders,
        ]);
    }

    /**
     * POST /customer/service-reminder/update
     * Form posts back from each card to update current mileage.
     */
    public function updateMileage(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        $userId =
            $_SESSION['user']['user_id'] ??
            ($_SESSION['user_id'] ?? 0);

        $vehicleId = (int)($_POST['vehicle_id'] ?? 0);
        $mileage   = (int)($_POST['mileage'] ?? 0);

        if ($userId <= 0 || $vehicleId <= 0 || $mileage < 0) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/customer/service-reminder');
            return;
        }

        $this->model->updateMileage($vehicleId, $userId, $mileage);

        // Classic PRG pattern
        header('Location: ' . rtrim(BASE_URL, '/') . '/customer/service-reminder');
        exit;
    }
}
