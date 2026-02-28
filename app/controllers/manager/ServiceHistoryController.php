<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class ServiceHistoryController extends Controller
{
    public function index(): void
    {
        // Typically you'd determine branch via the logged-in manager
        $branchId = (int)($_SESSION['branch_id'] ?? 0);

        // TODO: replace with a real model call:
        // $rows = (new \app\model\ServiceHistory())->getByBranch($branchId, $filters);
        // For now, sample data:
        $rows = [
            [
                'date'        => '2025-07-28',
                'time'        => '10:30',
                'vehicle_no'  => 'ABC-1234',
                'vehicle'     => 'Toyota Camry (2018)',
                'customer'    => 'Jane Doe',
                'service'     => 'Oil Change',
                'technician'  => 'John Smith',
                'status'      => 'Completed',
                'cost'        => 15000.00,
            ],
            [
                'date'        => '2025-07-28',
                'time'        => '11:30',
                'vehicle_no'  => 'QRS-9012',
                'vehicle'     => 'Ford Focus (2019)',
                'customer'    => 'Emily Johnson',
                'service'     => 'Inspection',
                'technician'  => 'Bill Hawkins',
                'status'      => 'In Service',
                'cost'        => 0.00,
            ],
            [
                'date'        => '2025-07-27',
                'time'        => '14:00',
                'vehicle_no'  => 'TUV-3456',
                'vehicle'     => 'BMW 3 Series (2017)',
                'customer'    => 'Michael Brown',
                'service'     => 'Tire Rotation',
                'technician'  => 'Mike Johnson',
                'status'      => 'Completed',
                'cost'        => 12000.00,
            ],
            [
                'date'        => '2025-07-27',
                'time'        => '12:00',
                'vehicle_no'  => 'WXY-7890',
                'vehicle'     => 'Audi A4 (2020)',
                'customer'    => 'Sarah Davis',
                'service'     => 'Brake Pad Replacement',
                'technician'  => 'Robert Chen',
                'status'      => 'Canceled',
                'cost'        => 0.00,
            ],
        ];

        // Expose vars to the view
        $pageTitle = 'Service History';
        $branchId  = $branchId;

        require APP_ROOT . '/views/manager/servicehistory/index.php';
    }
}
