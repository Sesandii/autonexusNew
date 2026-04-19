<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\AppointmentModel;

class AppointmentsController extends BaseManagerController
{

    public function __construct()
    {
        parent::__construct();
       
    }
    
    public function index(): void
    {
        $today = date('Y-m-d');
        $branchId = $this->getBranchId();

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
        $branchId = $this->getBranchId();
        
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
            $managerBranchId = $this->getBranchId();
            
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