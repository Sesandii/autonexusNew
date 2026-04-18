<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ScheduleModel;

class ScheduleController extends Controller
{
    protected ScheduleModel $model;

    private function guardManager(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'manager') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        if (!isset($_SESSION['user']['branch_id'])) {
            $stmt = db()->prepare('SELECT branch_id FROM managers WHERE user_id = :uid LIMIT 1');
            $stmt->execute(['uid' => $u['user_id']]);
            $manager = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$manager) {
                header('Location: ' . rtrim(BASE_URL, '/') . '/login');
                exit;
            }

            $_SESSION['user']['branch_id'] = $manager['branch_id'];
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->guardManager();
        $db = db();
        $this->model = new ScheduleModel($db);
    }

    // Default schedule view (Team Overview)
public function index(): void
{
    $branchId = $_SESSION['user']['branch_id'];

    $users = $this->model->getTeamMembers($branchId);
    // REMOVE THIS LINE - we don't need it anymore
    // $availableEmployees = $this->model->getAvailableEmployees($branchId);

    $this->view('manager/Schedule/schedule', [
        'pageTitle' => 'Team Overview',
        'users' => $users,
        // REMOVE THIS LINE TOO
        // 'availableEmployees' => $availableEmployees,
        'branchId' => $branchId,
        'activePage' => 'teamSchedule'
    ]);
}

    // Add employee to team (AJAX endpoint)
    public function addToTeam(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }

        $userId = (int)($_POST['user_id'] ?? 0);
        $role = $_POST['role'] ?? '';
        $branchId = $_SESSION['user']['branch_id'];

        if (!$userId || !$role) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Missing required data']);
            exit;
        }

        $success = $this->model->assignToBranch($userId, $role, $branchId);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $success ? 'Team member added successfully!' : 'Failed to add team member'
        ]);
        exit;
    }

    // Personal schedule view for a specific team member
    public function personalSchedule(): void
    {
        $userId = $_GET['id'] ?? null;
        
        if (!$userId) {
            header('Location: ' . BASE_URL . '/manager/schedule');
            exit;
        }
        
        $employee = $this->model->getEmployeeById((int)$userId);
        
        if (!$employee) {
            header('Location: ' . BASE_URL . '/manager/schedule');
            exit;
        }
        
        if ($employee['role'] === 'mechanic') {
            $workOrders = $this->model->getMechanicWorkOrders((int)$userId);
        } else if ($employee['role'] === 'supervisor') {
            $workOrders = $this->model->getSupervisorAppointments((int)$userId);
        } else {
            $workOrders = [];
        }
        
        $this->view('manager/Schedule/personalSchedule', [
            'pageTitle' => $employee['first_name'] . ' ' . $employee['last_name'] . ' - Schedule',
            'employee' => $employee,
            'workOrders' => $workOrders,
            'activePage' => 'teamSchedule'
        ]);
    }

// Show add team member page
public function addMemberForm(): void
{
    $branchId = $_SESSION['user']['branch_id'];
    $search = $_GET['search'] ?? '';
    
    // Debug - check if search is received
    error_log("Search term: " . $search);
    
    $allStaff = $this->model->getAllStaff($search, $branchId);
    
    // Debug - check count
    error_log("Staff count: " . count($allStaff));
    
    $this->view('manager/Schedule/addMember', [
        'pageTitle' => 'Add Team Member',
        'allStaff' => $allStaff,
        'search' => $search,
        'activePage' => 'teamSchedule'
    ]);
}

// Assign employee to branch
public function assignToBranch(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/manager/schedule/add-member');
        exit;
    }
    
    $userId = (int)($_POST['user_id'] ?? 0);
    $role = $_POST['role'] ?? '';
    $branchId = $_SESSION['user']['branch_id'];
    
    if ($userId && $role) {
        $this->model->assignToBranch($userId, $role, $branchId);
        $_SESSION['success'] = 'Team member added!';
    }
    
    header('Location: ' . BASE_URL . '/manager/schedule/add-member');
    exit;
}

}
