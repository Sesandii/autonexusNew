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
        $this->requireCustomer();

        // Use the parent helper method to get user_id
        $userId = $this->userId();

        $model = new ServiceHistory();
        $services = $model->getByCustomer($userId);

        // Debug logging
        error_log("ServiceHistoryController - UserID: $userId");
        error_log("ServiceHistoryController - Services found: " . count($services));
        if (!empty($services)) {
            error_log("First service: " . print_r($services[0], true));
        }

        $this->view('customer/service-history/index', [
            'title' => 'Service History',
            'services' => $services,
        ]);
    }

    public function show(int $id): void
    {
        $this->requireCustomer();
        $userId = $this->userId();

        $model = new ServiceHistory();
        $service = $model->getById($id);

        // Check if service exists and belongs to this customer
        if (!$service) {
            $_SESSION['flash'] = 'Service record not found.';
            header('Location: ' . BASE_URL . '/customer/service-history');
            exit;
        }

        // Verify ownership
        $customerServices = $model->getByCustomer($userId);
        $isOwner = false;
        foreach ($customerServices as $s) {
            if ((int)$s['work_order_id'] === $id) {
                $isOwner = true;
                break;
            }
        }

        if (!$isOwner) {
            $_SESSION['flash'] = 'You do not have access to this service record.';
            header('Location: ' . BASE_URL . '/customer/service-history');
            exit;
        }

        $this->view('customer/service-history/show', [
            'title' => 'Service Details',
            'service' => $service,
        ]);
    }
}
