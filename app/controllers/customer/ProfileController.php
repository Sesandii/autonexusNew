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

        $model = new Profile();
        $current = $model->getProfile($userId);

        $username = trim($_POST['username'] ?? ($current['username'] ?? ''));
        $email = trim($_POST['email'] ?? ($current['email'] ?? ''));
        $first = trim($_POST['first_name'] ?? '');
        $last  = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $alt   = trim($_POST['alt_phone'] ?? ($current['alt_phone'] ?? ''));
        $addr  = trim($_POST['street_address'] ?? '');
        $city  = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');

        $profilePicturePath = null;
        if (!empty($_FILES['profile_picture']['name']) && ($_FILES['profile_picture']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK) {
            $tmpName = (string)($_FILES['profile_picture']['tmp_name'] ?? '');
            $fileName = (string)($_FILES['profile_picture']['name'] ?? '');
            $fileType = (string)($_FILES['profile_picture']['type'] ?? '');

            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($fileType, $allowedTypes, true)) {
                $_SESSION['flash'] = 'Profile picture must be a JPG, PNG, GIF, or WEBP image.';
                header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile/edit');
                exit;
            }

            $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true)) {
                $_SESSION['flash'] = 'Profile picture must be a JPG, PNG, GIF, or WEBP image.';
                header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile/edit');
                exit;
            }

            $uploadDir = dirname(__DIR__, 3) . '/public/assets/img/profile_pictures/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $newFileName = 'profile_' . $userId . '_' . time() . '.' . $ext;
            $targetPath  = $uploadDir . $newFileName;

            if (!move_uploaded_file($tmpName, $targetPath)) {
                $_SESSION['flash'] = 'Failed to upload profile picture.';
                header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile/edit');
                exit;
            }

            $profilePicturePath = 'assets/img/profile_pictures/' . $newFileName;
        }

        $ok = $model->updateProfileFull(
            $userId,
            $username,
            $email,
            $first,
            $last,
            $phone,
            $alt,
            $addr,
            $city,
            $state,
            $profilePicturePath
        );

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
    $ok    = $model->deleteVehicleOwnedBy($this->userId(), $id);

    $_SESSION['flash'] = $ok
      ? 'Vehicle removed.'
      : 'Cannot remove this vehicle because it has appointments. Please cancel them first.';

    header('Location: ' . rtrim(BASE_URL,'/') . '/customer/profile');
    exit;
}

}
