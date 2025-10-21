<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class AppointmentsController extends Controller
{
    public function index(): void
    {
        // Later: fetch appointments from a model and pass to the view.
        require APP_ROOT . '/views/manager/appointments/index.php';
    }
}
