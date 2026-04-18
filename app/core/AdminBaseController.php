<?php
declare(strict_types=1);

namespace app\core;

abstract class AdminBaseController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->bootSession();
        $this->requireAdmin();
    }

    private function bootSession(): void
    {
        if (session_status() !== \PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    protected function requireAdmin(): void
    {
        $role = $_SESSION['user']['role'] ?? null;
        if ($role !== 'admin') {
            $this->redirect('/login');
        }
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . rtrim(BASE_URL, '/') . '/' . ltrim($path, '/'));
        exit;
    }

    protected function sanitize(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $data[$k] = $this->sanitize($v);
            } elseif (is_string($v)) {
                $v = trim($v);
                $data[$k] = preg_replace('/[^\P{C}\n\r\t]+/u', '', $v);
            }
        }
        return $data;
    }
}