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
}
