<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\BillingModel;

class BillingController extends Controller
{
    private BillingModel $billing;

    public function index(): void 
    { // This will load app/views/Receptionist/Billing/Billing.php
     $this->view('Receptionist/Billing/billing'); 
    }
    
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->billing = new BillingModel();
    }

    /** Load the invoice creation page */
    public function create(): void
    {
        // Load service types for dropdown
        $serviceTypes = $this->billing->getServiceTypes();

        $this->view('Receptionist/Billing/createInvoice', [
            'serviceTypes' => $serviceTypes
        ]);
    }

    /** AJAX: Fetch customer info by phone */
    public function getCustomerData(): void
{
    $phone = $_GET['phone'] ?? '';

    $customer = $this->billing->getCustomerByPhone($phone);

    if (!$customer) {
        echo json_encode(["error" => "Customer not found"]);
        return;
    }

    // fetch vehicles using user_id
    $vehicles = $this->billing->getVehiclesByUser($customer['user_id']);

    echo json_encode([
        "first_name" => $customer['first_name'],
        "last_name"  => $customer['last_name'],
        "email"      => $customer['email'],
        "vehicles"   => $vehicles
    ]);
}


    /** AJAX: Fetch services for a type */
    public function getServicesByType(): void
    {
        $typeId = $_GET['type_id'] ?? 0;

        $services = $this->billing->getServicesByType((int)$typeId);

        echo json_encode($services);
    }

    /** AJAX: Fetch service price */
    public function getServicePrice(): void
    {
        $serviceId = $_GET['service_id'] ?? 0;

        $price = $this->billing->getServicePrice((int)$serviceId);

        echo json_encode(["price" => $price]);
    }

        /** AJAX: Fetch all active packages */
    public function getPackages(): void
    {
        $packages = $this->billing->getPackages();
        echo json_encode($packages);
    }



}
