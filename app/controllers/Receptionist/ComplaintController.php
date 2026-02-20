<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\ComplaintModel;

class ComplaintController extends Controller {

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

        $this->view('receptionist/Complaints/complaintsReceptionist', [
            'complaints' => $complaints,
            'activePage' => 'complaints'
        ]);
    }

    // 2️⃣ Show create form
    public function create(): void {
        $this->view('receptionist/Complaints/newComplaint');
    }

    public function fetchByPhone(): void {
    $phone = $_GET['phone'] ?? '';

    if (!$phone) {
        $this->json(['success' => false, 'message' => 'Phone required']);
        return;
    }

    // Get customer info
    $customer = $this->model->getCustomerByPhone($phone);

    if (!$customer) {
        $this->json(['success' => false, 'message' => 'Customer not found']);
        return;
    }

    // Get all vehicles for this customer
    $vehicles = $this->model->getVehiclesByCustomer($customer['customer_id']);

    // Return combined data
    $this->json([
        'success' => true,
        'data' => array_merge($customer, ['vehicles' => $vehicles])
    ]);
}

protected function json(array $data): void {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
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

        $this->redirect(BASE_URL . '/receptionist/complaints');
    }
}

/*public function store(): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'customer_id'    => $_POST['customer_id'] ?? null,   // required
            'user_id'        => $_POST['user_id'] ?? null,       // required
            'vehicle_id'     => $_POST['vehicle_id'] ?? null,    // required
            'complaint_date' => $_POST['complaint_date'] ?? null,
            'complaint_time' => $_POST['complaint_time'] ?? null,
            'description'    => $_POST['description'] ?? '',
            'priority'       => $_POST['priority'] ?? 'Medium',
            'status'         => $_POST['status'] ?? 'Open',
            'assigned_to'    => $_POST['assigned_to'] ?? null
        ];

        // Optional: Validate required IDs
        if (!$data['customer_id'] || !$data['user_id'] || !$data['vehicle_id']) {
            $this->redirect(BASE_URL . '/receptionist/complaints/create?error=missing_ids');
            return;
        }

        $this->model->create($data);

        $this->redirect(BASE_URL . '/receptionist/complaints');
    }
}

*/

    // 4️⃣ Show single complaint
   /* public function show(int $id): void {
        $complaint = $this->model->find($id);
        if (!$complaint) {
            http_response_code(404);
            echo "Complaint not found";
            return;
        }

        $this->view('receptionist/Complaints/complainDetailsReceptionist', [
            'complaint' => $complaint
        ]);
    }*/
public function show(int $complaintId): void {
    $complaint = $this->model->find($complaintId);
    if (!$complaint) {
        http_response_code(404);
        echo "Complaint not found";
        return;
    }

    $this->view('Receptionist/Complaints/complainDetailsReceptionist', [
        'complaint' => $complaint
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

    $this->view('receptionist/Complaints/editComplaint', [
        'complaint' => $complaint,
        'vehicles'  => $vehicles   // pass this to the view
    ]);
}


    public function update(int $id): void {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $customer_id = $_POST['customer_id'] ?? null;
        $vehicle_id  = $_POST['vehicle_id'] ?? null;

        if (!$customer_id) {
            // Customer ID is required
            $this->redirect(BASE_URL . "/receptionist/complaints/$id?error=missing_customer");
            return;
        }

        // Get user_id automatically from customer_id
        $user_id = $this->model->getUserIdByCustomer((int)$customer_id);
        if (!$user_id) {
            // No linked user found → cannot update
            $this->redirect(BASE_URL . "/receptionist/complaints/$id?error=missing_user");
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
            'status'         => $_POST['status'] ?? 'Open',
            'assigned_to'    => $_POST['assigned_to'] ?? null
        ];

        // Call model update
        $this->model->update($id, $data);

        // Redirect to complaint details page
        $this->redirect(BASE_URL . "/receptionist/complaints/$id");
    }
}


    // 7️⃣ View complaints history of a customer
    public function history(string $customer_name): void {
        $complaints = $this->model->getByCustomer($customer_name);
        $this->view('receptionist/Complaints/viewHistory', [
            'complaints' => $complaints
        ]);
    }

 public function delete(int $id): void {
    $this->model->delete($id);
    // Redirect to complaints list after deletion
    $this->redirect(BASE_URL . '/receptionist/complaints');
}

}
