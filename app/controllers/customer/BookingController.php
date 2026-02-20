<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\public\BranchPublic;
use app\model\public\ServicePublic;
use app\model\customer\Vehicles;
use app\model\customer\Appointments;

class BookingController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireLogin')) $this->requireLogin();

        $branchCode  = trim($_GET['branch'] ?? '');
        $bp          = new BranchPublic();
        $branches    = $bp->allActive();
        $branchName  = $branchCode ? ($bp->findNameByCode($branchCode) ?? null) : null;

        $userId   = (int)($this->userId());
        $vehicles = (new Vehicles())->byUserId($userId);

        // services for selected branch (server-rendered)
        $services = [];
        if ($branchCode !== '') {
            $services = (new ServicePublic())->byBranchCode($branchCode);
        }

        $this->view('customer/booking/index', [
            'title'       => 'AutoNexus • Book Service',
            'branches'    => $branches,
            'branch_code' => $branchCode,
            'branch_name' => $branchName,
            'vehicles'    => $vehicles,
            'services'    => $services,
            'flash'       => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        if (method_exists($this, 'requireLogin')) $this->requireLogin();

        $userId     = (int)$this->userId();
        $branchCode = trim($_POST['branch_code'] ?? '');
        $vehicleId  = (int)($_POST['vehicle_id'] ?? 0);
        $serviceId  = (int)($_POST['service_id'] ?? 0);
        $dateYmd    = trim($_POST['date'] ?? '');
        $time       = trim($_POST['time'] ?? '');

        // very light validation
        if (!$branchCode || !$vehicleId || !$serviceId || !$dateYmd || !$time) {
            $_SESSION['flash'] = 'Please complete all fields.';
            header('Location: ' . $this->baseUrl() . '/customer/book?branch=' . urlencode($branchCode));
            return;
        }

        [$ok, $msg] = (new Appointments())->createBooking(
            $userId, $branchCode, $vehicleId, $serviceId, $dateYmd, $time
        );

        $_SESSION['flash'] = $msg;
        // on success go to appointments page
        $dest = $ok ? '/customer/appointments' : '/customer/book?branch=' . urlencode($branchCode);
        header('Location: ' . $this->baseUrl() . $dest);
    }

    
}
