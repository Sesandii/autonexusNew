<?php
namespace app\controllers\Manager;

use app\core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->view('Manager/Dashboard/dashboard');
    }

}
