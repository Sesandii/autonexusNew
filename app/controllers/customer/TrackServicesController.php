<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\ServiceTracking;

class TrackServicesController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        $userId = (int)($_SESSION['user_id'] ?? 0);

        // initial dataset for first render
        $model = new ServiceTracking();
        $services = $model->searchByCustomer(
            $userId,
            (string)($_GET['q'] ?? ''),              // optional prefilled query
            (string)($_GET['status'] ?? 'All')       // All | Pending | In Progress | Completed
        );

        $this->view('customer/track-services/index', [
            'title'    => 'Track Services',
            'services' => $services,
        ]);
    }

    // Optional: JSON endpoint used by the JS to filter without full reload
    public function list(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        header('Content-Type: application/json; charset=utf-8');

        $userId = (int)($_SESSION['user_id'] ?? 0);
        $q      = (string)($_GET['q'] ?? '');
        $status = (string)($_GET['status'] ?? 'All');

        $model = new ServiceTracking();
        $rows  = $model->searchByCustomer($userId, $q, $status);

        echo json_encode(['data' => $rows], JSON_UNESCAPED_UNICODE);
    }
}
