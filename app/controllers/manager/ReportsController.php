<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class ReportsController extends Controller
{
    public function index(): void
    {
        require APP_ROOT . '/views/manager/reports/index.php';
    }
}
