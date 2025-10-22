<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class VehicleReportsController extends Controller
{
    public function index()
    {
        // Later you can add database logic via a Report model
        $this->view('supervisor/reports/index');
    }
}
