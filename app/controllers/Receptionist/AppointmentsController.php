<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\AppointmentModel;

class AppointmentsController extends Controller
{

    public function index()
{
    // If you want to fetch real data:
    // $appointments = $this->appointmentModel->getAllAppointments();

    // For now, use dummy data or leave empty
    $appointments = [];

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
    $date = $_GET['date'] ?? date('Y-m-d'); // default today if no date
    $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());
    $appointments = $appointmentModel->getAppointmentsByDate($date);

    $this->view('receptionist/Appointments/dayAppointment', [
        'appointments' => $appointments,
        'date' => $date
    ]);
}



public function edit($id)
{


    $this->view('receptionist/Appointments/updateApp', [
        'appointment' => $appointment
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

        if (!$date) {
            throw new \Exception("Date is required");
        }

        $appointmentModel = new \app\model\Receptionist\AppointmentModel(db());

        // Fetch appointments for the given date
        $appointments = $appointmentModel->getAppointmentsByDate($date);

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



}
?>
