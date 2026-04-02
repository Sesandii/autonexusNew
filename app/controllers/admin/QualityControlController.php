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
                CONCAT(su.first_name, ' ', su.last_name) AS supervisor_name
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
            ORDER BY r.created_at DESC
            LIMIT 200
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
                a.appointment_id,
                a.appointment_date,
                a.status AS appointment_status,

                b.name AS branch_name,
                s.name AS service_name,
                v.license_plate,
                CONCAT(cu.first_name, ' ', cu.last_name) AS customer_name
            FROM final_reports fr
            LEFT JOIN work_orders wo ON wo.work_order_id = fr.work_order_id
            LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            LEFT JOIN services s ON s.service_id = a.service_id
            LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
            LEFT JOIN customers c ON c.customer_id = a.customer_id
            LEFT JOIN users cu ON cu.user_id = c.user_id
            ORDER BY fr.created_at DESC
            LIMIT 200
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
        ];

        $ratingSql = "
            SELECT
                COALESCE(quality_rating, 0) AS label,
                COUNT(*) AS total
            FROM reports
            GROUP BY COALESCE(quality_rating, 0)
            ORDER BY label DESC
        ";
        $ratings = $this->pdo->query($ratingSql)->fetchAll(PDO::FETCH_ASSOC);

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

        $this->view('admin/admin-quality/dashboard', [
            'pageTitle' => 'Quality Dashboard',
            'current'   => 'qc-dashboard',
            'summary'   => $summary,
            'ratings'   => $ratings,
            'branches'  => $branches,
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