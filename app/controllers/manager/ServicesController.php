<?php

namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ServiceModel;
use app\model\Manager\PackageModel;

class ServicesController extends Controller
{
    private ServiceModel $serviceModel;
    private PackageModel $packageModel;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->serviceModel = new ServiceModel();
        $this->packageModel = new PackageModel();
    }

    public function index(): void
{
    $services = $this->serviceModel->getActiveServices();
$packages = $this->packageModel->getAllPackages();


    $this->view('manager/ServicesandPackages/servicesManager', [
        'services' => $services,
        'packages' => $packages
    ]);
}

    public function create(): void
    {
        $this->view('manager/ServicesandPackages/addService', [
            'services'        => $this->serviceModel->getActiveServices(),
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

    $type = $_POST['type'] ?? '';
    $success = false;

    if ($type === 'service') {
        $success = $this->serviceModel->create([
            'service_code' => trim($_POST['service_code']),
            'name'         => trim($_POST['name']),
            'description'  => trim($_POST['description']),
            'duration'     => (int) $_POST['duration'],
            'price'        => (float) $_POST['price'],
            'type_id'      => (int) $_POST['type_id'],
        ]);
    }

    if ($type === 'package') {
        $success = $this->packageModel->create([
            'package_code'  => trim($_POST['package_code']),
            'name'          => trim($_POST['name']),
            'description'   => trim($_POST['description']),
            'services'      => $_POST['services'],
            'total_duration'=> (int) $_POST['total_duration'],
            'total_price'   => (float) $_POST['total_price'],
        ]);
    }

    $_SESSION['flash'] = $success ? 'Saved successfully' : 'Failed to save';
    $this->redirect(BASE_URL . '/manager/services');
}

}
