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

     private function guardReceptionist(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'receptionist') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
        $stmt = db()->prepare('SELECT branch_id FROM receptionists WHERE user_id = :uid LIMIT 1');
       
        $stmt->execute(['uid' => $u['user_id']]);
        $receptionist = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$receptionist) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $receptionist['branch_id'];
    }
}



    public function __construct(array $config = [])
    {
        parent::__construct($config);

        $this->guardReceptionist(); // 🔐 enforce manager login & branch
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