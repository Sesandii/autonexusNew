<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\Report;
use app\model\supervisor\WorkOrder;

class VehicleReportsController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

public function index()
{
    $this->view('supervisor/reports/index'); 
}

public function indexp()
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $userId = $_SESSION['user']['user_id'] ?? null;

    $db = db();
    $stmt = $db->prepare("SELECT supervisor_id FROM supervisors WHERE user_id = ?");
    $stmt->execute([$userId]);
    $supervisor = $stmt->fetch();
    $realSupervisorId = $supervisor['supervisor_id'] ?? 0;

    $model = new Report();
    $reports = $model->all();

    $this->view('supervisor/reports/indexp', [
        'reports' => $reports,
        'currentSupervisorId' => $realSupervisorId 
    ]);
}

    public function create()
{
    $woModel = new WorkOrder();

    $workOrderId = $_GET['id'] ?? null;

    if ($workOrderId) {
        $workOrder = $woModel->find((int)$workOrderId);

        if (!$workOrder || $workOrder['status'] !== 'completed') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports');
            exit;
        }

        $services = $woModel->getServiceSummaryFromChecklist((int)$workOrderId);

        $this->view('supervisor/reports/create', [
            'workOrder' => $workOrder,
            'services'  => $services
        ]);

    } else {
        $branchId = $_SESSION['user']['branch_id'] ?? null; 

        if (!$branchId) {
            $db = db();
            $stmt = $db->prepare("SELECT branch_id FROM supervisors WHERE user_id = ?");
            $stmt->execute([$_SESSION['user']['user_id']]);
            $branchId = $stmt->fetchColumn();
        }

        $completedOrders = $woModel->getCompletedWorkOrdersWithoutReport((int)$branchId);

        $this->view('supervisor/reports/create', [
            'completedOrders' => $completedOrders
        ]);
    }
}

    public function store()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        $userId = $_SESSION['user']['user_id'] ?? null;
        $workOrderId = $_POST['work_order_id'] ?? null;
        $db = db();
        $model = new Report();
    
        $stmt = $db->prepare("SELECT supervisor_id FROM supervisors WHERE user_id = ?");
        $stmt->execute([$userId]);
        $supervisor = $stmt->fetch();
        $realSupervisorId = $supervisor['supervisor_id'] ?? null;
    
        if (!$realSupervisorId) {
            die('Supervisor record not found.');
        }
    
$reportId = $model->create([
    'work_order_id'      => $workOrderId,
    'supervisor_id'      => $realSupervisorId,
    'inspection_notes'   => $_POST['inspection_notes'] ?? '',
    'quality_rating'     => (int)($_POST['quality_rating'] ?? 0),
    'checklist_verified' => in_array('tasks_verified', $_POST['checklist'] ?? []) ? 1 : 0,
    'test_driven'        => in_array('test_driven', $_POST['checklist'] ?? []) ? 1 : 0,
    'concerns_addressed' => in_array('concerns_addressed', $_POST['checklist'] ?? []) ? 1 : 0,
    'report_summary'     => $_POST['report_summary'] ?? '',
    'status'             => $_POST['status']
]);

$status = $_POST['status'] ?? 'draft';

if ($status === 'submitted' || $status === 'draft') {
    
    $current  = (int)($_POST['current_mileage'] ?? 0);
        $interval = (int)($_POST['service_interval'] ?? 5000);
        $nextDue  = $current + $interval;

    $model->updateVehicleServiceData(
        (int)$workOrderId, 
        $nextDue, 
        $interval
    );
}

if (!empty($_FILES['work_images']['name'][0])) {
    $uploadDir = __DIR__ . '/../../../public/assets/img/report_photos/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    foreach ($_FILES['work_images']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['work_images']['error'][$key] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['work_images']['name'][$key], PATHINFO_EXTENSION);
            $fileName = uniqid('report_') . '.' . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                $model->savePhoto(
                    $reportId, 
                    'assets/img/report_photos/' . $fileName
                );
            }
        }
    }
}
        $this->flash('success', 'Report Created.');
        header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports/indexp');
        exit;
    }

    public function delete($id)
    {
        $model = new Report();
        $model->delete((int)$id);
        $this->flash('success', 'Report deleted.');
        header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports/indexp');
        exit;
    }


    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'supervisor') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }

    public function show($id)
{
    $reportModel = new Report();
    
    $report = $reportModel->find((int)$id);
    if (!$report) {
        header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports');
        exit;
    }

    $workOrder = $reportModel->getWorkOrderWithVehicleData((int)$report['work_order_id']);

    $services = (new \app\model\supervisor\WorkOrder())->getServiceSummaryFromChecklist(
        (int)$report['work_order_id']
    );

    $photos = $reportModel->getPhotos($id);

    $this->view('supervisor/reports/view', [
        'report'    => $report,
        'workOrder' => $workOrder, 
        'services'  => $services,
        'photos'    => $photos
    ]);
}
    
