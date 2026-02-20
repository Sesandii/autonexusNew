<?php
declare(strict_types=1);
namespace app\controllers\manager;

use app\core\Controller;

class CustomersController extends Controller
{
    public function index(): void
    {
        require APP_ROOT . '/views/manager/customers/index.php';
    }

    public function create(): void
    {
        // This renders the "New Customer" view
        require APP_ROOT . '/views/manager/customers/newCustomer.php';
    }

    public function newCustomer(): void
{
    // Just load a static view — no params needed
    require APP_ROOT . '/views/manager/customers/individualDetails.php';
}

}
