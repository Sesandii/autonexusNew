<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ComplaintModel;

class ComplaintController extends BaseManagerController {

    protected ComplaintModel $model;

    public function __construct() {
        parent::__construct();
        $this->model = new ComplaintModel();
    }

    // 1️⃣ List all complaints (with optional filters)
    public function index(): void {
        $search   = $_GET['search'] ?? '';
        $status   = $_GET['status'] ?? '';
        $priority = $_GET['priority'] ?? '';

        $complaints = $this->model->filter($search, $status, $priority);

        $this->view('manager/Complaints/complaintsManager', [
            'complaints' => $complaints,
            'activePage' => 'complaints'
        ]);
    }

    public function getVehiclesByCustomer(): void {
    $customerId = $_GET['customer_id'] ?? 0;
    
    if (!$customerId) {
        $this->json(['success' => false, 'message' => 'Customer ID required']);
        return;
    }
    
    $vehicles = $this->model->getVehiclesByCustomer((int)$customerId);
    
    $this->json([
        'success' => true,
        'vehicles' => $vehicles
    ]);
}

   // 3️⃣ Store new complaint
   public function store(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'customer_id'    => $_POST['customer_id'], // already autofilled
            'vehicle_id'     => $_POST['vehicle_id'],  // already autofilled
            'complaint_date' => $_POST['complaint_date'] ?? null,
            'complaint_time' => $_POST['complaint_time'] ?? null,
            'description'    => $_POST['description'] ?? '',
            'priority'       => $_POST['priority'] ?? 'Medium',
            'status'         => $_POST['status'] ?? 'Open',
            'assigned_to'    => $_POST['assigned_to'] ?? null
        ];

        $this->model->create($data);

        $this->redirect(BASE_URL . '/manager/complaints');
    }
}


public function show(int $complaintId): void {
    $complaint = $this->model->find($complaintId);
    if (!$complaint) {
        http_response_code(404);
        echo "Complaint not found";
        return;
    }

    $this->view('manager/Complaints/complainDetailManager', [
        'complaint' => $complaint
    ]);
}

public function history(int $customerId): void
{
    // 1. Appointments + work orders + embedded complaints
    $appointments = $this->model->getCustomerAppointments($customerId);

    // 2. Direct complaints (not tied to appointments)
    $complaints = $this->model->getCustomerComplaints($customerId);

    // 3. Build unified customer history object
    $customer = [
        'appointments' => $appointments,
        'complaints'   => $complaints
    ];

    $this->view('manager/Complaints/viewHistory', [
        'customer'   => $customer,
        'customerId' => $customerId
    ]);
}     


    // 5️⃣ Show edit form
    public function edit(int $id): void {
    $complaint = $this->model->find($id);
    if (!$complaint) {
        http_response_code(404);
        echo "Complaint not found";
        return;
    }

    // 🔹 Fetch vehicles for this customer
    $vehicles = $this->model->getVehiclesByCustomer($complaint['customer_id']);

    $supervisors = $this->model->getSupervisors(); // new model method

    $this->view('manager/Complaints/editComplaint', [
        'complaint' => $complaint,
        'vehicles'  => $vehicles,   // pass this to the view
        'supervisors'=> $supervisors
    ]);
}


    public function update(int $id): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer_id = $_POST['customer_id'] ?? null;
        $vehicle_id  = $_POST['vehicle_id'] ?? null;

        if (!$customer_id) {
            $this->redirect(BASE_URL . "/manager/complaints/$id?error=missing_customer");
            return;
        }

        // Get user_id automatically from customer_id
        $user_id = $this->model->getUserIdByCustomer((int)$customer_id);
        if (!$user_id) {
            $this->redirect(BASE_URL . "/manager/complaints/$id?error=missing_user");
            return;
        }

        $data = [
            'customer_id'    => (int)$customer_id,
            'user_id'        => (int)$user_id,
            'vehicle_id'     => $vehicle_id ? (int)$vehicle_id : null,
            'complaint_date' => $_POST['complaint_date'] ?? null,
            'complaint_time' => $_POST['complaint_time'] ?? null,
            'description'    => $_POST['description'] ?? '',
            'priority'       => $_POST['priority'] ?? 'Medium',
            'status'         => $_POST['status'] ?? 'open',
            'assigned_to'    => !empty($_POST['assigned_to']) ? (int)$_POST['assigned_to'] : null  // Make sure this is integer or null
        ];

        // Call model update
        $this->model->update($id, $data);

        // Redirect to complaint details page
        $this->redirect(BASE_URL . "/manager/complaints/$id");
    }
}

 public function delete(int $id): void {
    $this->model->delete($id);
    // Redirect to complaints list after deletion
    $this->redirect(BASE_URL . '/manager/complaints');
}

public function testCustomer(): void {
    $model = new \app\model\Manager\ComplaintModel();
    $customer = $model->getCustomerById(6); // replace 6 with the customer ID you want to check
    var_dump($customer);
    exit; // stop execution so you can see the output
}
}
