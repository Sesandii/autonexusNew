<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class CustomerHistoryController extends Controller
{
    public function index(array $params = []): void
    {
        // Optionally use $params['id'] to load specific customer data later.
        require APP_ROOT . '/views/manager/vehiclehistory/index.php';
    }
}
