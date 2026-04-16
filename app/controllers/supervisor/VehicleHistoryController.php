<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Appointment;

class VehicleHistoryController extends Controller
{
    private $appointmentModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->appointmentModel = new Appointment(db());
    }

    public function index()
    {
        $this->view('supervisor/history/index');
    }

    public function show()
{
    $licensePlate = $_GET['license_plate'] ?? '';
    $fromDate     = $_GET['fromDate'] ?? '';
    $toDate       = $_GET['toDate'] ?? '';

    if (empty($licensePlate) || empty($fromDate) || empty($toDate)) {
        $_SESSION['message'] = [
            'type' => 'error',
            'text' => 'License plate, From Date and To Date are required.'
        ];
        header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/history');
        exit;
    }

    $vehicle = $this->appointmentModel->getVehicleByLicense($licensePlate);

    $appointments = $this->appointmentModel
        ->getVehicleHistoryByLicenseWithDateRange(
            $licensePlate,
            $fromDate,
            $toDate
        );

    $this->view('supervisor/history/show', [
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
            header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/history');
            exit;
        }

        $this->view('supervisor/history/details', ['details' => $details]);
    }
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
