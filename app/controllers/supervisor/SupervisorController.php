<?php
namespace app\controllers\supervisor;

use app\core\Controller;

class SupervisorController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }
    public function dashboard()
    {
        $this->view('supervisor/dashboard/index');
    }

    /*public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Example login logic
            if ($email === 'supervisor@example.com' && $password === '1234') {
                session_start();
                $_SESSION['role'] = 'supervisor';
                header('Location: /autonexus/supervisor/dashboard');
                exit;
            } else {
                echo "Invalid credentials!";
            }
        } else {
            $this->render('Supervisor/Login/index');
        }
    }*/
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'supervisor')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
