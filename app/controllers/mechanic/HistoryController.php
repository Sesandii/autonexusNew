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
        $this->appointmentModel = new Appointment(db());
    }

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

        $this->view('mechanic/history/show', [
            'vehicle' => $vehicle,
            'appointments' => $appointments
        ]);
    }

    
    public function details($appointmentId)
    {
        $appointmentId = (int)$appointmentId;

        $details = $this->appointmentModel->getAppointmentDetails($appointmentId);

        if (!$details) {
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => 'Appointment not found.'
            ];
            header('Location: ' . rtrim(BASE_URL, '/') . '/mechanic/history');
            exit;
        }

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

