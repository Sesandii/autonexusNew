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

        $this->view('Receptionist/Complaints/complaintsReceptionist', [
            'complaints' => $complaints,
            'activePage' => 'complaints'
        ]);
    }

    // 2️⃣ Show create form
    public function create(): void {
        $this->view('Receptionist/Complaints/newComplaint');
    }

    // 3️⃣ Store new complaint
    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'customer_name'  => $_POST['customer_name'] ?? '',
                'phone'          => $_POST['phone'] ?? '',
                'email'          => $_POST['email'] ?? '',
                'vehicle'        => $_POST['vehicle'] ?? '',
                'vehicle_number' => $_POST['vehicle_number'] ?? '',
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

    // 4️⃣ Show single complaint
    public function show(int $id): void {
        $complaint = $this->model->find($id);
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

        $this->view('Receptionist/Complaints/editComplaint', [
            'complaint' => $complaint
        ]);
    }

    // 6️⃣ Update existing complaint
    public function update(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'customer_name'  => $_POST['customer_name'] ?? '',
                'phone'          => $_POST['phone'] ?? '',
                'email'          => $_POST['email'] ?? '',
                'vehicle'        => $_POST['vehicle'] ?? '',
                'vehicle_number' => $_POST['vehicle_number'] ?? '',
                'complaint_date' => $_POST['complaint_date'] ?? null,
                'complaint_time' => $_POST['complaint_time'] ?? null,
                'description'    => $_POST['description'] ?? '',
                'priority'       => $_POST['priority'] ?? 'Medium',
                'status'         => $_POST['status'] ?? 'Open',
                'assigned_to'    => $_POST['assigned_to'] ?? null
            ];

            $this->model->update($id, $data);

            $this->redirect(BASE_URL . '/receptionist/complaints/' . $id);
        }
    }

    // 7️⃣ View complaints history of a customer
    public function history(string $customer_name): void {
        $complaints = $this->model->getByCustomer($customer_name);
        $this->view('Receptionist/Complaints/viewHistory', [
            'complaints' => $complaints
        ]);
    }

 public function delete(int $id): void {
    $this->model->delete($id);
    // Redirect to complaints list after deletion
    $this->redirect(BASE_URL . '/receptionist/complaints');
}

}
