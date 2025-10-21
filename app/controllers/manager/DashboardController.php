<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        // Here you can load metrics from models later and pass to the view if needed.
        // For now, just render the static UI.
        require APP_ROOT . '/views/manager/dashboard/dashboard.php';
    }
}
