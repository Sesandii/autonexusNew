<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class VehicleHistoryController extends Controller
{
    public function index()
    {
        // Later you can load vehicle data using a Vehicle model
        $this->view('supervisor/history/index');
    }
}
