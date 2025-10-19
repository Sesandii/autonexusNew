<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\public\BranchPublic;

class RegisteredHomeController extends Controller
{
    public function index(): void
    {
        // Gate (only if you have these helpers)
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        } elseif (method_exists($this, 'requireLogin')) {
            $this->requireLogin();
        }

        $branches = (new BranchPublic())->allActive();

        $this->view('customer/registeredhome/index', [
            'title'     => 'AutoNexus — Vehicle Service Management',
            'user_name' => $_SESSION['first_name'] ?? 'Customer',
            'branches'  => $branches,
        ]);
    }
}
