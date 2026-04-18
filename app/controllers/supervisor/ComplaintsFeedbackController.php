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
        $complaints = $this->complaintModel->getAllComplaints();
        $feedbacks  = $this->feedbackModel->getAllFeedbacks();

        $this->view('supervisor/complaints_feedbacks/index', [
            'complaints' => $complaints,
            'feedbacks'  => $feedbacks
        ]);
    }

    public function updateComplaintStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['complaint_id'];
            $status = $_POST['status'];
    
            if ($this->complaintModel->updateStatus($id, $status)) {
                header("Location: " . BASE_URL . "/supervisor/complaints_feedbacks#complaints");
                exit();
            }
        }
    }

    public function addFeedbackReply() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
            $data = [
                'feedback_id' => $_POST['feedback_id'],
                'reply_text'  => $_POST['reply_text'],
                'replied_by'  => $_SESSION['user_id'] ?? 1
            ];
            
            if ($this->feedbackModel->saveFeedbackReply($data)) {
                if (session_status() !== PHP_SESSION_ACTIVE) session_start();
                $_SESSION['active_tab'] = 'feedbacks'; 
    
                header("Location: " . BASE_URL . "/supervisor/complaints_feedbacks");
                exit();
            }
        }
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



