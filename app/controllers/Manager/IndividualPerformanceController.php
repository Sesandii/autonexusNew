<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\IndividualPerformanceModel;

class IndividualPerformanceController extends Controller
{
    protected IndividualPerformanceModel $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new IndividualPerformanceModel(db());
    }

 public function index()
{
    $mechanic_id = $_GET['mechanic_id'] ?? null;

    if (!$mechanic_id) {
        die('Mechanic ID not provided');
    }

    $mechanicModel = new \app\models\IndividualPerformanceModel();

    $mechanic = $mechanicModel->getById($mechanic_id);
    $workOrders = $mechanicModel->getWorkOrders($mechanic_id);
    $feedback = $mechanicModel->getFeedback($mechanic_id);

    $this->view('manager/individual-performance', [
        'mechanic' => $mechanic,
        'workOrders' => $workOrders,
        'feedback' => $feedback
    ]);
}

}
