<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class ServicesController extends Controller
{
    public function index(): void
    {
        // Later you can fetch real data and pass to the view.
        require APP_ROOT . '/views/manager/viewservices/index.php';
    }
}
