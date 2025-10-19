<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class AppointmentsController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/admin-appointments */
    public function index(): void
    {
        // Dummy data
        $appointments = [
            ['id'=>1,'customer'=>'John Smith','service'=>'Oil Change','branch'=>'Downtown Branch','datetime'=>'2025-11-10 09:00:00','status'=>'Scheduled'],
            ['id'=>2,'customer'=>'Sarah Williams','service'=>'Brake Inspection','branch'=>'Uptown Branch','datetime'=>'2025-11-10 10:30:00','status'=>'In Progress'],
            ['id'=>3,'customer'=>'Robert Brown','service'=>'Tire Rotation','branch'=>'Midtown Branch','datetime'=>'2025-11-10 13:15:00','status'=>'Completed'],
        ];

        // IMPORTANT: pass the data
        $this->view('admin/admin-appointments/index', [
            'appointments' => $appointments,
            'pageTitle'    => 'Appointments - AutoNexus',
            'current'      => 'appointments',
        ]);
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || ($u['role'] ?? '') !== 'admin') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login'); exit;
        }
    }
}
