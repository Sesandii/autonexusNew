<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ComplaintModel;

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

        $this->view('manager/Complaints/complaintsManager', [
            'complaints' => $complaints,
            'activePage' => 'complaints'
        ]);
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

}
    ?>