public function edit(int $id)
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $userId = $_SESSION['user']['user_id'] ?? null;

    $db = db();
    $stmt = $db->prepare("SELECT supervisor_id FROM supervisors WHERE user_id = ?");
    $stmt->execute([$userId]);
    $sup = $stmt->fetch();
    $realSupervisorId = $sup ? (int)$sup['supervisor_id'] : 0;

    $reportModel = new Report();
    $report = $reportModel->find($id);

    if (!$report) {
        header('Location: ' . BASE_URL . '/supervisor/reports');
        exit;
    }

    if ((int)$report['supervisor_id'] !== $realSupervisorId) {
        header('Location: ' . BASE_URL . '/supervisor/reports/indexp');
        exit;
    }

    $workOrderModel = new WorkOrder();
    $workOrder = $workOrderModel->find($report['work_order_id']);

    $services = $workOrderModel->getServiceSummaryFromChecklist($report['work_order_id']);

    $photos = $reportModel->getPhotosByReportId($report['report_id']); 

    $this->view('supervisor/reports/edit', [
        'report' => $report,
        'workOrder' => $workOrder,
        'services' => $services,
        'photos'    => $photos
    ]);
}

public function update(int $reportId)
{
    $reportModel = new Report();

    $status = $_POST['status'] ?? 'draft';

    $odoAtService = (int)($_POST['current_mileage'] ?? 0);
    $interval     = (int)($_POST['service_interval'] ?? 5000);
    $nextDue      = $odoAtService + $interval;

    $data = [
        'inspection_notes'   => $_POST['inspection_notes'] ?? '',
        'quality_rating'     => (int)($_POST['quality_rating'] ?? 0),
        'checklist_verified' => in_array('tasks_verified', $_POST['checklist'] ?? []) ? 1 : 0,
        'test_driven'        => in_array('test_driven', $_POST['checklist'] ?? []) ? 1 : 0,
        'concerns_addressed' => in_array('concerns_addressed', $_POST['checklist'] ?? []) ? 1 : 0,
        'report_summary'     => $_POST['report_summary'] ?? '',
        'status'             => $_POST['status'] ?? 'draft'
    ];

    $reportModel->update($reportId, $data);

    if ($status === 'submitted' || $status === 'draft') {
        $report = $reportModel->find($reportId);
        
        if ($report && !empty($report['work_order_id'])) {
            $reportModel->updateVehicleServiceData(
                (int)$report['work_order_id'], 
                $nextDue, 
                $interval
            );
        }
    }

    if (!empty($_FILES['work_images']['name'][0])) {
        $uploadDir = __DIR__ . '/../../../public/assets/img/report_photos/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        foreach ($_FILES['work_images']['tmp_name'] as $key => $tmpName) {
            if ($_FILES['work_images']['error'][$key] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['work_images']['name'][$key], PATHINFO_EXTENSION);
                $fileName = uniqid('report_') . '.' . $ext;
                $targetPath = $uploadDir . $fileName;

                if (move_uploaded_file($tmpName, $targetPath)) {
                    $reportModel->savePhoto($reportId, 'assets/img/report_photos/' . $fileName);
                }
            }
        }
    }
    $this->flash('success', 'Report Updated.');
    header('Location: ' . BASE_URL . '/supervisor/reports/indexp');
    exit;
}

public function deletePhoto(int $id)
{
    $reportModel = new Report();

    $photo = $reportModel->getPhotoById($id);
    if (!$photo) {
        header('Location: ' . BASE_URL . '/supervisor/reports');
        exit;
    }

    $filePath = __DIR__ . '/../../../public/assets/img/report_photos/' . basename($photo['file_path']);
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    $reportModel->deletePhoto($id);

    header('Location: ' . BASE_URL . '/supervisor/reports/edit/' . $photo['report_id']);
    exit;
}

public function dailyJobs()
{
    $reportModel = new \app\model\supervisor\Report();
    
    $branchId = $_SESSION['user']['branch_id'] ?? null;

    $date = $_GET['report_date'] ?? date('Y-m-d');
    $mechanicCode = $_GET['mechanic_code'] ?? null;

    $dailyReport = $reportModel->getDailyJobCompletion($date, $mechanicCode, $branchId);
    $mechanics   = $reportModel->getAllMechanics($branchId);
    $statusStats = $reportModel->getAppointmentStatusStats($branchId, $date);
    $hourlyStats = $reportModel->getHourlyJobStats($branchId, $date);
    $trendStats  = $reportModel->getWeeklyBookingTrend($branchId);
    $summary     = $reportModel->getBranchPerformanceSummary($branchId, $date);

    $this->view('supervisor/reports/daily-jobs', [
        'dailyReport'  => $dailyReport,
        'mechanics'    => $mechanics,
        'statusStats'  => $statusStats,
        'hourlyStats'  => $hourlyStats,
        'trendStats'   => $trendStats,
        'summary'      => $summary,
        'selectedDate' => $date,
        'selectedMechanic' => $mechanicCode
    ]);
}

