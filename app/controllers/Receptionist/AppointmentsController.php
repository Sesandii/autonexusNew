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
        foreach (['customer_id','vehicle_id','branch_id','service_id','appointment_date','appointment_time'] as $key) {
            if (empty($data[$key])) {
                throw new \Exception("Missing or invalid field: $key");
            }
        }

        // Save appointment
        $success = $appointmentModel->saveAppointment($data);

        if (!$success) {
            throw new \Exception('Failed to save appointment due to database error.');
        }

        // Always return JSON
        echo json_encode([
            'success' => true,
            'message' => 'Appointment saved successfully'
        ]);

    } catch (\Throwable $e) {
        // Return JSON even on error
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

public function edit($id)
{
    $appointmentModel = new AppointmentModel(db());

    $appointment = $appointmentModel->getAppointmentById($id);
    $services = $appointmentModel->getAllServices();

    $stmt = db()->prepare("SELECT branch_id, name FROM branches WHERE status='active' ORDER BY name ASC");
    $stmt->execute();
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 👇 Use your existing method
    $supervisors = $appointmentModel->getSupervisorsByBranch($appointment['branch_id']);

    $this->view('receptionist/Appointments/updateApp', [
        'appointment' => $appointment,
        'services' => $services,
        'branches' => $branches,
        'supervisors' => $supervisors
    ]);
}

public function update(): void
{
    header('Content-Type: application/json');

    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception("Invalid request method");
        }

        $appointmentModel = new AppointmentModel(db());

        $data = [
            'appointment_id'   => $_POST['appointment_id'] ?? null,
            'service_id'       => $_POST['service_id'] ?? null,
            'branch_id'        => $_POST['branch_id'] ?? null,
            'appointment_date' => $_POST['appointment_date'] ?? null,
            'appointment_time' => $_POST['appointment_time'] ?? null,
            'status'           => $_POST['status'] ?? null,
            'notes'            => $_POST['notes'] ?? null,
            'assigned_to'      => $_POST['assigned_to'] ?? null
        ];

        foreach (['appointment_id','service_id','branch_id','appointment_date','appointment_time','status'] as $field) {
            if (empty($data[$field])) {
                throw new \Exception("Missing field: $field");
            }
        }

        $success = $appointmentModel->updateAppointment($data);

        if (!$success) {
            throw new \Exception("Failed to update appointment.");
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
