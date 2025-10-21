<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;

class LogoutController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
    }

    /** GET /admin/logout */
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // Unset all session variables
        $_SESSION = [];

        // Destroy the session
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }
        session_destroy();

        // Redirect to login page
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }
}
