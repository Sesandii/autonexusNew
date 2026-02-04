<?php
namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\WorkOrder;
use app\model\supervisor\Checklist;

class AssignedJobsController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireSupervisor();
    }

    public function index()
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $supervisor_id = $_SESSION['user']['user_id'] ?? null;

    if (!$supervisor_id) {
        die("Unauthorized");
    }

    $m = new WorkOrder();

    $workOrders = $m->getAssigned($supervisor_id);

    foreach ($workOrders as &$job) {

        $progress = 0;

        switch ($job['status']) {
            case 'open':
                $progress = 20;
                break;

            case 'in_progress':
                $progress = 50;
                break;

            case 'completed':
                $progress = 100;
                break;
        }

        if (!empty($job['photo_count']) && $job['photo_count'] > 0) {
            $progress += 25;
        }

        if (!empty($job['checklist_completed']) && $job['checklist_completed'] > 0) {
            $progress += 25;
        }

        $job['progress'] = min($progress, 100);
    }

    $data = [
        'workOrders' => $workOrders,
        'message'    => $_SESSION['message'] ?? null,
    ];

    unset($_SESSION['message']);

    $this->view('supervisor/assignedjobs/index', $data);
}

    public function edit($id)
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $supervisor_id = $_SESSION['user']['user_id'] ?? null;
    if (!$supervisor_id) {
        die("Unauthorized");
    }

    $workOrderModel = new \app\model\supervisor\WorkOrder();
    $checklistModel = new \app\model\supervisor\Checklist();
    $photoModel     = new \app\model\supervisor\ServicePhoto();


    // ✅ NOW $work_order_id EXISTS
    $job = $workOrderModel->getFullJobDetails($id);
    if (!$job) {
        die("found");
    }
    $checklist = $checklistModel->getByWorkOrder((int)$id);
    $job['photos'] = $photoModel->getByWorkOrder((int)$id);

    $data = [
        'job' => $job,
        'checklist' => $checklist
    ];

    $this->view('supervisor/assignedjobs/edit', $data);
}

public function toggleChecklist($id = null)
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /autonexus/supervisor/assignedjobs');
        exit;
    }

    $checklistId = (int)($_POST['checklist_id'] ?? 0);
    $status      = $_POST['status'] ?? 'pending';
    $workOrderId = (int)($_POST['work_order_id'] ?? 0);

    if (!$checklistId || !$workOrderId) {
        die('Invalid request');
    }

    $c = new Checklist();
    $c->updateStatus($checklistId, $status);

    header('Location: /autonexus/supervisor/assignedjobs/' . $workOrderId);
    exit;
}

public function uploadPhoto()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die('Invalid request');
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $workOrderId = (int)($_POST['work_order_id'] ?? 0);

    if (!$workOrderId || !isset($_FILES['service_photo'])) {
        die('Missing data');
    }

    $file = $_FILES['service_photo'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        die('Upload failed');
    }

    // ✅ Validate image type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (!in_array($file['type'], $allowedTypes)) {
        die('Only JPG and PNG allowed');
    }

    // ✅ Generate safe file name
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $fileName  = uniqid('service_', true) . '.' . $extension;

    $uploadDir = dirname(__DIR__, 3) . '/public/assets/img/service_photos/';
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        die('Failed to save file');
    }

    // ✅ Save to database
    $photoModel = new \app\model\supervisor\ServicePhoto();
    $photoModel->create($workOrderId, $fileName);

    // 🔁 Redirect back to edit page
    header('Location: /autonexus/supervisor/assignedjobs/' . $workOrderId);
    exit;
}

public function deletePhoto()
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        die('Invalid request');
    }

    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $photoId     = (int)($_POST['photo_id'] ?? 0);
    $workOrderId = (int)($_POST['work_order_id'] ?? 0);

    if (!$photoId || !$workOrderId) {
        die('Invalid data');
    }

    $photoModel = new \app\model\supervisor\ServicePhoto();

    // 1️⃣ Get photo details
    $photo = $photoModel->findById($photoId);
    if (!$photo) {
        die('Photo not found');
    }

    // 2️⃣ Delete file from folder
    $filePath = dirname(__DIR__, 3) . '/public/assets/img/service_photos/' . $photo['file_name'];
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    // 3️⃣ Delete DB record
    $photoModel->delete($photoId);

    // 4️⃣ Redirect back
    header('Location: /autonexus/supervisor/assignedjobs/' . $workOrderId);
    exit;
}


    private function requireSupervisor(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
