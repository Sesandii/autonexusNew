<?php

namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\ProfileModel;

class ProfileController extends Controller
{
    private ProfileModel $profileModel;

    public function __construct($config)
    {
        parent::__construct($config);

        $pdo = db(); // PDO connection

        $this->profileModel = new ProfileModel($pdo);

        $this->guardReceptionist();
    }

    private function guardReceptionist(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();

        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'receptionist') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        if (!isset($_SESSION['user']['branch_id'])) {
            $stmt = db()->prepare('SELECT branch_id FROM receptionists WHERE user_id = :uid LIMIT 1');
            $stmt->execute(['uid' => $u['user_id']]);
            $receptionist = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$receptionist) {
                header('Location: ' . rtrim(BASE_URL, '/') . '/login');
                exit;
            }

            $_SESSION['user']['branch_id'] = $receptionist['branch_id'];
        }
    }

    public function index()
    {
        $this->requireLogin();

        $userId = $this->userId();

        $user = $this->profileModel->getById($userId);

        if (!$user) {
            $_SESSION['flash'] = "User not found";
            return $this->redirect($this->baseUrl());
        }

        $this->view('Receptionist/Profile/profile', [
            'user' => $user
        ]);
    }

    public function update()
{
    $this->requireLogin();

    $userId = $this->userId();

    var_dump($userId);
    exit;

    $data = [
        'first_name' => $_POST['first_name'] ?? '',
        'last_name' => $_POST['last_name'] ?? '',
        'phone' => $_POST['phone'] ?? '',
        'alt_phone' => $_POST['alt_phone'] ?? '',
        'street_address' => $_POST['street_address'] ?? '',
        'city' => $_POST['city'] ?? '',
        'state' => $_POST['state'] ?? ''
    ];

    $this->profileModel->update($userId, $data);

    $_SESSION['flash'] = "Profile updated successfully";

    return $this->redirect($this->baseUrl() . "/profile");
}
}