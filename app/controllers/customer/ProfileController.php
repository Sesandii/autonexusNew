<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Profile;

class ProfileController extends Controller
{
    public function index(): void
    {
        $this->requireCustomer();

        $userId   = $this->userId();        // uses parent Controller::userId()
        $model    = new Profile();
        $profile  = $model->getProfile($userId);
        $vehicles = $model->getVehicles($userId);

        $this->view('customer/profile/index', [
            'title'    => 'My Profile',
            'profile'  => $profile,
            'vehicles' => $vehicles,
            'flash'    => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    /* ---------- Edit Profile (HTML form) ---------- */

    public function editForm(): void
    {
        $this->requireCustomer();

        $userId  = $this->userId();
        $model   = new Profile();
        $profile = $model->getProfile($userId);

        $this->view('customer/profile/edit', [
            'title'   => 'Edit Profile',
            'profile' => $profile,
            'flash'   => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    public function updateProfile(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method Not Allowed"; return; }
        $this->requireCustomer();

        $userId = $this->userId();

        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $alt   = trim($_POST['alt_phone'] ?? '');
        $addr  = trim($_POST['street_address'] ?? '');
        $city  = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');

        $model = new Profile();
        $ok = $model->updateProfileFull($userId, $first, $last, $phone, $alt, $addr, $city, $state);

        $_SESSION['flash'] = $ok ? 'Profile updated.' : 'Failed to update profile.';
        header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
        exit;
    }

    /* ---------- Vehicle add/edit (HTML forms) ---------- */

    public function vehicleForm(): void
    {
        $this->requireCustomer();

        $userId = $this->userId();
        $vehId  = (int)($_GET['id'] ?? 0);

        $model = new Profile();
        $vehicle = $vehId ? $model->getVehicleByIdForUser($userId, $vehId) : null;

        $this->view('customer/profile/vehicle_form', [
            'title'   => $vehId ? 'Edit Vehicle' : 'Add Vehicle',
            'vehicle' => $vehicle,
            'flash'   => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    public function saveVehicle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method Not Allowed"; return; }
        $this->requireCustomer();

        $userId = $this->userId();
        $data = [
            'vehicle_id'    => $_POST['vehicle_id'] ?? null,
            'license_plate' => trim($_POST['license_plate'] ?? ''),
            'make'          => trim($_POST['make'] ?? ''),
            'model'         => trim($_POST['model'] ?? ''),
            'year'          => (int)($_POST['year'] ?? 0),
            'color'         => trim($_POST['color'] ?? ''),
        ];

        $model = new Profile();
        $ok = $model->saveVehicle($userId, $data);

        $_SESSION['flash'] = $ok ? 'Vehicle saved.' : 'Failed to save vehicle.';
        header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
        exit;
    }

    public function deleteVehicle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo "Method Not Allowed"; return; }
        $this->requireCustomer();

        $id = (int)($_POST['vehicle_id'] ?? 0);
        $model = new Profile();
        $ok = $model->deleteVehicleOwnedBy($this->userId(), $id);

        $_SESSION['flash'] = $ok ? 'Vehicle removed.' : 'Could not remove vehicle.';
        header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
        exit;
    }
}
