<?php

namespace app\controllers\supervisor;

use app\model\supervisor\Mechanic;
use app\model\supervisor\WorkOrder;
use app\model\supervisor\Issue;

class CoordinationController {

    private $mechanicModel;
    private $workOrderModel;
    private $issueModel;

    public function __construct() {
        $this->mechanicModel = new Mechanic();
        $this->workOrderModel = new WorkOrder();
        $this->issueModel = new Issue();
    }

    public function index() {

        $mechanics  = $this->mechanicModel->getAllMechanics();
        $issues     = $this->issueModel->getAllIssues();
    
        // Apply filters
        $mechanic_code   = $_GET['mechanic_code'] ?? null;
        $specialization  = $_GET['specialization'] ?? null;
        $status          = $_GET['status'] ?? null;
    
        $mechanics = array_filter($mechanics, function($m) use ($mechanic_code, $specialization, $status) {
            $ok = true;
            if ($mechanic_code && stripos($m['mechanic_code'], $mechanic_code) === false) $ok = false;
            if ($specialization && $m['specialization'] != $specialization) $ok = false;
            if ($status && $m['status'] != $status) $ok = false;
            return $ok;
        });
    
        // Attach scheduled work orders to each mechanic
        foreach ($mechanics as &$mech) {
            $mech['scheduled_orders'] = $this->workOrderModel
    ->getScheduledWorkOrdersByMechanicCode($mech['mechanic_code']);
        }
        unset($mech);
    
        require "../app/views/supervisor/coordination/coordination.php";
    }
    

    public function assignWorkOrder() {
        $this->workOrderModel->assignMechanic($_POST['work_order_id'], $_POST['mechanic_id']);
        header("Location: /supervisor/coordination");
    }

    public function updateMechanicStatus()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $mechanicId = $_POST['mechanic_id'];
            $status = $_POST['status'];

            // Use the already initialized model
            $updated = $this->mechanicModel->updateStatus($mechanicId, $status);

            if ($updated) {
                header('Location: /autonexus/supervisor/coordination');
                exit;
            } else {
                echo "Failed to update status!";
            }
        }
    }

    public function reportIssue() {
        $this->issueModel->reportIssue(
            $_POST['mechanic_id'],
            $_POST['work_order_id'],
            $_POST['issue_note']
        );
        header("Location: /autonexus/supervisor/coordination");
    }    
}
