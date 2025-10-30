<?php
namespace app\controllers\Receptionist;

use app\core\Controller;

class CustomerProfileController extends Controller
{
    public function index(): void
    {
        // Fetch customers from DB (replace with real DB call)
        $customers = []; 

        $this->view('Receptionist/Customer Profile/Profile', [
            'customers' => $customers
        ]);
    }

    public function create(): void
{
    // Load the form view to add a new customer
    $this->view('Receptionist/Customer Profile/newCustomer');
}

public function show(): void
    {
     $this->view('Receptionist/Customer Profile/individualDetails', ['customers' => []]);

    }
}
