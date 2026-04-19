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
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        $userId = $_SESSION['user_id'] ?? ($_SESSION['user']['user_id'] ?? null);
    
        if (!$userId) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    
        $stmt = db()->prepare("SELECT branch_id FROM supervisors WHERE user_id = ?");
        $stmt->execute([$userId]);
        $supervisorRecord = $stmt->fetch();
        
        if (!$supervisorRecord) {
            die("Error: This user account is not linked to a branch in the supervisors table.");
        }
    
        $branchId = (int)$supervisorRecord['branch_id'];
    
        $complaints = $this->complaintModel->getAllComplaints($branchId);
        $feedbacks  = $this->feedbackModel->getAllFeedbacks($branchId);
    
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
                $this->flash('success', 'Status updated.');
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
                $this->flash('success', 'Reply updated.');
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

    private function flash(string $type, string $text): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['message'] = [
        'type' => $type,
        'text' => $text
    ];
}
}



