<?php
// app/controllers/customer/AvailableServicesController.php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\public\BranchPublic;
use app\model\public\ServicePublic;

/**
 * Shows branch-specific services available for customer booking.
 */
class AvailableServicesController extends Controller
{
    /**
     * Render available services for a selected branch code.
     */
    public function index(): void
    {
        if (method_exists($this, 'requireLogin')) {
            $this->requireLogin();
        }

        $code = trim($_GET['branch'] ?? '');
        $bp   = new BranchPublic();
        $sp   = new ServicePublic();

        $branchName = $code ? ($bp->findNameByCode($code) ?? $code) : null;
        $services   = $code ? $sp->byBranchCode($code) : [];

        $this->view('customer/available-services/index', [
            'branch_code' => $code,
            'branch_name' => $branchName,
            'services'    => $services,     // <-- pass to view
            'title'       => 'AutoNexus • Available Services',
        ]);
    }
}
