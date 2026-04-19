<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Complaints;

class ComplaintsController extends Controller
{
    private Complaints $complaints;

    // Initialize controller dependencies and request context.
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->complaints = new Complaints();
    }

    // Display the main listing or dashboard page.
    public function index(): void
    {
        $filters = [
            'search' => trim($_GET['q'] ?? ''),
            'status' => trim($_GET['status'] ?? ''),
            'priority' => trim($_GET['priority'] ?? ''),
            'branch_id' => trim($_GET['branch_id'] ?? ''),
            'assigned_to' => trim($_GET['assigned_to'] ?? ''),
            'sla' => trim($_GET['sla'] ?? ''),
        ];

        $records = $this->complaints->list($filters);
        $branches = $this->complaints->getBranches();
        $assignableUsers = $this->complaints->getAssignableUsers();
        $summary = $this->complaints->summaryCards();

        $this->view('admin/admin-viewcomplaints/index', [
            'records' => $records,
            'branches' => $branches,
            'assignableUsers' => $assignableUsers,
            'filters' => $filters,
            'summary' => $summary,
            'pageTitle' => 'Complaints',
            'current' => 'complaints',
        ]);
    }

    // Display details for a single record.
    public function show(): void
    {
        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid complaint ID';
            return;
        }

        $record = $this->complaints->find($id);
        if (!$record) {
            http_response_code(404);
            echo 'Complaint not found';
            return;
        }

        $assignableUsers = $this->complaints->getAssignableUsers();

        $this->view('admin/admin-viewcomplaints/show', [
            'record' => $record,
            'assignableUsers' => $assignableUsers,
            'pageTitle' => 'Complaint #' . $id,
            'current' => 'complaints',
        ]);
    }

    // Validate input and update an existing record.
    public function update(): void
    {
        $id = (int) ($_POST['complaint_id'] ?? 0);

        if ($id <= 0) {
            http_response_code(400);
            echo 'Invalid complaint ID';
            return;
        }

        $status = trim((string) ($_POST['status'] ?? 'open'));
        $priority = trim((string) ($_POST['priority'] ?? 'medium'));
        $assignedTo = $_POST['assigned_to_user_id'] ?? null;
        $resolutionNote = trim((string) ($_POST['resolution_note'] ?? ''));

        if (isset($_POST['reopen']) && $_POST['reopen'] === '1') {
            $status = 'open';
            $resolutionNote = $resolutionNote !== ''
                ? $resolutionNote
                : 'Complaint reopened by admin.';
        }

        $allowedStatuses = ['open', 'in_progress', 'resolved', 'closed'];
        $allowedPriorities = ['low', 'medium', 'high'];

        if (!in_array($status, $allowedStatuses, true)) {
            $status = 'open';
        }

        if (!in_array($priority, $allowedPriorities, true)) {
            $priority = 'medium';
        }

        $this->complaints->update($id, [
            'status' => $status,
            'priority' => $priority,
            'assigned_to_user_id' => $assignedTo,
            'resolution_note' => $resolutionNote,
        ]);

        $this->setSuccessToast('Complaint updated successfully.');

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewcomplaints/show?id=' . $id);
        exit;
    }

    // Ensure the current session belongs to an admin user.
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}