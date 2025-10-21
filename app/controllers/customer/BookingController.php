<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\public\BranchPublic;
use app\model\customer\Vehicles;

class BookingController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireLogin')) {
            $this->requireLogin();
        }

        $branchCode = trim($_GET['branch'] ?? '');
        $bp = new BranchPublic();
        $branches = $bp->allActive();
        $branchName = $branchCode ? ($bp->findNameByCode($branchCode) ?? null) : null;

        // add: load vehicles for logged-in user
        $userId = (int)($_SESSION['user_id'] ?? ($_SESSION['user']['user_id'] ?? 0));
        $vehicles = (new Vehicles())->byUserId($userId);

        $itemsParam = $_GET['items'] ?? '[]';

        $this->view('customer/booking/index', [
            'title'       => 'AutoNexus • Book Service',
            'branches'    => $branches,
            'branch_code' => $branchCode,
            'branch_name' => $branchName,
            'items_param' => $itemsParam,
            'vehicles'    => $vehicles,          // <— NEW
        ]);
    }

    // stub for later
    public function create(): void
    {
        // Validate POST, insert appointment, then redirect with success message.
        http_response_code(501);
        echo 'Booking submission handler not implemented yet.';
    }
}
