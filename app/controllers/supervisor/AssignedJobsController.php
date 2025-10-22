<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class AssignedJobsController extends Controller
{
    public function index()
    {
        // In future, load assigned jobs using a model (e.g., Job model)
        $this->view('supervisor/assignedjobs/index');
    }
}
