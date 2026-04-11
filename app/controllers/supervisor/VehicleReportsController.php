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

    /* =========================
       REPORT LIST
    ========================= */

    // Controller: app/controllers/supervisor/VehicleReportsController.php

public function index()
{
    // Optional: you can pass data to the view if needed
    // For now, no data is required, just show the dashboard

    $this->view('supervisor/reports/index'); // the file: views/supervisor/reports/dashboard.php
}

    public function indexp()
    {
        $model = new Report();
        $reports = $model->all(); // fetch all reports

        $this->view('supervisor/reports/indexp', [
            'reports' => $reports
        ]);
    }

    /* =========================
       CREATE REPORT (FORM)
       URL: /supervisor/reports/create/{workOrderId}
    ========================= */
    public function create()
{
    $woModel = new WorkOrder();

    // Check if a work order id is selected via GET
    $workOrderId = $_GET['id'] ?? null;

    if ($workOrderId) {
        // Fetch selected work order
        $workOrder = $woModel->find((int)$workOrderId);

        // Only completed jobs allowed
        if (!$workOrder || $workOrder['status'] !== 'completed') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports');
            exit;
        }

        // Fetch service summary from checklist
        $services = $woModel->getServiceSummaryFromChecklist((int)$workOrderId);

        $this->view('supervisor/reports/create', [
            'workOrder' => $workOrder,
            'services'  => $services
        ]);

    } else {
        // --- UPDATED LOGIC ---
        // We use branch_id because supervisors oversee a branch, 
        // and work_orders are tied to branches via appointments.
        $branchId = $_SESSION['user']['branch_id'] ?? null; 

        // Security Fallback: If branch_id isn't in session, fetch it from the DB
        if (!$branchId) {
            $db = db();
            $stmt = $db->prepare("SELECT branch_id FROM supervisors WHERE user_id = ?");
            $stmt->execute([$_SESSION['user']['user_id']]);
            $branchId = $stmt->fetchColumn();
        }

        // Fetch completed orders for this branch that don't have a report yet
        $completedOrders = $woModel->getCompletedWorkOrdersWithoutReport((int)$branchId);

        $this->view('supervisor/reports/create', [
            'completedOrders' => $completedOrders
        ]);
    }
}

    /* =========================
       STORE REPORT
       URL: POST /supervisor/reports/store/{workOrderId}
    ========================= */
    public function store()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        
        $userId = $_SESSION['user']['user_id'] ?? null;
        $workOrderId = $_POST['work_order_id'] ?? null;
        $db = db();
        $model = new Report();
    
        // Fetch supervisor_id
        $stmt = $db->prepare("SELECT supervisor_id FROM supervisors WHERE user_id = ?");
        $stmt->execute([$userId]);
        $supervisor = $stmt->fetch();
        $realSupervisorId = $supervisor['supervisor_id'] ?? null;
    
        if (!$realSupervisorId) {
            die('Supervisor record not found.');
        }
    
        // 1. Create the Report (Report table only)
// 1. Create the Report and CAPTURE the ID
$reportId = $model->create([
    'work_order_id'      => $workOrderId,
    'supervisor_id'      => $realSupervisorId,
    'inspection_notes'   => $_POST['inspection_notes'] ?? '',
    'quality_rating'     => (int)($_POST['quality_rating'] ?? 0),
    'checklist_verified' => in_array('tasks_verified', $_POST['checklist'] ?? []) ? 1 : 0,
    'test_driven'        => in_array('test_driven', $_POST['checklist'] ?? []) ? 1 : 0,
    'concerns_addressed' => in_array('concerns_addressed', $_POST['checklist'] ?? []) ? 1 : 0,
    'report_summary'     => $_POST['report_summary'] ?? '',
    'status'             => $_POST['status'] ?? 'draft'
]);

// 2. Calculate and Update Vehicle (quantitative tracking)
if (($_POST['status'] ?? '') === 'submitted') {
    
    // Use current mileage for calculation ONLY
    $odoAtService = (int)($_POST['current_mileage'] ?? 0); 
    $interval     = (int)($_POST['service_interval'] ?? 5000);
    $nextDue      = $odoAtService + $interval;

    // Save only what we need to keep
    $model->updateVehicleServiceData(
        (int)$workOrderId, 
        $nextDue, 
        $interval
    );
}

