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
        // No selection yet → fetch completed work orders without existing report
        $completedOrders = $woModel->getCompletedWorkOrdersWithoutReport();

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

    $workOrderId = $_POST['work_order_id'] ?? null;
    if (!$workOrderId) {
        die('Invalid work order ID');
    }

    $model = new Report();

    // 1️⃣ Create report
    $model->create([
        'work_order_id'               => $workOrderId,
        'inspection_notes'            => $_POST['inspection_notes'] ?? '',
        'quality_rating'              => (int)($_POST['quality_rating'] ?? 0),

        'checklist_verified'          => in_array('tasks_verified', $_POST['checklist'] ?? []) ? 1 : 0,
        'test_driven'                 => in_array('test_driven', $_POST['checklist'] ?? []) ? 1 : 0,
        'concerns_addressed'          => in_array('concerns_addressed', $_POST['checklist'] ?? []) ? 1 : 0,

        'report_summary'              => $_POST['report_summary'] ?? '',
        'next_service_recommendation' =>(!empty($_POST['next_service_recommendation']))
    ? $_POST['next_service_recommendation']
    : null,

        'status'                      => $_POST['status'] ?? 'draft'
    ]);

    // 2️⃣ Get newly created report ID
    $reportId = $model->getLastInsertId();

    // 3️⃣ Handle photo uploads
    if (!empty($_FILES['work_images']['name'][0])) {

        $uploadDir = __DIR__ . '/../../../public/assets/img/report_photos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

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

    // 4️⃣ Redirect
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

        header('Location: ' . rtrim(BASE_URL, '/') . '/supervisor/reports');
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

    // 1️⃣ Collect form data
    $data = [
        'inspection_notes'           => $_POST['inspection_notes'] ?? '',
        'quality_rating'             => (int)($_POST['quality_rating'] ?? 0),
        'checklist_verified'         => in_array('tasks_verified', $_POST['checklist'] ?? []) ? 1 : 0,
        'test_driven'                => in_array('test_driven', $_POST['checklist'] ?? []) ? 1 : 0,
        'concerns_addressed'         => in_array('concerns_addressed', $_POST['checklist'] ?? []) ? 1 : 0,
        'report_summary'             => $_POST['report_summary'] ?? '',
        'next_service_recommendation'=> !empty($_POST['next_service_recommendation'])
                                        ? $_POST['next_service_recommendation']
                                        : null,
        'status'                     => $_POST['status'] ?? 'draft'
    ];

    // 2️⃣ Update DB
    $reportModel->update($reportId, $data);

    // 3️⃣ Handle photo uploads (optional)
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

    // 4️⃣ Redirect to view page
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

    // Read filters from GET
    $date = $_GET['report_date'] ?? null;
    $mechanicCode = $_GET['mechanic_code'] ?? null;

    // Fetch filtered daily report
    $dailyReport = $reportModel->getDailyJobCompletion($date, $mechanicCode);

    // Optionally, pass mechanics list to the view for the dropdown
    $mechanics = $reportModel->getAllMechanics(); // You need a method for this

    $this->view('supervisor/reports/daily-jobs', [
        'dailyReport' => $dailyReport,
        'mechanics'   => $mechanics,
        'selectedDate' => $date,
        'selectedMechanic' => $mechanicCode
    ]);
}

public function mechanicActivity()
{
    $reportModel = new \app\model\supervisor\Report();

    // Read optional filters from GET
    $date = $_GET['date'] ?? null;
    $mechanicCode = $_GET['mechanic_code'] ?? null;

    $activity = $reportModel->getMechanicActivity($date, $mechanicCode);

    // List of mechanics for dropdown filter
    $mechanics = $reportModel->getAllMechanics();

    $this->view('supervisor/reports/mechanic-activity', [
        'activity' => $activity,
        'mechanics' => $mechanics,
        'selectedDate' => $date,
        'selectedMechanic' => $mechanicCode
    ]);
}


}
