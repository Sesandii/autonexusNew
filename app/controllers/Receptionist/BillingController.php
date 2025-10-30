<?php
namespace app\controllers\Receptionist;

use app\core\Controller;

class BillingController extends Controller
{
    public function index(): void
    {
        // This will load app/views/Receptionist/Billing/Billing.php
        $this->view('Receptionist/Billing/Billing');
    }

    public function create(): void
    {
        $this->view('Receptionist/Billing/CreateInvoice'); // make sure this file exists
    }
}
