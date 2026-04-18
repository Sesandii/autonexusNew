<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\AppointmentModel;

class AppointmentsController extends Controller
{
    private function guardManager(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;

        // Check role
        if (!$u || ($u['role'] ?? '') !== 'manager') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        // Load branch_id if not set yet
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
    }
    
    public function index(): void
    {
        $today = date('Y-m-d');
        $branchId = $_SESSION['user']['branch_id'];

        $appointmentModel = new AppointmentModel(db());
        $appointments = $appointmentModel->getAppointmentsByDateAndBranch($today, $branchId);

        $this->view('manager/Appointments/appointment', [
            'appointments' => $appointments,
            'today' => $today
        ]);
    }

    public function day()
    {
        $date = $_GET['date'] ?? date('Y-m-d');
        $branchId = $_SESSION['user']['branch_id'];
        
        $appointmentModel = new \app\model\Manager\AppointmentModel(db());
        $appointments = $appointmentModel->getAppointmentsByDateAndBranch($date, $branchId);

        $this->view('manager/Appointments/dayAppointment', [
            'appointments' => $appointments,
            'date' => $date
        ]);
    }

    // ✅ FIXED: Complete edit method with proper signature
    public function edit($id = null)
{
    if (!$id) {
        header('Location: ' . BASE_URL . '/manager/appointments');
        exit;
    }

    $appointmentModel = new AppointmentModel(db());
    $appointment = $appointmentModel->getAppointmentById($id);

    $supervisors = $appointmentModel->getSupervisorsByBranch($appointment['branch_id']);

    $this->view('manager/Appointments/updateApp', [
        'appointment' => $appointment,
        'supervisors' => $supervisors
    ]);
}


    public function update(): void
    {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            $appointmentId = $_POST['appointment_id'] ?? null;
            $assignedTo = $_POST['assigned_to'] ?? null;
            $notes = $_POST['notes'] ?? null;
            
            if (!$appointmentId) {
                throw new \Exception('Appointment ID is required');
            }
            
            $appointmentModel = new AppointmentModel(db());
            
            // Verify the appointment belongs to manager's branch
            $appointment = $appointmentModel->getAppointmentById($appointmentId);
            $managerBranchId = $_SESSION['user']['branch_id'];
            
            if ($appointment['branch_id'] != $managerBranchId) {
                throw new \Exception('You can only update appointments from your branch');
            }
            
            // Convert empty string to null for database
            $assignedTo = $assignedTo === '' ? null : (int)$assignedTo;
            
            $success = $appointmentModel->updateAppointmentAssignment(
            $appointmentId,
            $assignedTo,
            $notes
        );
        
            if (!$success) {
                throw new \Exception('Failed to update appointment');
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Appointment updated successfully'
            ]);
            
        } catch (\Throwable $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
        
        exit;
    }
}
?>