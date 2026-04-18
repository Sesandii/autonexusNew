<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ReportModel;
use PDO;

class ReportController extends Controller
{
    private ReportModel $reportModel;

    private function guardManager(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'manager') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        // Also reload if branch_id is null
        if (!isset($_SESSION['user']['branch_id']) || $_SESSION['user']['branch_id'] === null) {
            $stmt = db()->prepare('SELECT branch_id FROM managers WHERE user_id = :uid LIMIT 1');
            $stmt->execute(['uid' => $u['user_id']]);
            $manager = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$manager || empty($manager['branch_id'])) {
                header('Location: ' . rtrim(BASE_URL, '/') . '/login');
                exit;
            }

            $_SESSION['user']['branch_id'] = $manager['branch_id'];
        }
    }

    public function __construct()
    {
        parent::__construct();
        $this->guardManager();
        $this->reportModel = new ReportModel(db());
    }

    public function index(): void
    {
        $step       = isset($_GET['step']) ? (int)$_GET['step'] : 1;
        $reportType = $_GET['report_type'] ?? '';
        $rows       = [];
        $from       = '';
        $to         = '';
        $services   = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reportType = $_POST['report_type'] ?? '';
            $metrics    = $_POST['metrics'] ?? ['total_revenue'];
            $fromDate   = ($_POST['from_date'] ?? date('Y-m-01')) . ' 00:00:00';
            $toDate     = ($_POST['to_date']   ?? date('Y-m-d'))  . ' 23:59:59';
            $from       = substr($fromDate, 0, 10);
            $to         = substr($toDate,   0, 10);
            $branchId   = (int)$_SESSION['user']['branch_id'];
            $serviceId  = !empty($_POST['service_type']) && is_numeric($_POST['service_type'])
                ? (int)$_POST['service_type']
                : null;

            if ($reportType === 'revenue') {
                $rows = $this->reportModel->revenueReport(
                    $fromDate, $toDate, $metrics, $serviceId, $branchId
                );

            } elseif ($reportType === 'pending_services') {
                $rows = $this->reportModel->pendingServices(
                    $fromDate, $toDate, $branchId
                );

            } elseif ($reportType === 'service_completion') {  // ← added
                $rows = $this->reportModel->serviceCompletionReport(
                    $fromDate, $toDate, $branchId
                );
            }

            $step = 3;

        } elseif ($step === 2) {
            // Load dynamic filters per report type
            if ($reportType === 'revenue') {
                $services = $this->reportModel->getServices();
            }
            // service_completion and pending_services only need date range
            // so nothing extra to load for those
        }

        $this->view('Manager/Reports/report', [
            'current_page' => 'reports',
            'step'         => $step,
            'reportType'   => $reportType,
            'rows'         => $rows,
            'from'         => $from,
            'to'           => $to,
            'services'     => $services,
        ]);
    }
}