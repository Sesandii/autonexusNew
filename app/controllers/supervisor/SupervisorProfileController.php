<?php

namespace app\controllers\supervisor;

use app\core\Controller;
use app\model\supervisor\User;

class SupervisorProfileController extends Controller
{

public function edit()
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $userId = $_SESSION['user']['user_id'];

    $userModel = new User();
    $user = $userModel->findById($userId);

    $this->view('supervisor/profile/edit', [
        'user' => $user
    ]);
}


public function update()
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();

    $userId = $_SESSION['user']['user_id'];

    $data = [
        'first_name'      => $_POST['first_name'],
        'last_name'       => $_POST['last_name'],
        'username'        => $_POST['username'] ?? null,
        'email'           => $_POST['email'],
        'phone'           => $_POST['phone'],
        'alt_phone'       => $_POST['alt_phone'] ?? null,
        'street_address'  => $_POST['street_address'] ?? null,
        'city'            => $_POST['city'] ?? null,
        'state'           => $_POST['state'] ?? null,
    ];

    // Only update password if entered
    if (!empty($_POST['password'])) {
        $data['password_hash'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    $userModel = new User();
    $userModel->updateProfile($userId, $data);

    // Update session name
    $_SESSION['user']['name'] = $data['first_name'] . ' ' . $data['last_name'];

    header('Location: ' . BASE_URL . '/supervisor/dashboard');
    exit;
}

}
