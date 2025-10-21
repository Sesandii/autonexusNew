<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Profile;

class ProfileController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        // robust session user id
        $userId = (int)($_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? 0);

        $model    = new Profile();
        $profile  = $model->getProfile($userId);
        $vehicles = $model->getVehicles($userId);

        $this->view('customer/profile/index', [
            'title'    => 'My Profile',
            'profile'  => $profile,
            'vehicles' => $vehicles
        ]);
    }

    public function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "Method Not Allowed";
            return;
        }

        $userId = (int)($_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? 0);
        if (!$userId) {
            echo json_encode(['error' => 'Not logged in']);
            return;
        }

        // your form currently posts a single "name"; split to first/last safely
        $name  = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $parts = preg_split('/\s+/', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $first = $parts[0] ?? '';
        $last  = implode(' ', array_slice($parts, 1));

        $model = new Profile();
        // No NIC / picture update here because columns do not exist
        $model->updateProfile($userId, $first, $last, $phone);

        echo json_encode(['success' => true]);
    }

    public function saveVehicle(): void
    {
        $userId = (int)($_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? 0);

        // map posted names to your DB column names
        $data = [
            'vehicle_id'    => $_POST['vehicle_id'] ?? null,
            'make'          => trim($_POST['brand'] ?? ''),            // brand -> make
            'model'         => trim($_POST['model'] ?? ''),
            'color'         => trim($_POST['color'] ?? ''),
            'year'          => (int)($_POST['year'] ?? 0),
            'license_plate' => trim($_POST['reg_no'] ?? ''),           // reg_no -> license_plate
        ];

        $model = new Profile();
        $model->saveVehicle($userId, $data);
        echo json_encode(['success' => true]);
    }

    public function deleteVehicle(): void
    {
        $id = (int)($_POST['vehicle_id'] ?? 0);
        $model = new Profile();
        $model->deleteVehicle($id);
        echo json_encode(['success' => true]);
    }
}
