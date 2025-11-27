<?php
declare(strict_types=1);

namespace app\controllers\mechanic;

use app\core\Controller;
use app\core\Auth;

class DashboardController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireMechanic();
    }
    public function index(): void
    {
        
            // normal dashboard code
        
        
        // Optional guards (use only if you have them)
        // if (method_exists($this, 'requireMechanic')) {
        //     $this->requireMechanic();
        // } elseif (method_exists($this, 'requireRole')) {
        //     $this->requireRole('mechanic');
        // } elseif (method_exists($this, 'requireLogin')) {
        //     $this->requireLogin();
        // }

        // TODO: replace placeholders with real DB data later
        $data = [
            'user_first_name' => $_SESSION['first_name'] ?? 'Mechanic',

            // top cards
            'stats' => [
                'done'     => 128,
                'assigned' => 45,
                'ongoing'  => 32,
                'total'    => 205,
            ],

            // today’s schedule (table)
            'appointments' => [
                ['client' => 'James Wilson',   'vehicle' => 'Toyota Camry (2019)',  'time' => '9:00 AM',  'service' => 'Oil Change',        'status' => 'Upcoming'],
                ['client' => 'Sarah Johnson',  'vehicle' => 'Honda Civic (2020)',   'time' => '10:30 AM', 'service' => 'Tire Rotation',     'status' => 'Upcoming'],
                ['client' => 'Robert Brown',   'vehicle' => 'Ford F-150 (2018)',    'time' => '11:45 AM', 'service' => 'Brake Inspection',  'status' => 'Confirmed'],
                ['client' => 'Emily Davis',    'vehicle' => 'Chevrolet Equinox (2021)','time'=>'2:15 PM','service'=>'Full Inspection',     'status' => 'Confirmed'],
                ['client' => 'Michael Thompson','vehicle' => 'Nissan Altima (2017)','time' => '4:00 PM',  'service' => 'Engine Diagnostics','status' => 'Waiting'],
            ],
        ];

        $this->view('mechanic/dashboard/index', $data);
    }
    private function requireMechanic(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'mechanic')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
