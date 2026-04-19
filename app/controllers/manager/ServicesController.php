<?php

namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ServiceModel;
use app\model\Manager\PackageModel;

class ServicesController extends BaseManagerController
{
    private ServiceModel $serviceModel;
    private PackageModel $packageModel;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        
        $this->serviceModel = new ServiceModel();
        $this->packageModel = new PackageModel();
    
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
    $defaultType = 'service';
    $lastCode = $this->serviceModel->getLastCode(); // this was missing!
    
    $this->view('manager/ServicesandPackages/addService', [
        'services'     => $this->serviceModel->getAllServices(),
        'serviceTypes' => $this->serviceModel->getServiceTypes(),
        'lastCode'     => $lastCode,
        'defaultType'  => $defaultType,
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
            'service_code' => $_POST['service_code'],
            'name' => $_POST['name'],
            'description' => $_POST['description'],
            'services' => $_POST['services'] ?? [],
            'duration' => $_POST['duration'],
            'price' => $_POST['price'],
            'type_id'        => $_POST['type_id'],
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

    $lastCode = $this->serviceModel->getLastCode(); // updated here too

    $this->view('manager/ServicesandPackages/editService', [
        'editing'      => $editing,
        'editType'     => $editType,
        'service'      => $service,
        'package'      => $package,
        'services'     => $this->serviceModel->getAllServices(),
        'serviceTypes' => $this->serviceModel->getServiceTypes(),
        'lastCode'     => $lastCode, // single variable replaces both old ones
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
             'type_id'        => $_POST['type_id'],
        ]);
    }

    $this->redirect(BASE_URL . '/manager/services');
}

public function delete(int $id, string $type): void
{
    if ($type === 'service') {
        $service = $this->serviceModel->getServiceById($id);
        if ($service['status'] === 'active') {
            $this->serviceModel->updateStatus($id, 'inactive');
        }
    } elseif ($type === 'package') {
        $package = $this->packageModel->getPackageById($id);
        if ($package['status'] === 'active') {
            $this->packageModel->updateStatus($id, 'inactive');
        }
    }

    $this->redirect(BASE_URL . '/manager/services');
}

public function activate(int $id, string $type): void
{
    if ($type === 'service') {
        $service = $this->serviceModel->getServiceById($id);
        if ($service['status'] === 'inactive') {
            $this->serviceModel->updateStatus($id, 'active');
        }
    } elseif ($type === 'package') {
        $package = $this->packageModel->getPackageById($id);
        if ($package['status'] === 'inactive') {
            $this->packageModel->updateStatus($id, 'active');
        }
    }

    $this->redirect(BASE_URL . '/manager/services');
}

}