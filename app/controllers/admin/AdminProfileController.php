<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Profile;

class AdminProfileController extends Controller
{
    private Profile $profileModel;

    // in app/controllers/admin/AdminProfileController.php
    private function guardAdmin(): void
    {
        if (session_status() !== \PHP_SESSION_ACTIVE)
            session_start();
        $role = $_SESSION['user']['role'] ?? null;
        if ($role !== 'admin') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }

    // then call $this->guardAdmin(); inside __construct() or at the top of index()/update()


    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->guardAdmin(); // ensure only admin can access
        $this->profileModel = new Profile();
    }

    /** GET /admin/profile */
    public function index(): void
    {
        $adminId = $this->userId();
        if (!$adminId) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $admin = $this->profileModel->getAdminById($adminId);
        if (!$admin) {
            $_SESSION['flash'] = 'Admin profile not found.';
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
        $this->view('admin/admin-profile/index', ['admin' => $admin]);
    }

    /** POST /admin/profile/update */
    public function update(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/profile');
            exit;
        }

        $adminId = $this->userId();
        if (!$adminId) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'alt_phone' => trim($_POST['alt_phone'] ?? ''),
            'street' => trim($_POST['street_address'] ?? ($_POST['street'] ?? '')),
            'city' => trim($_POST['city'] ?? ''),
            'state' => trim($_POST['state'] ?? ''),
        ];

        $errors = [];
        if ($data['first_name'] === '' || $data['last_name'] === '')
            $errors[] = 'Name is required.';
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL))
            $errors[] = 'Valid email required.';
        if ($data['phone'] !== '' && !preg_match('/^\d{10}$/', $data['phone']))
            $errors[] = 'Phone must be 10 digits.';
        if ($data['alt_phone'] !== '' && !preg_match('/^\d{10}$/', $data['alt_phone']))
            $errors[] = 'Alternate phone must be 10 digits.';

        if ($errors) {
            $_SESSION['flash'] = implode(' ', $errors);
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/profile');
            exit;
        }

        $updated = $this->profileModel->updateAdmin($adminId, $data);
        if ($updated) {
            $_SESSION['user']['first_name'] = $data['first_name'];
            $_SESSION['user']['last_name'] = $data['last_name'];
            $_SESSION['user']['email'] = $data['email'];

            $_SESSION['flash'] = 'Profile updated successfully.';
        } else {
            $_SESSION['flash'] = 'Failed to update profile. Please try again.';
        }

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/profile');
        exit;
    }
}
