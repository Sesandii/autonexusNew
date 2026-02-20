<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Complaint;

class ComplaintsController extends Controller
{
    private $complaintModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->complaintModel = new Complaint(db());
    }

    public function index()
    {
        // Fetch all complaints with related info
        $complaints = $this->complaintModel->getAllComplaints();

        // Pass to view
        $this->view('supervisor/complaints/index', [
            'complaints' => $complaints
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
