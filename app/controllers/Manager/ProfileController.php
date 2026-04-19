<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Manager\ProfileModel;

class ProfileController extends BaseManagerController
{
    private ProfileModel $profileModel;
    
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->profileModel = new ProfileModel();
    }
    
    public function index(): void
    {
        $this->requireLogin();
        
        if ($this->userRole() !== 'manager') {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Access denied.'];
            $this->redirect($this->baseUrl() . '/');
            return;
        }
        
        $userId = $this->userId();
        $user = $this->profileModel->getUserProfile($userId);
        
        if (!$user) {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'User profile not found.'];
            $this->redirect($this->baseUrl() . '/manager/profile');
            return;
        }
        
        // Check if edit mode is requested via URL parameter
        $editMode = isset($_GET['edit']) && $_GET['edit'] === 'true';
        
        $this->view('manager/Profile/profile', [
            'user' => $user,
            'editMode' => $editMode
        ]);
    }
    
    public function update(): void
    {
        $this->requireLogin();
        
        if ($this->userRole() !== 'manager') {
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Access denied.'];
            $this->redirect($this->baseUrl() . '/');
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect($this->baseUrl() . '/manager/profile');
            return;
        }
        
        $userId = $this->userId();
        
        $data = [
            'first_name' => trim($_POST['first_name'] ?? ''),
            'last_name' => trim($_POST['last_name'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'alt_phone' => trim($_POST['alt_phone'] ?? ''),
            'street_address' => trim($_POST['street_address'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'state' => trim($_POST['state'] ?? '')
        ];
        
        $errors = [];
        
        if (empty($data['first_name'])) $errors[] = 'First name is required.';
        if (empty($data['last_name'])) $errors[] = 'Last name is required.';
        if (empty($data['phone'])) $errors[] = 'Phone number is required.';
        if (empty($data['city'])) $errors[] = 'City is required.';
        if (empty($data['street_address'])) $errors[] = 'Street address is required.';
        if (empty($data['state'])) $errors[] = 'State/Province is required.';
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            $_SESSION['message'] = ['type' => 'error', 'text' => 'Please fix the errors below.'];
            $this->redirect($this->baseUrl() . '/manager/profile?edit=true');
            return;
        }
        
        $updated = $this->profileModel->updateProfile($userId, $data);
        
        $_SESSION['message'] = [
            'type' => $updated ? 'success' : 'error',
            'text' => $updated ? 'Profile updated successfully!' : 'Failed to update profile.'
        ];
        
        $this->redirect($this->baseUrl() . '/manager/profile');
    }
}