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
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    
        $branchId = $_SESSION['user']['branch_id'] ?? null;
    
        if (!$branchId) {
            die("Unauthorized: No branch assigned to this supervisor.");
        }
    
        $mechanics  = $this->mechanicModel->getMechanicsByBranch($branchId);
        $issues     = $this->issueModel->getAllIssues();
    
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
            $mechanicCode = $_POST['mechanic_code'] ?? null;
            $status = $_POST['status'] ?? null;
    
            if ($mechanicCode && $status) {
                $updated = $this->mechanicModel->updateStatus($mechanicCode, $status);
    
                if ($updated) {
                    $this->flash('success', 'Status updated.');
                    header('Location: ' . BASE_URL . '/supervisor/coordination');
                    exit;
                }
            }

            header('Location: ' . BASE_URL . '/supervisor/coordination?error=update_failed');
            exit;
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

    private function flash(string $type, string $text): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $_SESSION['message'] = [
        'type' => $type,
        'text' => $text
    ];
}
}
