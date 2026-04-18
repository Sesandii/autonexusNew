<?php

namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ServiceModel;
use app\model\Manager\PackageModel;

class ServicesController extends Controller
{
    private ServiceModel $serviceModel;
    private PackageModel $packageModel;

        private function guardManager(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'manager') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
        $stmt = db()->prepare('SELECT branch_id FROM managers WHERE user_id = :uid LIMIT 1');
        $stmt->execute(['uid' => $u['user_id']]); // ✅ FIX
        $manager = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$manager) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $manager['branch_id'];
    }
}

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        
        $this->serviceModel = new ServiceModel();
        $this->packageModel = new PackageModel();
    
        $this->guardManager(); // 🔐 protect all methods
    
    }

      /* Session handling & role check for manager
     */
   
    public function index(): void
{
    $services = $this->serviceModel->getAllServices();
$packages = $this->packageModel->getAllPackages();


    $this->view('manager/ServicesandPackages/servicesManager', [
        'services' => $services,
        'packages' => $packages
    ]);
}

    public function create(): void
    {
        $this->view('manager/ServicesandPackages/addService', [
            'services'        => $this->serviceModel->getAllServices(),
            'serviceTypes'    => $this->serviceModel->getServiceTypes(),
            'lastServiceCode' => $this->serviceModel->getLastServiceCode(),
            'lastPackageCode' => $this->packageModel->getLastPackageCode(),
        ]);
    }

public function store(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect(BASE_URL . '/manager/services');
    }

    $type = $_POST['type'];

    if ($type === 'service') {

        $this->serviceModel->create([
            'service_code' => $_POST['service_code'],
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'duration' => $_POST['duration'],
            'price' => $_POST['price'],
            'type_id' => $_POST['type_id'],
        ]);

    } elseif ($type === 'package') {

        $this->packageModel->create([
            'package_code' => $_POST['package_code'],
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'services' => $_POST['services'] ?? [],
            'total_duration' => $_POST['total_duration'],
            'total_price' => $_POST['total_price'],
            'service_type_id' => $_POST['service_type_id'], // REQUIRED
        ]);
    }

    $this->redirect(BASE_URL . '/manager/services');
}

public function edit(int $id, string $type): void
{
    $editing = true;
    $editType = $type;

    $service = [];
    $package = [];

    if ($editType === 'service') {
        $service = $this->serviceModel->getServiceById($id);
    } elseif ($editType === 'package') {
        $package = $this->packageModel->getPackageById($id);
    }

    $services = $this->serviceModel->getAllServices();
    $serviceTypes = $this->serviceModel->getServiceTypes();

    $this->view('manager/ServicesandPackages/editService', [
        'editing'         => $editing,
        'editType'        => $editType,
        'service'         => $service,
        'package'         => $package,
        'services'        => $services,
        'serviceTypes'    => $serviceTypes,
        'lastServiceCode' => $this->serviceModel->getLastServiceCode(),
        'lastPackageCode' => $this->packageModel->getLastPackageCode(),
    ]);
}

public function update(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $this->redirect(BASE_URL . '/manager/services');
    }

    $type = $_POST['type'];
    $id   = (int) $_POST['id'];

    if ($type === 'service') {
        $this->serviceModel->update($id, [
            'name'        => $_POST['name'],
            'description' => $_POST['description'],
            'duration'    => $_POST['duration'],
            'price'       => $_POST['price'],
            'type_id'     => $_POST['type_id'],
        ]);

    } elseif ($type === 'package') {
        $this->packageModel->update($id, [
            'name'             => $_POST['name'],
            'description'      => $_POST['description'],
            'services'         => $_POST['services'] ?? [],
            'total_duration'   => $_POST['total_duration'],
            'total_price'      => $_POST['total_price'],
            'service_type_id'  => $_POST['service_type_id'],
        ]);
    }

    $this->redirect(BASE_URL . '/manager/services');
}

public function delete(int $id, string $type): void
{
    if ($type === 'service') {
        $this->serviceModel->updateStatus($id, 'inactive');
    } elseif ($type === 'package') {
        $this->packageModel->updateStatus($id, 'inactive');
    }

    // Redirect back to the services/packages list
    $this->redirect(BASE_URL . '/manager/services');
}

public function activate(int $id, string $type): void
{
    if ($type === 'service') {
        $this->serviceModel->updateStatus($id, 'active');
    } elseif ($type === 'package') {
        $this->packageModel->updateStatus($id, 'active');
    }

    $this->redirect(BASE_URL . '/manager/services');
}

}