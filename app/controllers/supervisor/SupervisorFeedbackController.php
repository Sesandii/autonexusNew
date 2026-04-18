<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Feedback;

class SupervisorFeedbackController extends Controller   {
    private $feedbackModel;

    public function __construct() {
        $this->requireAdmin();
        $this->feedbackModel = new Feedback(db());
    }

    public function index() {
        $feedbacks = $this->feedbackModel->getAllFeedbacks();
        $this->view('supervisor/feedbacks/index', ['feedbacks' => $feedbacks]);
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


