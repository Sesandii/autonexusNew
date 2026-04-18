<?php
namespace app\core;

class Controller
{
    /** Optional place to keep app settings */
    protected array $config = [];

    /** Add a constructor so children can safely call parent::__construct($config) */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Render a view from app/views.
     * Usage: $this->view('admin/admin-viewmanagers/index', ['rows'=>$rows]);
     */
    /**
     * Render a view and expose $data as variables (e.g. $rows, $q, $status, $base)
     */
    protected function view(string $path, array $data = []): void
    {
        // Resolve view file
        $file = __DIR__ . '/../views/' . ltrim($path, '/') . '.php';
        if (!is_file($file)) {
            http_response_code(500);
            echo "<pre style='font:14px/1.4 monospace'>View not found: {$file}</pre>";
            return;
        }

        // Make $data keys available as variables in the view
        // e.g. ['rows'=>...] becomes $rows in the template.
        if (!empty($data)) {
            extract($data, EXTR_SKIP);
        }

        // Include the view
        require $file;

        echo "<!-- Loading view file: {$file} -->";

    }

    protected function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }

     /* =========================
       Auth / Role helpers
       ========================= */

    /** Base URL helper */
    protected function baseUrl(): string
    {
        return rtrim(BASE_URL ?? '', '/');
    }

    /** Current user id from session (supports both shapes you’ve used) */
    protected function userId(): int
    {
        return (int)($_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? 0);
    }

    /** Current user role from session */
    protected function userRole(): ?string
    {
        return $_SESSION['user']['role'] ?? null;
    }

    /** Simple login gate: redirect to /login if not logged in */
    protected function requireLogin(): void
    {
        if ($this->userId() > 0) return;

        $_SESSION['flash'] = 'Please log in to continue.';
        header('Location: ' . $this->baseUrl() . '/login');
        exit;
    }

    /**
     * Customer-only gate:
     * - Not logged in -> redirect to login
     * - Logged in but not a "customer" -> send them to a sensible place
     */
    protected function requireCustomer(): void
    {
        $uid  = $this->userId();
        $role = $this->userRole();

        if ($uid <= 0) {
            $_SESSION['flash'] = 'Please log in to continue.';
            header('Location: ' . $this->baseUrl() . '/login');
            exit;
        }

        if ($role !== 'customer') {
            // If you have role-based dashboards, you can redirect accordingly:
            // admin -> /admin-dashboard, others -> /
            $_SESSION['flash'] = 'You do not have access to that page.';
            if ($role === 'admin') {
                header('Location: ' . $this->baseUrl() . '/admin-dashboard');
            } else {
                header('Location: ' . $this->baseUrl() . '/');
            }
            exit;
        }
    }
}
