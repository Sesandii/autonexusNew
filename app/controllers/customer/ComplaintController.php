<?php
// app/controllers/customer/ComplaintController.php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Complaint;

class ComplaintController extends Controller
{
    public function index(): void
    {
        $this->requireCustomer();

        $userId = $this->userId();
        $model  = new Complaint();

        $complaints = $model->getByUser($userId);
        $vehicles   = $model->vehiclesByUser($userId);

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        $this->view('customer/complaints/index', [
            'title'      => 'My Complaints',
            'complaints' => $complaints,
            'vehicles'   => $vehicles,
            'flash'      => $flash,
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $this->requireCustomer();

        $userId      = $this->userId();
        $vehicleId   = (int)($_POST['vehicle_id']  ?? 0);
        $description = trim((string)($_POST['description'] ?? ''));
        $priority    = trim((string)($_POST['priority']    ?? 'Medium'));

        $allowed = ['Low', 'Medium', 'High'];
        if (!in_array($priority, $allowed, true)) {
            $priority = 'Medium';
        }

        if ($vehicleId <= 0 || $description === '') {
            $_SESSION['flash'] = 'Please select a vehicle and describe the issue.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/customer/complaints');
            exit;
        }

        $model = new Complaint();
        $ok    = $model->create($userId, $vehicleId, $description, $priority);

        $_SESSION['flash'] = $ok
            ? 'Your complaint has been submitted successfully.'
            : 'Failed to submit complaint. Please try again.';

        header('Location: ' . rtrim(BASE_URL, '/') . '/customer/complaints');
        exit;
    }
}
