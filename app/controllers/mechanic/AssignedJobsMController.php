<?php
namespace app\controllers\mechanic;

use app\core\Controller;

class AssignedJobsMController extends Controller
{
    public function index()
    {
        // Later you can fetch assigned jobs from the database here
        $this->view('mechanic/assignedjobs/index');
    }
}
