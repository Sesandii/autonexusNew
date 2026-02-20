<?php
declare(strict_types=1);

namespace app\controllers\manager;

use app\core\Controller;

class ComplaintsController extends Controller
{
    public function index(): void
    {
        require APP_ROOT . '/views/manager/complaints/index.php';
    }
}