// 3. Handle photo uploads (This is where the error was)
if (!empty($_FILES['work_images']['name'][0])) {
    $uploadDir = __DIR__ . '/../../../public/assets/img/report_photos/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    foreach ($_FILES['work_images']['tmp_name'] as $key => $tmpName) {
        if ($_FILES['work_images']['error'][$key] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['work_images']['name'][$key], PATHINFO_EXTENSION);
            $fileName = uniqid('report_') . '.' . $ext;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($tmpName, $targetPath)) {
                // Now $reportId is a valid integer, so this won't crash
                $model->savePhoto(
                    $reportId, 
                    'assets/img/report_photos/' . $fileName
                );
            }
        }
    }
}
        // 5. Redirect
        header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports/indexp');
        exit;
    }


    /* =========================
       DELETE REPORT
    ========================= */
    public function delete($id)
    {
        $model = new Report();
        $model->delete((int)$id);

        header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports/indexp');
        exit;
    }

    /* =========================
       AUTH
    ========================= */
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
        $woModel     = new WorkOrder();
    
        // Fetch report
        $report = $reportModel->find((int)$id);
        if (!$report) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports');
            exit;
        }
    
        // Fetch related work order
        $workOrder = $woModel->find((int)$report['work_order_id']);
    
        // Fetch service summary from checklist
        $services = $woModel->getServiceSummaryFromChecklist(
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
    

    // Show edit form
public function edit(int $id)
{
    $reportModel = new Report();
    $report = $reportModel->find($id);

    if (!$report) {
        header('Location: ' . BASE_URL . '/supervisor/reports');
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

// Handle update
public function update(int $reportId)
{
    $reportModel = new Report();

    // 1. Calculate the 'Next Due' value locally (not stored in reports table)
    $odoAtService = (int)($_POST['current_mileage'] ?? 0);
    $interval     = (int)($_POST['service_interval'] ?? 5000);
    $nextDue      = $odoAtService + $interval;

    // 2. Qualitative Data for the Reports table
    $data = [
        'inspection_notes'   => $_POST['inspection_notes'] ?? '',
        'quality_rating'     => (int)($_POST['quality_rating'] ?? 0),
        'checklist_verified' => in_array('tasks_verified', $_POST['checklist'] ?? []) ? 1 : 0,
        'test_driven'        => in_array('test_driven', $_POST['checklist'] ?? []) ? 1 : 0,
        'concerns_addressed' => in_array('concerns_addressed', $_POST['checklist'] ?? []) ? 1 : 0,
        'report_summary'     => $_POST['report_summary'] ?? '',
        'status'             => $_POST['status'] ?? 'draft'
    ];

    // Save report notes
    $reportModel->update($reportId, $data);

    // 3. Sync to Vehicles table only on submission
    if (($_POST['status'] ?? '') === 'submitted') {
        $report = $reportModel->find($reportId);
        
        if ($report && !empty($report['work_order_id'])) {
            // Update the vehicle record using the calculated values
            $reportModel->updateVehicleServiceData(
                (int)$report['work_order_id'], 
                $nextDue, 
                $interval
            );
        }
    }

    // 3. Handle photo uploads (unchanged)
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

    header('Location: ' . BASE_URL . '/supervisor/reports/view/' . $reportId);
    exit;
}

public function deletePhoto(int $id)
{
    $reportModel = new Report();

    // Find the photo
    $photo = $reportModel->getPhotoById($id);
    if (!$photo) {
        header('Location: ' . BASE_URL . '/supervisor/reports');
        exit;
    }

    // Delete file from server
    $filePath = __DIR__ . '/../../../public/assets/img/report_photos/' . basename($photo['file_path']);
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // Delete DB record
    $reportModel->deletePhoto($id);

    header('Location: ' . BASE_URL . '/supervisor/reports/edit/' . $photo['report_id']);
    exit;
}

public function dailyJobs()
{
    $reportModel = new \app\model\supervisor\Report();
    
    // SECURE: Only fetch data for the logged-in supervisor's branch
    $branchId = $_SESSION['user']['branch_id'] ?? null;

    $date = $_GET['report_date'] ?? date('Y-m-d');
    $mechanicCode = $_GET['mechanic_code'] ?? null;

    // Pass $branchId to all methods
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
    
    // SECURE: Grab branch_id from session
    $branchId = $_SESSION['user']['branch_id'] ?? null;

    // Optional filters
    $date = $_GET['date'] ?? null;
    $mechanicCode = $_GET['mechanic_code'] ?? null;

    // 1. Table Data (Branch Restricted)
    $activity = $reportModel->getMechanicActivity($date, $mechanicCode, $branchId);

    // 2. Dropdown List (Only mechanics in this branch)
    $mechanics = $reportModel->getAllMechanics($branchId);

    // 3. Chart Data (Branch Performance Comparison)
    $comparison = $reportModel->getMechanicCompletionComparison($branchId);
    $efficiency = $reportModel->getMechanicEfficiencyStats($branchId);

    $this->view('supervisor/reports/mechanic-activity', [
        'activity'         => $activity,
        'mechanics'        => $mechanics,
        'comparison'       => $comparison,
        'efficiency'       => $efficiency,
        'selectedDate'     => $date,
        'selectedMechanic' => $mechanicCode
    ]);
}


}
