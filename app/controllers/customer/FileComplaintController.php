<?php
namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Complaint;
use app\model\customer\Appointments;

class FileComplaintController extends Controller {

    private $complaintModel;
    private $appointmentModel;

    public function __construct() {
        parent::__construct();
        $this->complaintModel = new Complaint();
        $this->appointmentModel = new Appointments();
    }

    /**
     * Display the file complaint form
     */
    public function index() {
        // Check authentication using parent method
        $this->requireCustomer();

        $user_id = $this->userId();

        // Get all completed appointments for this user
        $appointments = $this->appointmentModel->completedByUser($user_id);

        // Load the view
        $this->view('customer/file-complaint/index', [
            'appointments' => $appointments
        ]);
    }

    /**
     * Handle complaint form submission
     */
    public function submit() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/customer/file-complaint');
            exit;
        }

        // Check authentication using parent method
        $this->requireCustomer();

        $user_id = $this->userId();

        // Validate input
        $appointment_id = $_POST['appointment_id'] ?? null;
        $description = trim($_POST['complaint'] ?? '');
        $priority = $_POST['priority'] ?? 'Medium';

        if (empty($appointment_id)) {
            $_SESSION['complaint_error'] = 'Please select an appointment';
            header('Location: ' . BASE_URL . '/customer/file-complaint');
            exit;
        }

        if (empty($description) || strlen($description) < 10) {
            $_SESSION['complaint_error'] = 'Please provide a detailed description (at least 10 characters)';
            header('Location: ' . BASE_URL . '/customer/file-complaint');
            exit;
        }

        // Get appointment details to extract vehicle_id
        $appointment = $this->appointmentModel->getById($appointment_id);
        if (!$appointment) {
            $_SESSION['complaint_error'] = 'Invalid appointment selected';
            header('Location: ' . BASE_URL . '/customer/file-complaint');
            exit;
        }

        // Prepare complaint data (only fields that exist in the table)
        $complaintData = [
            'user_id' => $user_id,
            'vehicle_id' => $appointment['vehicle_id'] ?? null,
            'description' => $description,
            'priority' => $priority,
            'status' => 'Open'
        ];

        try {
            // Create the complaint
            $complaint_id = $this->complaintModel->create($complaintData);

            if ($complaint_id) {
                $_SESSION['complaint_success'] = 'Your complaint has been filed successfully. We will review it shortly.';
            } else {
                $_SESSION['complaint_error'] = 'Failed to file complaint. Please try again.';
            }
        } catch (\Exception $e) {
            $_SESSION['complaint_error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: ' . BASE_URL . '/customer/file-complaint');
        exit;
    }

    /**
     * View customer's complaint history
     */
    public function history() {
        // Check authentication using parent method
        $this->requireCustomer();

        $user_id = $this->userId();

        // Get all complaints for this user
        $complaints = $this->complaintModel->getByUserId($user_id);

        // Load the view (you may want to create this view later)
        $this->view('customer/complaints/history', [
            'complaints' => $complaints
        ]);
    }
}
