<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\CustomerModel;

class CustomerController extends BaseManagerController
{
    private CustomerModel $model; // ✅ Add this declaration

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        // Use the same db() helper or $this->db from base controller
        $db = db();  

        $this->model = new CustomerModel($db); // ✅ No more warning
    }

    /**
     * Show All Customers
     */
    public function index(): void
    {
        $customers = $this->model->getAllCustomers();

        $this->view('manager/Customer Profile/profile', [
            'customers' => $customers
        ]);
    }

public function show(int $customerId): void
{
    $customer = $this->model->getCustomerById($customerId);

    if (!$customer) {
        http_response_code(404);
        echo "Customer not found";
        return;
    }

    // 🔥 DEBUG PROPERLY
    $appointments = $this->model->getCustomerAppointments($customerId);

    $customer['appointments'] = $appointments;

    $this->view('manager/Customer Profile/individualDetails', [
        'customer' => $customer
    ]);
}
private function groupBy(array $data, string $key): array
{
    $result = [];

    foreach ($data as $row) {
        $result[$row[$key]][] = $row;
    }

    return $result;
}

}
