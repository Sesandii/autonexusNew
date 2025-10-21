<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class PerformanceController extends Controller
{
    public function index(): void
    {
        // Later you can fetch metrics and pass to the view.
        require APP_ROOT . '/views/manager/performance/index.php';
    }
}
