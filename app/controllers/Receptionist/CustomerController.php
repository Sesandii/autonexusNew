<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\CustomerModel;

use PDO; // <-- Add this at the to
class CustomerController extends Controller
{

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

    // Use the same db() helper or $this->db from base controller
    $db = db();  
    $this->guardReceptionist(); // 🔐 enforce manager login & branch
    $this->model = new CustomerModel($db); // ✅ pass required argument
}

    /**
     * Show All Customers
     */
    public function index(): void
    {
        $customers = $this->model->getAllCustomers();

        $this->view('receptionist/Customer Profile/profile', [
            'customers' => $customers
        ]);
    }
   public function create(): void
{
    $db = db(); // your PDO instance

    // Fetch all active branches from the DB
    $stmt = $db->query("SELECT branch_id, name, city FROM branches WHERE status = 'active' ORDER BY name ASC");
    $branches = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Load the form view with branches
    $this->view('receptionist/Customer Profile/newCustomer', [
        'branches' => $branches
    ]);
}

public function store(): void
    {
        $db = db(); // your global PDO helper
        $customerModel = new CustomerModel($db);

        // Read POST data
        $userData = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name'  => $_POST['last_name'] ?? '',
            'username'   => $_POST['username'] ?? '',
            'email'      => $_POST['email'] ?? '',
            'password'   => $_POST['password'] ?? '',
            'phone'      => $_POST['phone'] ?? ''
        ];

        // Read vehicle data
        $vehiclesData = $_POST['vehicles'] ?? [];

        // Use the new model method that handles multiple vehicles
        $customerId = $customerModel->storeCustomerWithVehicles($userData, $vehiclesData);

       echo "<script>
    alert('Customer saved successfully!');
    window.location.href='" . BASE_URL . "/receptionist/customers';
</script>";
exit;
    }


public function show(int $customerId): void
    {
        $customer = $this->model->getCustomerById($customerId);

        if (!$customer) {
            http_response_code(404);
            echo "Customer not found";
            return;
        }

        $this->view('receptionist/Customer Profile/individualDetails', [
            'customer' => $customer
        ]);
    }


// In CustomerController

public function edit(int $customerId): void
{
    $customer = $this->model->getCustomerById($customerId);

    if (!$customer) {
        http_response_code(404);
        echo "Customer not found";
        return;
    }

    $this->view('receptionist/Customer Profile/updateCustomer', [
        'customer' => $customer
    ]);
}


public function updateCustomer(int $customerId): void
{
    $customer = $this->model->getCustomerById($customerId);
    if (!$customer) {
        http_response_code(404);
        echo "Customer not found";
        return;
    }

    $userData = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name'  => $_POST['last_name'] ?? '',
        'username'   => $_POST['username'] ?? '',
        'email'      => $_POST['email'] ?? '',
        'phone'      => $_POST['phone'] ?? ''
    ];

    $vehiclesData = $_POST['vehicles'] ?? [];

    // Update user info
    $this->model->updateUser($customer['user_id'], $userData);

    // Update vehicles
    $this->model->updateVehicles($customer['customer_id'], $vehiclesData);

    echo "<script>
        alert('Customer updated successfully!');
        window.location.href='" . BASE_URL . "/receptionist/customers/{$customer['customer_id']}';
    </script>";
    exit;
}

}