public function mechanicActivity()
{
    $reportModel = new \app\model\supervisor\Report();
    
    $branchId = $_SESSION['user']['branch_id'] ?? null;

    $date = $_GET['date'] ?? null;
    $mechanicCode = $_GET['mechanic_code'] ?? null;

    $activity = $reportModel->getMechanicActivity($date, $mechanicCode, $branchId);

    $mechanics = $reportModel->getAllMechanics($branchId);

    $comparison = $reportModel->getMechanicCompletionComparison($branchId, $date, $mechanicCode);
    $efficiency = $reportModel->getMechanicEfficiencyStats($branchId, $date, $mechanicCode);

    $this->view('supervisor/reports/mechanic-activity', [
        'activity'         => $activity,
        'mechanics'        => $mechanics,
        'comparison'       => $comparison,
        'efficiency'       => $efficiency,
        'selectedDate'     => $date,
        'selectedMechanic' => $mechanicCode
    ]);
}

public function download(int $reportId)
{
    $reportModel = new Report();
    $report = $reportModel->find($reportId);
    
    if (!$report) {
        die("Error: Report not found.");
    }

    $format = $_GET['format'] ?? 'pdf';

    if ($format === 'pdf') {
        $this->generatePDF($report);
    } else {
        $this->generateCSV($report);
    }
}

private function generatePDF($report)
{
    $workOrderModel = new \app\model\supervisor\WorkOrder();
    $reportModel = new \app\model\supervisor\Report();
    $workOrder = $workOrderModel->find($report['work_order_id']);
    $services = $reportModel->getServiceTasks($report['work_order_id']); 
    $photos = $reportModel->getReportPhotos($report['report_id']); 

    ob_start();
    include __DIR__ . '/../../views/supervisor/reports/pdf_template.php';
    $html = ob_get_clean();

    if (ob_get_length()) ob_clean();

    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('chroot', $_SERVER['DOCUMENT_ROOT']);
    $dompdf = new \Dompdf\Dompdf($options);

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $dompdf->stream("Report_{$report['report_id']}.pdf", ["Attachment" => true]);
    exit;
}

public function exportMechanic()
{
    $reportModel = new Report();
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    
    $date = $_POST['date'] ?? $_GET['date'] ?? null;
    $mechanicCode = $_POST['mechanic'] ?? $_GET['mechanic'] ?? null;
    $format = $_GET['format'] ?? 'pdf';
    
    $chart1 = $_POST['chart1'] ?? null;
    $chart2 = $_POST['chart2'] ?? null;

    $activity = $reportModel->getMechanicActivity($date, $mechanicCode, $branchId);

    if ($format === 'csv') {
        $this->generateMechanicCSV($activity, $date);
    } else {
        $this->generateMechanicPDF($activity, $date, $chart1, $chart2);
    }
}

private function generateMechanicCSV($data, $date)
{
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="mechanic_report_' . ($date ?? 'all') . '.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['Mechanic Name', 'Code', 'Assigned', 'Completed', 'In Progress', 'Pending', 'Avg Duration (Mins)']);

    foreach ($data as $row) {
        fputcsv($output, [
            $row['mechanic_name'],
            $row['mechanic_code'],
            $row['total_assigned'],
            $row['completed'],
            $row['in_progress'],
            $row['open'],
            $row['avg_duration_mins']
        ]);
    }
    fclose($output);
    exit;
}

private function generateMechanicPDF($activity, $date)
{
    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true); 
    $dompdf = new \Dompdf\Dompdf($options);

    ob_start();
    include __DIR__ . '/../../views/supervisor/reports/mechanic_pdf_template.php';
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = "Mechanic_Report_" . ($date ?? date('Y-m-d')) . ".pdf";
    $dompdf->stream($filename, ["Attachment" => true]);
    exit;
}

public function exportDaily()
{
    $reportModel = new \app\model\supervisor\Report();
    $branchId = $_SESSION['user']['branch_id'] ?? null;
    
    $date = $_REQUEST['date'] ?? date('Y-m-d');
    $mechanicCode = $_REQUEST['mechanic'] ?? null;
    $format = $_GET['format'] ?? 'csv';

    $dailyReport = $reportModel->getDailyJobCompletion($date, $mechanicCode, $branchId);
    $summary = $reportModel->getBranchPerformanceSummary($branchId, $date);

    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="Daily_Report_'.date('Y-m-d').'.csv"');
        
        $output = fopen('php://output', 'w');
        
        fputcsv($output, ['Report Date       ', 'Mechanic Code', 'Total Completed', 'On-Time', 'Delayed', 'Avg Time (Mins)']);
        
        foreach ($dailyReport as $row) {
            fputcsv($output, [
                $row['report_date'] . "\t", 
                $row['mechanic_code'],
                $row['total_completed'],
                $row['on_time'],
                $row['delayed_count'],
                round($row['avg_completion_time'], 2)
            ]);
        }
        fclose($output);
        exit;
    } else {
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new \Dompdf\Dompdf($options);

        $chartStatus = $_POST['chart_status'] ?? null;
        $chartHourly = $_POST['chart_hourly'] ?? null;
        $chartTrend  = $_POST['chart_trend'] ?? null;

        ob_start();
        include __DIR__ . '/../../views/supervisor/reports/daily_jobs_pdf.php';
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("Daily_Report_$date.pdf", ["Attachment" => true]);
        exit;
    }
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
