<?php
declare(strict_types=1);

namespace app\controllers;

use app\core\Controller;

class LogoutController extends Controller
{
    /** GET /logout */
    public function index(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        // (Optional) remember role if you want role-specific redirects later
        $lastRole = $_SESSION['role'] ?? null;

        // Unset all session variables
        $_SESSION = [];

        // Destroy the session cookie
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        // Kill the session
        session_destroy();

        // Redirect to login (single login page)
        $redirect = rtrim(BASE_URL, '/') . '/login';

        // If you prefer role-based login pages, uncomment this switch:
        /*
        switch ($lastRole) {
            case 'admin':       $redirect = rtrim(BASE_URL, '/') . '/admin/login'; break;
            case 'manager':     $redirect = rtrim(BASE_URL, '/') . '/manager/login'; break;
            case 'receptionist':$redirect = rtrim(BASE_URL, '/') . '/reception/login'; break;
            case 'mechanic':    $redirect = rtrim(BASE_URL, '/') . '/mechanic/login'; break;
            default:            $redirect = rtrim(BASE_URL, '/') . '/login';
        }
        */

        header('Location: ' . $redirect);
        exit;
    }
}
