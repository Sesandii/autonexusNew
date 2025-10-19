<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Controller;
use app\model\public\BranchPublic;

class HomeController extends Controller
{
    public function index(): void
    {
        // Read-only branches for the “Select Your Branch” modal
        $branchModel = new BranchPublic();
        $branches    = $branchModel->allActive();   // [['branch_code'=>'GL','branch_name'=>'Galle'], ...]

        $this->view('home/index', [
            'branches' => $branches,
            'title'    => 'AutoNexus — Vehicle Service Management',
        ]);
    }
}
