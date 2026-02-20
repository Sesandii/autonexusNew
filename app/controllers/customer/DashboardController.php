<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        // If you have a login/role guard method, call it safely:
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        } elseif (method_exists($this, 'requireLogin')) {
            $this->requireLogin();
        }

        // TODO: wire these from DB later. For now, pass placeholders to match your static UI.
        $data = [
            'user_first_name'   => $_SESSION['first_name'] ?? 'Customer',
            'next_appointment'  => ['date' => '2025-10-20', 'service' => 'Oil Change'],
            'mileage'           => ['current' => 12450, 'next_service_at' => 15000],
            'feedback_pending'  => 1,
            'recent_services'   => [
                ['title' => 'Tire Replacement', 'date' => '2025-10-10'],
                ['title' => 'Battery Check',    'date' => '2025-10-02'],
            ],
        ];

        $this->view('customer/dashboard/index', $data);
    }
}
