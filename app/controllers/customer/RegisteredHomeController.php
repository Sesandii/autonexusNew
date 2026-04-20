<?php
// app/controllers/customer/RegisteredHomeController.php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\public\BranchPublic;

/**
 * Renders the registered customer home page and active branches.
 */
class RegisteredHomeController extends Controller
{
    /**
     * Show customer home with branch quick-access cards.
     */
    public function index(): void
    {
        if (method_exists($this, 'requireLogin')) {
            $this->requireLogin();
        }

        $branches = (new BranchPublic())->allActive(); // branch_code, branch_name

        // app/controllers/customer/RegisteredHomeController.php
$this->view('customer/registeredhome/index', [
    'title'    => 'AutoNexus • Home',
    'branches' => (new \app\model\public\BranchPublic())->allActive(),
]);

    }
}
