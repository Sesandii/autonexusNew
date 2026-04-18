<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use PDO;

class QualityControlController extends Controller
{
    private PDO $pdo;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->pdo = db();
    }

    public function inspectionReports(): void
    {
        $sql = "
            SELECT
                r.report_id,
                r.work_order_id,
                r.supervisor_id,
                r.inspection_notes,
                r.quality_rating,
                r.checklist_verified,
                r.test_driven,
                r.concerns_addressed,
                r.report_summary,
                r.next_service_recommendation,
                r.status,
                r.created_at,
                r.updated_at,

                wo.work_order_id,
                wo.mechanic_id,

                a.appointment_id,
                a.appointment_date,
                a.status AS appointment_status,

                b.name AS branch_name,
                b.branch_code,

                s.name AS service_name,
                s.service_code,

                v.license_plate,
                v.make,
                v.model,

                CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name,
                CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name,
                CONCAT(mu.first_name, ' ', mu.last_name) AS mechanic_name,

                (
                    SELECT COUNT(*)
                    FROM report_photos rp
                    WHERE rp.report_id = r.report_id
                ) AS report_photo_count
            FROM reports r
            LEFT JOIN work_orders wo ON wo.work_order_id = r.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            LEFT JOIN services s ON s.service_id = a.service_id
            LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
            LEFT JOIN customers c ON c.customer_id = a.customer_id
            LEFT JOIN users cu ON cu.user_id = c.user_id
            LEFT JOIN supervisors sp ON sp.supervisor_id = r.supervisor_id
            LEFT JOIN users su ON su.user_id = sp.user_id
            LEFT JOIN mechanics m ON m.mechanic_id = wo.mechanic_id
            LEFT JOIN users mu ON mu.user_id = m.user_id
            ORDER BY r.created_at DESC
            LIMIT 300
        ";

        $records = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/admin-quality/inspection-reports', [
            'pageTitle' => 'Inspection Reports',
            'current'   => 'qc-reports',
            'records'   => $records,
        ]);
    }

    public function finalApprovals(): void
    {
        $sql = "
            SELECT
                fr.report_id,
                fr.work_order_id,
                fr.report_details,
                fr.recommendations,
                fr.created_at,

                wo.work_order_id,
                wo.mechanic_id,

                a.appointment_id,
                a.appointment_date,
                a.status AS appointment_status,

                b.name AS branch_name,
                s.name AS service_name,
                v.license_plate,
                CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name,
                CONCAT(mu.first_name, ' ', mu.last_name) AS mechanic_name,

                r.quality_rating,
                r.status AS inspection_status,
                r.updated_at AS inspection_updated_at,
                CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name
            FROM final_reports fr
            LEFT JOIN work_orders wo ON wo.work_order_id = fr.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            LEFT JOIN services s ON s.service_id = a.service_id
            LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
            LEFT JOIN customers c ON c.customer_id = a.customer_id
            LEFT JOIN users cu ON cu.user_id = c.user_id
            LEFT JOIN mechanics m ON m.mechanic_id = wo.mechanic_id
            LEFT JOIN users mu ON mu.user_id = m.user_id
            LEFT JOIN reports r ON r.work_order_id = fr.work_order_id
            LEFT JOIN supervisors sp ON sp.supervisor_id = r.supervisor_id
            LEFT JOIN users su ON su.user_id = sp.user_id
            ORDER BY fr.created_at DESC
            LIMIT 300
        ";

        $records = $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/admin-quality/final-approvals', [
            'pageTitle' => 'Final Approvals',
            'current'   => 'qc-approvals',
            'records'   => $records,
        ]);
    }

    public function dashboard(): void
    {
        $summary = [
            'reports_total' => (int)$this->pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn(),
            'reports_draft' => (int)$this->pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'draft'")->fetchColumn(),
            'reports_submitted' => (int)$this->pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'submitted'")->fetchColumn(),
            'final_reports_total' => (int)$this->pdo->query("SELECT COUNT(*) FROM final_reports")->fetchColumn(),
            'checklists_total' => (int)$this->pdo->query("SELECT COUNT(*) FROM checklist")->fetchColumn(),
            'photos_total' => (int)$this->pdo->query("SELECT (SELECT COUNT(*) FROM report_photos) + (SELECT COUNT(*) FROM service_photos)")->fetchColumn(),

            'low_rated_total' => (int)$this->pdo->query("
                SELECT COUNT(*)
                FROM reports
                WHERE COALESCE(quality_rating, 0) <= 2
            ")->fetchColumn(),

            'failed_checklist_total' => (int)$this->pdo->query("
                SELECT COUNT(*)
                FROM reports
                WHERE COALESCE(checklist_verified, 0) = 0
            ")->fetchColumn(),

            'reinspection_queue_total' => (int)$this->pdo->query("
                SELECT COUNT(*)
                FROM reports
                WHERE COALESCE(quality_rating, 0) <= 2
                   OR COALESCE(checklist_verified, 0) = 0
                   OR COALESCE(test_driven, 0) = 0
                   OR COALESCE(concerns_addressed, 0) = 0
            ")->fetchColumn(),
        ];

        $checklistCompliance = (float)$this->pdo->query("
            SELECT COALESCE(AVG(CASE WHEN checklist_verified = 1 THEN 100 ELSE 0 END), 0)
            FROM reports
        ")->fetchColumn();

        $testDriveRate = (float)$this->pdo->query("
            SELECT COALESCE(AVG(CASE WHEN test_driven = 1 THEN 100 ELSE 0 END), 0)
            FROM reports
        ")->fetchColumn();

        $concernRate = (float)$this->pdo->query("
            SELECT COALESCE(AVG(CASE WHEN concerns_addressed = 1 THEN 100 ELSE 0 END), 0)
            FROM reports
        ")->fetchColumn();

        $ratingSql = "
            SELECT
                COALESCE(quality_rating, 0) AS label,
                COUNT(*) AS total
            FROM reports
            GROUP BY COALESCE(quality_rating, 0)
            ORDER BY label DESC
        ";
        $ratings = $this->pdo->query($ratingSql)->fetchAll(PDO::FETCH_ASSOC);

        $statusSql = "
            SELECT
                COALESCE(status, 'unknown') AS label,
                COUNT(*) AS total
            FROM reports
            GROUP BY COALESCE(status, 'unknown')
            ORDER BY total DESC, label ASC
        ";
        $statuses = $this->pdo->query($statusSql)->fetchAll(PDO::FETCH_ASSOC);

        $branchSql = "
            SELECT
                COALESCE(b.name, 'Unknown Branch') AS label,
                COUNT(r.report_id) AS total
            FROM reports r
            LEFT JOIN work_orders wo ON wo.work_order_id = r.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            GROUP BY COALESCE(b.name, 'Unknown Branch')
            ORDER BY total DESC, label ASC
        ";
        $branches = $this->pdo->query($branchSql)->fetchAll(PDO::FETCH_ASSOC);

        $branchScoreSql = "
            SELECT
                COALESCE(b.name, 'Unknown Branch') AS label,
                ROUND(COALESCE(AVG(r.quality_rating), 0), 2) AS total
            FROM reports r
            LEFT JOIN work_orders wo ON wo.work_order_id = r.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            GROUP BY COALESCE(b.name, 'Unknown Branch')
            ORDER BY total DESC, label ASC
        ";
        $branchScores = $this->pdo->query($branchScoreSql)->fetchAll(PDO::FETCH_ASSOC);

        $mechanicOutcomeSql = "
            SELECT
                COALESCE(CONCAT(mu.first_name, ' ', mu.last_name), 'Unassigned Mechanic') AS label,
                ROUND(COALESCE(AVG(r.quality_rating), 0), 2) AS total
            FROM reports r
            LEFT JOIN work_orders wo ON wo.work_order_id = r.work_order_id
            LEFT JOIN mechanics m ON m.mechanic_id = wo.mechanic_id
            LEFT JOIN users mu ON mu.user_id = m.user_id
            GROUP BY COALESCE(CONCAT(mu.first_name, ' ', mu.last_name), 'Unassigned Mechanic')
            ORDER BY total DESC, label ASC
            LIMIT 10
        ";
        $mechanics = $this->pdo->query($mechanicOutcomeSql)->fetchAll(PDO::FETCH_ASSOC);

        $supervisorOutcomeSql = "
            SELECT
                COALESCE(CONCAT(su.first_name, ' ', su.last_name), 'Unknown Supervisor') AS label,
                ROUND(COALESCE(AVG(r.quality_rating), 0), 2) AS total
            FROM reports r
            LEFT JOIN supervisors sp ON sp.supervisor_id = r.supervisor_id
            LEFT JOIN users su ON su.user_id = sp.user_id
            GROUP BY COALESCE(CONCAT(su.first_name, ' ', su.last_name), 'Unknown Supervisor')
            ORDER BY total DESC, label ASC
            LIMIT 10
        ";
        $supervisors = $this->pdo->query($supervisorOutcomeSql)->fetchAll(PDO::FETCH_ASSOC);

        $photoBranchSql = "
            SELECT
                COALESCE(b.name, 'Unknown Branch') AS label,
                COUNT(rp.photo_id) AS total
            FROM report_photos rp
            LEFT JOIN reports r ON r.report_id = rp.report_id
            LEFT JOIN work_orders wo ON wo.work_order_id = r.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            GROUP BY COALESCE(b.name, 'Unknown Branch')
            ORDER BY total DESC, label ASC
        ";
        $photoByBranch = $this->pdo->query($photoBranchSql)->fetchAll(PDO::FETCH_ASSOC);

        $finalBranchSql = "
            SELECT
                COALESCE(b.name, 'Unknown Branch') AS label,
                COUNT(fr.report_id) AS total
            FROM final_reports fr
            LEFT JOIN work_orders wo ON wo.work_order_id = fr.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            GROUP BY COALESCE(b.name, 'Unknown Branch')
            ORDER BY total DESC, label ASC
        ";
        $finalByBranch = $this->pdo->query($finalBranchSql)->fetchAll(PDO::FETCH_ASSOC);

        $failedCasesSql = "
            SELECT
                r.report_id,
                r.quality_rating,
                r.checklist_verified,
                r.test_driven,
                r.concerns_addressed,
                r.status,
                r.created_at,
                COALESCE(b.name, 'Unknown Branch') AS branch_name,
                COALESCE(s.name, 'Unknown Service') AS service_name,
                COALESCE(CONCAT(cu.first_name, ' ', cu.last_name), 'Unknown Customer') AS customer_name
            FROM reports r
            LEFT JOIN work_orders wo ON wo.work_order_id = r.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            LEFT JOIN services s ON s.service_id = a.service_id
            LEFT JOIN customers c ON c.customer_id = a.customer_id
            LEFT JOIN users cu ON cu.user_id = c.user_id
            WHERE COALESCE(r.quality_rating, 0) <= 2
               OR COALESCE(r.checklist_verified, 0) = 0
               OR COALESCE(r.test_driven, 0) = 0
               OR COALESCE(r.concerns_addressed, 0) = 0
            ORDER BY r.created_at DESC
            LIMIT 10
        ";
        $failedCases = $this->pdo->query($failedCasesSql)->fetchAll(PDO::FETCH_ASSOC);

        $reinspectionQueueSql = "
            SELECT
                r.report_id,
                r.status,
                r.quality_rating,
                r.checklist_verified,
                r.test_driven,
                r.concerns_addressed,
                r.updated_at,
                COALESCE(b.name, 'Unknown Branch') AS branch_name,
                COALESCE(CONCAT(cu.first_name, ' ', cu.last_name), 'Unknown Customer') AS customer_name
            FROM reports r
            LEFT JOIN work_orders wo ON wo.work_order_id = r.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            LEFT JOIN customers c ON c.customer_id = a.customer_id
            LEFT JOIN users cu ON cu.user_id = c.user_id
            WHERE COALESCE(r.quality_rating, 0) <= 2
               OR COALESCE(r.checklist_verified, 0) = 0
               OR COALESCE(r.test_driven, 0) = 0
               OR COALESCE(r.concerns_addressed, 0) = 0
            ORDER BY r.updated_at DESC, r.created_at DESC
            LIMIT 10
        ";
        $reinspectionQueue = $this->pdo->query($reinspectionQueueSql)->fetchAll(PDO::FETCH_ASSOC);

        $this->view('admin/admin-quality/dashboard', [
            'pageTitle'          => 'Quality Dashboard',
            'current'            => 'qc-dashboard',
            'summary'            => $summary,
            'ratings'            => $ratings,
            'statuses'           => $statuses,
            'branches'           => $branches,
            'branchScores'       => $branchScores,
            'mechanics'          => $mechanics,
            'supervisors'        => $supervisors,
            'photoByBranch'      => $photoByBranch,
            'finalByBranch'      => $finalByBranch,
            'failedCases'        => $failedCases,
            'reinspectionQueue'  => $reinspectionQueue,
            'checklistCompliance'=> $checklistCompliance,
            'testDriveRate'      => $testDriveRate,
            'concernRate'        => $concernRate,
        ]);
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}