<?php
/*namespace app\controllers\Receptionist;

use app\core\Controller;

class ServiceController extends Controller
{
    public function index(): void
    {
        $this->view('Receptionist/ServicesandPackages/services');

    }

}*/

/*namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\ServiceModel;
use app\model\Receptionist\PackageModel;*/

/*class ServiceController extends Controller
{
    private ServiceModel $serviceModel;

    public function __construct($config = [])
    {
        parent::__construct($config);
        $this->serviceModel = new ServiceModel();
    }*/

    /** Loads the view with only active services */
   /* public function index()
    {
        $services = $this->serviceModel->getActiveServices();

        $this->view('receptionist/ServicesandPackages/services', [
            'services' => $services
        ]);
    }
}
*/


namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\ServiceModel;
use app\model\Receptionist\PackageModel;

class ServiceController extends Controller
{
    private ServiceModel $serviceModel;
    private PackageModel $packageModel;

    public function __construct(array $config = [])
    {
        parent::__construct($config);

        // no Database class needed
        $this->serviceModel = new ServiceModel();
        $this->packageModel = new PackageModel();
    }

    public function index()
    {
        $services = $this->serviceModel->getActiveServices();
        $packages = $this->packageModel->getAllPackages();

        $this->view('receptionist/ServicesandPackages/services', [
            'services' => $services,
            'packages' => $packages
        ]);
    }
}