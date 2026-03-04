<?php

namespace app\model\supervisor;

use PDO;

class Issue {
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // existing helper
    }

    public function reportIssue($mechanicId, $jobId, $description) {
        $stmt = $this->pdo->prepare("INSERT INTO mechanic_issues(mechanic_id, work_order_id, issue_description) VALUES(?,?,?)");
        return $stmt->execute([$mechanicId, $workOrderId, $description]);
    }

    public function getAllIssues() {
        $stmt = $this->pdo->query("SELECT i.*, m.mechanic_code, w.service_summary as job_desc
                                  FROM mechanic_issues i
                                  LEFT JOIN mechanics m ON i.mechanic_id = m.mechanic_id
                                  LEFT JOIN work_orders w ON i.work_order_id = w.work_order_id");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
