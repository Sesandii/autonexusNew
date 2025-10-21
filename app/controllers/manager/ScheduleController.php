<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class ScheduleController extends Controller
{
    public function index(): void
    {
        // Later you can fetch actual schedule data from a model.
        require APP_ROOT . '/views/manager/viewschedules/index.php';
    }
}
