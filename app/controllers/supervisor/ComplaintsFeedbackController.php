<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Complaint;
use app\model\supervisor\Feedback;

class ComplaintsFeedbackController extends Controller
{
    private Complaint $complaintModel;
    private Feedback $feedbackModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->complaintModel = new Complaint(db());
        $this->feedbackModel  = new Feedback(db());
    }

    public function index()
    {
        // Fetch complaints and feedbacks
        $complaints = $this->complaintModel->getAllComplaints();
        $feedbacks  = $this->feedbackModel->getAllFeedbacks();

        // Pass both datasets to a single view
        $this->view('supervisor/complaints_feedbacks/index', [
            'complaints' => $complaints,
            'feedbacks'  => $feedbacks
        ]);
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}



