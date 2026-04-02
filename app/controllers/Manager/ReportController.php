<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ReportModel;
use PDO;

class ReportController extends Controller
{
    private ReportModel $reportModel;

    public function __construct()
    {
        $this->reportModel = new ReportModel(db());
    }

    // Show initial report page
    public function index(): void
    {
        $this->view('Manager/Reports/report', [
            'current_page' => 'reports',
            'reportType'   => '',
            'rows'         => [],
            'from'         => '',
            'to'           => ''
        ]);
    }

    // AJAX endpoint to fetch dynamic filters based on report type
    public function getFilters(): void
    {
        $reportType = $_GET['report'] ?? '';
        $data = [];

        switch ($reportType) {
            case 'revenue':
                $data['services'] = $this->reportModel->getServices();
                break;

            case 'pending_services':
                $data['statuses'] = ['pending', 'overdue'];
                break;

            default:
                $data = [];
        }

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    // Generate report based on submitted filters
   public function generate(): void
{
    $reportType = $_POST['report_type'] ?? '';
    $metrics    = $_POST['metrics'] ?? [];

    $fromDate = ($_POST['from_date'] ?? date('Y-m-01')) . ' 00:00:00';
    $toDate   = ($_POST['to_date'] ?? date('Y-m-d')) . ' 23:59:59';

    $rows = [];

    if ($reportType === 'revenue') {
        $serviceId = !empty($_POST['service_type']) && is_numeric($_POST['service_type'])
            ? (int)$_POST['service_type']
            : null;

        $rows = $this->reportModel->revenueReport(
            $fromDate,
            $toDate,
            $metrics,
            $serviceId
        );
    }

    header('Content-Type: text/html');
    echo $this->view('Manager/Reports/result', [
        'reportType' => $reportType,
        'rows'       => $rows,
        'from'       => substr($fromDate, 0, 10),
        'to'         => substr($toDate, 0, 10),
    ], true);
}


    // Optional: view report page directly
    public function result(): void
    {
        $reportType  = $_POST['report_type'] ?? $_GET['report_type'] ?? '';
        $serviceType = $_POST['service_type'] ?? $_GET['service_type'] ?? '';
        $metrics     = $_POST['metrics'] ?? ['total_revenue'];
        $fromDate    = $_POST['from_date'] ?? $_GET['from_date'] ?? date('Y-m-01');
        $toDate      = $_POST['to_date'] ?? $_GET['to_date'] ?? date('Y-m-t');
        $serviceId   = is_numeric($serviceType) ? (int)$serviceType : null;

        $rows = [];

        if ($reportType === 'revenue') {
            $rows = $this->reportModel->revenueReport($fromDate, $toDate, $metrics, $serviceId);
        } elseif ($reportType === 'pending_services') {
            $statuses = $_POST['status'] ?? ['pending','overdue'];
            $rows = $this->reportModel->pendingServices($fromDate, $toDate, $statuses);
        }

        $this->view('Manager/Reports/result', [
            'reportType' => $reportType,
            'rows'       => $rows,
            'from'       => $fromDate,
            'to'         => $toDate
        ]);
    }
}
