<?php
namespace app\controllers\mechanic;

use app\core\Controller;
use app\model\supervisor\Appointment;

class HistoryController extends Controller
{
    private $appointmentModel;

    public function __construct()
    {
        $this->requireAdmin();
        // ✅ Use global db() helper which returns the PDO instance
        $this->appointmentModel = new Appointment(db());
    }

    /** 
     * ✅ Show the vehicle history search page 
     */
    public function index()
    {
        $this->view('mechanic/history/index');
    }

    public function show()
    {
        $licensePlate = $_GET['license_plate'] ?? '';
        $fromDate     = $_GET['fromDate'] ?? '';
        $toDate       = $_GET['toDate'] ?? '';
        $vehicle = null;
        $appointments = [];

        if (empty($licensePlate) || empty($fromDate) || empty($toDate)) {
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => 'License plate, From Date and To Date are required.'
            ];
            header('Location: ' . rtrim(BASE_URL, '/') . '/mechanic/history');
            exit;
        } 
        if (!empty($licensePlate)) {
            $vehicle = $this->appointmentModel->getVehicleByLicense($licensePlate);
            $appointments = $this->appointmentModel
        ->getVehicleHistoryByLicenseWithDateRange(
            $licensePlate,
            $fromDate,
            $toDate
        );
    }

        // ✅ Render the history result view
        $this->view('mechanic/history/show', [
            'vehicle' => $vehicle,
            'appointments' => $appointments
        ]);
    }

    

    /**
     * ✅ Show detailed info for a specific appointment
     * Example URL: /mechanic/history/details/12
     */
    public function details($appointmentId)
    {
        $appointmentId = (int)$appointmentId;

        // Get full appointment details (joins: services, vehicle, customer, work_order)
        $details = $this->appointmentModel->getAppointmentDetails($appointmentId);

        if (!$details) {
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => 'Appointment not found.'
            ];
            header('Location: ' . rtrim(BASE_URL, '/') . '/mechanic/history');
            exit;
        }

        // ✅ Render the details view
        $this->view('mechanic/history/details', ['details' => $details]);
    }
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'mechanic')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}

