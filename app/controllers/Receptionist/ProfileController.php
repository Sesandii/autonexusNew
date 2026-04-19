<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\ProfileModel;

class ProfileController extends Controller
{
    private ProfileModel $model;

    public function __construct()
    {
        parent::__construct();

        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->model = new ProfileModel();
    }

    // ───────── VIEW PROFILE ─────────
    public function index(): void
    {
        $userId = $_SESSION['user']['user_id'] ?? null;

        if (!$userId) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $user = $this->model->getUserProfile($userId);

        if (!$user) {
            $_SESSION['message'] = [
                'type' => 'error',
                'text' => 'Profile not found'
            ];
            header("Location: " . BASE_URL . "/receptionist/profile");
            exit;
        }

        $this->view('Receptionist/Profile/profile', [
            'user' => $user,
            'editMode' => false
        ]);
    }

    // ───────── EDIT PROFILE FORM ─────────
    public function edit(): void
    {
        $userId = $_SESSION['user']['user_id'] ?? null;

        if (!$userId) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $user = $this->model->getUserProfile($userId);

        $this->view('Receptionist/Profile/profile', [
            'user' => $user,
            'editMode' => true
        ]);
    }

    // ───────── UPDATE PROFILE ─────────
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: " . BASE_URL . "/receptionist/profile");
            exit;
        }

        $userId = $_SESSION['user']['user_id'] ?? null;

        if (!$userId) {
            header("Location: " . BASE_URL . "/login");
            exit;
        }

        $data = [
            'first_name'     => trim($_POST['first_name'] ?? ''),
            'last_name'      => trim($_POST['last_name'] ?? ''),
            'phone'          => trim($_POST['phone'] ?? ''),
            'alt_phone'      => trim($_POST['alt_phone'] ?? ''),
            'street_address' => trim($_POST['street_address'] ?? ''),
            'city'           => trim($_POST['city'] ?? ''),
            'state'          => trim($_POST['state'] ?? ''),
        ];

        $success = $this->model->updateProfile($userId, $data);

        $_SESSION['message'] = [
            'type' => $success ? 'success' : 'error',
            'text' => $success ? 'Profile updated successfully' : 'Update failed'
        ];

        header("Location: " . BASE_URL . "/receptionist/profile");
        exit;
    }
}