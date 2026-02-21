<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\ServiceHistory;

class ServiceHistoryController extends Controller
{
    public function index(): void
    {
        // Allow only logged-in customers
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        // Session user id is stored in $_SESSION['user']['user_id']
        $userId = (int)($_SESSION['user']['user_id'] ?? 0);

        $model = new ServiceHistory();
        $services = $model->getByCustomer($userId);

        $this->view('customer/service-history/index', [
            'title' => 'Service History',
            'services' => $services,
        ]);
    }
}
