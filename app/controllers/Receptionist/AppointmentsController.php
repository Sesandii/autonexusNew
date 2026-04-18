<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\AppointmentModel;

use PDO;
class AppointmentsController extends Controller
{

 private function guardReceptionist(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'receptionist') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
        $stmt = db()->prepare('SELECT branch_id FROM receptionists WHERE user_id = :uid LIMIT 1');
       
        $stmt->execute(['uid' => $u['user_id']]);
        $receptionist = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$receptionist) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $receptionist['branch_id'];
    }
}

public function __construct()
{
    parent::__construct();
    $this->guardReceptionist(); // 🔐 enforce manager login & branch
}


   public function index()
{
    $appointmentModel = new AppointmentModel(db());
    $date = date('Y-m-d'); // today
    
    // Get receptionist's branch_id from session
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    
    if (!$branchId) {
        die("Error: Branch not assigned to this receptionist");
    }
    
    // Fetch appointments only for receptionist's branch
    $appointments = $appointmentModel->getAppointmentsByDateAndBranch($date, $branchId);

    foreach ($appointments as &$app) {
        $app['supervisors'] = $appointmentModel->getAvailableSupervisors(
            $app['branch_id'], 
            $app['appointment_date'], 
            5 // max appointments per day
        );
    }

    $this->view('receptionist/Appointments/appointment', [
        'appointments' => $appointments
    ]);
}

   public function create()
{
    $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());
    $data = $appointmentModel->getAllServicesAndPackages();

    // Fetch all active branches
    $stmt = db()->prepare("SELECT branch_id, name, city FROM branches WHERE status='active' ORDER BY name ASC");
    $stmt->execute();
    $branches = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // Pass services, packages, and branches to the view
    $this->view('receptionist/Appointments/newAppointment', [
        'services' => $data['services'],
        'packages' => $data['packages'],
        'branches' => $branches
    ]);
}


    
public function day()
{
    $date = $_GET['date'] ?? date('Y-m-d'); // default today
    $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());
    
    // Get receptionist's branch_id from session
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    
    if (!$branchId) {
        die("Error: Branch not assigned to this receptionist");
    }

    // Check if a supervisor assignment was submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appointment_id'], $_POST['supervisor_id'])) {
        $appointmentModel->assignSupervisorToAppointment(
            (int)$_POST['appointment_id'], 
            (int)$_POST['supervisor_id']
        );
        // Redirect to avoid resubmission
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    }

    // Fetch appointments ONLY for receptionist's branch
    $appointments = $appointmentModel->getAppointmentsByDateAndBranch($date, $branchId);

    foreach ($appointments as &$app) {
        $app['supervisors'] = $appointmentModel->getAvailableSupervisors(
            $app['branch_id'], 
            $app['appointment_date'], 
            5 // max appointments per day
        );
    }

    $this->view('receptionist/Appointments/dayAppointment', [
        'appointments' => $appointments,
        'date' => $date
    ]);
}


public function getCustomer()
{
    // Make sure the phone is sent via GET or POST
    $phone = $_GET['phone'] ?? null;

    if (!$phone) {
        echo json_encode(['error' => 'Phone number is required']);
        return;
    }

    $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());
    $customer = $appointmentModel->getCustomerByPhone($phone);

    echo json_encode($customer);
    exit;
}

// Inside AppointmentsController
public function getServices(): void
{
    $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());
    $data = $appointmentModel->getAllServicesAndPackages();

    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

public function save(): void
{
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception('Invalid request method');
        }

        $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());

        // Debug - see what's actually being received
        error_log("POST data: " . print_r($_POST, true));

        // Safely fetch POST data
        $data = [
            'customer_id' => $_POST['customer_id'] ?? null,
            'vehicle_id'  => $_POST['vehicle_id'] ?? null,
            'branch_id'   => $_POST['branch_id'] ?? null,
            'service_id'  => $_POST['service_id'] ?? null,
            'appointment_date' => $_POST['appointment_date'] ?? null,
            'appointment_time' => $_POST['appointment_time'] ?? null,
            'status'      => $_POST['status'] ?? 'Requested',
            'notes'       => $_POST['notes'] ?? null
        ];

        // Validate required fields
        $missing = [];
        foreach (['customer_id','vehicle_id','branch_id','service_id','appointment_date','appointment_time'] as $key) {
            if (empty($data[$key])) {
                $missing[] = $key;
            }
        }
        
        if (!empty($missing)) {
            throw new \Exception("Missing fields: " . implode(', ', $missing));
        }

        // Save appointment
        $success = $appointmentModel->saveAppointment($data);

        if (!$success) {
            throw new \Exception('Failed to save appointment due to database error.');
        }

        echo json_encode([
            'success' => true,
            'message' => 'Appointment saved successfully'
        ]);

    } catch (\Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

public function getAppointmentsByDate(): void
{
    header('Content-Type: application/json');

    try {
        $date = $_GET['date'] ?? null;
        $branchId = $_SESSION['user']['branch_id'] ?? null;

        if (!$date) {
            throw new \Exception("Date is required");
        }
        
        if (!$branchId) {
            throw new \Exception("Branch not assigned");
        }

        $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());

        // Fetch appointments for the given date and branch
        $appointments = $appointmentModel->getAppointmentsByDateAndBranch($date, $branchId);

        echo json_encode([
            'success' => true,
            'appointments' => $appointments
        ]);

    } catch (\Throwable $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }

    exit;
}

