<?php

namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\supervisor\User; // use the same working User model

class ProfileController extends Controller
{
    public function index()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $userId = $_SESSION['user']['user_id'];

        $userModel = new User();
        $user = $userModel->findById($userId); // or add findReceptionistProfile() like mechanic has

        $this->view('Receptionist/Profile/profile', [
            'user' => $user
        ]);
    }

    public function update()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $userId = $_SESSION['user']['user_id'];

        $data = [
            'first_name'     => trim($_POST['first_name'] ?? ''),
            'last_name'      => trim($_POST['last_name'] ?? ''),
            'phone'          => trim($_POST['phone'] ?? ''),
            'alt_phone'      => trim($_POST['alt_phone'] ?? null),
            'street_address' => trim($_POST['street_address'] ?? null),
            'city'           => trim($_POST['city'] ?? null),
            'state'          => trim($_POST['state'] ?? null),
        ];

        $userModel = new User();
        $userModel->updateProfile($userId, $data);

        $_SESSION['flash'] = 'Profile updated successfully.';

        header('Location: ' . BASE_URL . '/receptionist/profile');
        exit;
    }
}