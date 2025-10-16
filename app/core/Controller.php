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
}