public function assignSupervisor(): void
{
    try {
        $appointment_id = $_POST['appointment_id'] ?? null;
        $supervisor_id = $_POST['supervisor_id'] ?? null;
        $date = $_POST['date'] ?? date('Y-m-d'); // capture the current date

        if (!$appointment_id || !$supervisor_id) {
            throw new \Exception("Missing appointment or supervisor ID");
        }

        $appointmentModel = new AppointmentModel(db());
        $success = $appointmentModel->assignSupervisorToAppointment((int)$appointment_id, (int)$supervisor_id);

        if (!$success) {
            throw new \Exception("Failed to assign supervisor.");
        }

        // Redirect back to the same day's appointments page
        header("Location: " . BASE_URL . "/receptionist/appointments/day?date=" . $date);
        exit;

    } catch (\Throwable $e) {
        // Simple error display
        die("Error: " . $e->getMessage());
    }
}

    // ✅ FIXED: Complete edit method with proper signature
    public function edit($id = null)
{
    if (!$id) {
        header('Location: ' . BASE_URL . '/receptionist/appointments');
        exit;
    }

    $appointmentModel = new AppointmentModel(db());
    $appointment = $appointmentModel->getAppointmentById($id);
    
    // Verify the appointment belongs to receptionist's branch
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    if ($appointment['branch_id'] != $branchId) {
        header('Location: ' . BASE_URL . '/receptionist/appointments');
        exit;
    }
    
    // Get all active branches for dropdown
    $stmt = db()->prepare("SELECT branch_id, name, city FROM branches WHERE status='active' ORDER BY name ASC");
    $stmt->execute();
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all active services for dropdown
    $services = $appointmentModel->getAllServices();
    
    // Get supervisors for the current branch
    $supervisors = $appointmentModel->getSupervisorsByBranch($appointment['branch_id']);

    $this->view('receptionist/Appointments/updateApp', [
        'appointment' => $appointment,
        'branches' => $branches,
        'services' => $services,
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
        $serviceId = $_POST['service_id'] ?? null;
        $branchId = $_POST['branch_id'] ?? null;
        $appointmentDate = $_POST['appointment_date'] ?? null;
        $appointmentTime = $_POST['appointment_time'] ?? null;
        $notes = $_POST['notes'] ?? null;
        
        if (!$appointmentId || !$serviceId || !$branchId || !$appointmentDate || !$appointmentTime) {
            throw new \Exception('All required fields must be filled');
        }
        
        $appointmentModel = new AppointmentModel(db());
        
        // Get original appointment to check if branch changed
        $originalAppointment = $appointmentModel->getAppointmentById($appointmentId);
        
        // Verify the appointment belongs to receptionist's branch
        $receptionistBranchId = $_SESSION['user']['branch_id'] ?? null;
        if ($originalAppointment['branch_id'] != $receptionistBranchId) {
            throw new \Exception('You can only update appointments from your branch');
        }
        
        // Check if branch has changed
        $branchChanged = ($originalAppointment['branch_id'] != $branchId);
        
        // Prepare update data
        $updateData = [
            'appointment_id' => $appointmentId,
            'service_id' => $serviceId,
            'branch_id' => $branchId,
            'appointment_date' => $appointmentDate,
            'appointment_time' => $appointmentTime,
            'notes' => $notes
        ];
        
        // If branch changed, reset assigned_to and set status to 'Requested'
        if ($branchChanged) {
            $updateData['assigned_to'] = null;
            $updateData['status'] = 'Requested';
        } else {
            // Keep existing assigned_to if branch didn't change
            $updateData['assigned_to'] = $originalAppointment['assigned_to'];
            $updateData['status'] = $originalAppointment['status'];
        }
        
        $success = $appointmentModel->updateAppointment($updateData);
        
        if (!$success) {
            throw new \Exception('Failed to update appointment');
        }
        
        echo json_encode([
            'success' => true,
            'message' => 'Appointment updated successfully' . ($branchChanged ? ' (Branch changed - supervisor assignment reset)' : '')
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
