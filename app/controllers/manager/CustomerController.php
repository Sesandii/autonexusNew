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
    // 1. Base customer
    $customer = $this->model->getCustomerById($customerId);

    if (!$customer) {
        http_response_code(404);
        echo "Customer not found";
        return;
    }

    // 2. Appointments
    $appointments = $this->model->getAppointmentsByCustomer($customerId);

    $appointmentIds = array_column($appointments, 'appointment_id');

    // 3. Work orders + complaints (bulk fetch)
    $workOrders = $this->model->getWorkOrdersByAppointments($appointmentIds);
    $complaints = $this->model->getComplaintsByAppointments($appointmentIds);

    // 4. Group them
    $workByAppt = $this->groupBy($workOrders, 'appointment_id');
    $complaintsByAppt = $this->groupBy($complaints, 'appointment_id');

    // 5. Attach to appointments
    foreach ($appointments as &$appt) {
        $id = $appt['appointment_id'];

        $appt['work_orders'] = $workByAppt[$id] ?? [];
        $appt['complaints'] = $complaintsByAppt[$id] ?? [];
    }

    // 6. Attach to customer
    $customer['appointments'] = $appointments;

    // 7. Render view
    $this->view('receptionist/Customer Profile/individualDetails', [
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
