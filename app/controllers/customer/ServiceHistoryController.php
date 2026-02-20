<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\ServiceHistory;

class ServiceHistoryController extends Controller
{
    public function index(): void
    {
        // Make sure the user is logged in as a customer
        $this->requireCustomer();

        // This helper is defined in app\core\Controller
        $userId = $this->userId();   // == $_SESSION['user']['user_id']

        if (!$userId) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $model    = new ServiceHistory();
        $services = $model->getByCustomer($userId);

        $this->view('customer/service-history/index', [
            'title'    => 'Service History',
            'services' => $services,
        ]);
    }
}
