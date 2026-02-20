<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Appointment;

class AppointmentsController extends Controller
{
    private $appointmentModel;

    public function __construct()
    {
        $this->requireAdmin();
        $this->appointmentModel = new Appointment();
    }

    public function index()
    {
        $appointments = $this->appointmentModel->all();
        $this->render('supervisor/appointments/index', ['appointments' => $appointments]);
    }

    public function show($id)
    {
        $appointment = $this->appointmentModel->find($id);
        $this->render('supervisor/appointments/show', ['appointment' => $appointment]);
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
