<?php
namespace app\controllers\Receptionist;

use app\core\Controller;

class AppointmentsController extends Controller
{
    public function index(): void
    {
        // You can pass dynamic data later if needed
        $appointments = []; // placeholder for future DB data

        // Load the view
        $this->view('Receptionist/Appointments/Appointment', [
            'appointments' => $appointments
        ]);
    }

    public function create()
    {
        $this->view('Receptionist/Appointments/newAppointment');
    }

    public function day()
{
    $date = $_GET['date'] ?? null;
    if (!$date) {
        header("Location: " . BASE_URL . "/receptionist/appointments");
        exit;
    }

    // Example appointments
    $appointments = [
        ['id'=>1,'time'=>'10:30','customer'=>'Jane Doe','vehicle_number'=>'ABC-1212','vehicle'=>'Toyota Camry','service'=>'Oil Change','status'=>'Not Arrived'],
        ['id'=>2,'time'=>'11:00','customer'=>'John Smith','vehicle_number'=>'XYZ-5678','vehicle'=>'Honda Civic','service'=>'Inspection','status'=>'Waiting'],
        ['id'=>3,'time'=>'11:30','customer'=>'Emily Johnson','vehicle_number'=>'QRS-9012','vehicle'=>'Ford Focus','service'=>'Inspection','status'=>'In Service'],
    ];

    $this->view('Receptionist/Appointments/dayAppointment', [
        'appointments' => $appointments,
        'date' => $date
    ]);
}


public function edit($id)
{
    // Fetch appointment by ID from DB
    $appointment = [
        'id' => $id,
        'customer' => 'Jane Doe',
        'vehicle_number' => 'ABC-1212',
        'vehicle' => 'Toyota Camry',
        'service' => 'Oil Change',
        'status' => 'Not Arrived'
    ];

    $this->view('Receptionist/Appointments/updateApp', [
        'appointment' => $appointment
    ]);
}


}
