<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Manager;
use app\model\admin\User;

class ServiceManagersController extends Controller
{
    private Manager $Manager;
    private User $User;

    // Initialize controller dependencies and request context.
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->Manager = new Manager();
        $this->User = new User();
    }

    // Display the main listing or dashboard page.
    public function index()
    {
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        $status = isset($_GET['status']) ? trim((string) $_GET['status']) : 'all';
        $rows = $this->Manager->all($q, $status);

        $this->view('admin/admin-viewmanagers/index', [
            'rows' => $rows,
            'q' => $q,
            'status' => $status,
            'base' => BASE_URL,
        ]);
    }

    // Render the form for creating a new record.
    public function create()
    {
        $this->view('admin/admin-viewmanagers/create', [
            'base' => BASE_URL,
            'nextCode' => $this->Manager->nextCode(),
            'errors' => [],
            'old' => ['status' => 'active', 'password' => 'Manager@123'],
        ]);
    }

    // Handle list operation.
    public function list()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->Manager->all(), JSON_UNESCAPED_UNICODE);
    }

    // Display details for a single record.
    public function show($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $row = $this->Manager->findWithUser($id);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $this->view('admin/admin-viewmanagers/show', [
            'row' => $row,
            'base' => BASE_URL,
        ]);
    }

    // Validate input and save a new record.
    public function store()
    {
        $d = $this->sanitize($_POST);
        if (($d['password'] ?? '') === '') {
            $d['password'] = 'Manager@123';
        }

        $errors = $this->validateCreate($d);
        if ($errors) {
            http_response_code(422);
            $this->view('admin/admin-viewmanagers/create', [
                'base' => BASE_URL,
                'nextCode' => $this->Manager->nextCode(),
                'errors' => $errors,
                'old' => $d,
            ]);
            return;
        }

        try {
            $this->Manager->createUserAndManager([
                'first_name' => $d['first_name'],
                'last_name' => $d['last_name'],
                'username' => $d['username'],
                'email' => $d['email'],
                'password' => $d['password'],
                'phone' => $d['phone'] ?? null,
                'status' => $d['status'] ?? 'active',
            ], $d['manager_code']);

            header('Location: ' . BASE_URL . '/admin/service-managers');
            exit;
        } catch (\Throwable $e) {
            http_response_code(400);
            echo "Error: " . $e->getMessage();
        }
    }

    // Render the form for editing an existing record.
    public function edit($id)
    {
        $id = (int) $id;
        if ($id <= 0) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $row = $this->Manager->findWithUser($id);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $this->view('admin/admin-viewmanagers/edit', [
            'row' => $row,
            'base' => BASE_URL,
            'errors' => [],
            'old' => [],
        ]);
    }

    // Validate input and update an existing record.
    public function update($id)
    {
        $id = (int) $id;
        $d = $this->sanitize($_POST);
        $row = $this->Manager->find($id);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $errors = $this->validateUpdate($d, (int) ($row['user_id'] ?? 0));
        if ($errors) {
            http_response_code(422);
            $this->view('admin/admin-viewmanagers/edit', [
                'row' => array_merge($row, $d),
                'base' => BASE_URL,
                'errors' => $errors,
                'old' => $d,
            ]);
            return;
        }

        $this->User->update((int) $row['user_id'], [
            'first_name' => $d['first_name'],
            'last_name' => $d['last_name'],
            'username' => $d['username'],
            'email' => $d['email'],
            'phone' => $d['phone'] ?? null,
            'status' => $d['status'] ?? 'active',
            'password' => $d['password'] ?? null,
        ]);

        if (isset($d['manager_code']) && $d['manager_code'] !== ($row['manager_code'] ?? null)) {
            $this->Manager->update($id, ['manager_code' => $d['manager_code']]);
        }

        header('Location: ' . BASE_URL . '/admin/service-managers');
        exit;
    }

    // Delete the selected record.
    public function destroy($id)
    {
        $row = $this->Manager->find((int) $id);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $this->User->delete((int) $row['user_id']);
        echo "OK";
    }

    // Handle sanitize operation.
    private function sanitize(array $in): array
    {
        return array_map(static fn($v) => is_string($v) ? trim($v) : $v, $in);
    }

    // Handle validateCreate operation.
    private function validateCreate(array $d): array
    {
        $errors = [];
        foreach (['first_name', 'last_name', 'username', 'email'] as $f) {
            if (empty($d[$f]))
                $errors[] = "$f is required";
        }
        if (!empty($d['email']) && !filter_var($d['email'], FILTER_VALIDATE_EMAIL))
            $errors[] = 'email invalid';
        if (!empty($d['username']) && !preg_match('/^[A-Za-z0-9_]{3,}$/', $d['username']))
            $errors[] = 'username invalid';
        if (!empty($d['phone']) && !preg_match('/^0\d{9}$/', (string) $d['phone']))
            $errors[] = 'phone must be 10 digits and start with 0';
        if (!empty($d['password']) && strlen((string) $d['password']) < 8)
            $errors[] = 'password min 8 chars';
        if (!empty($d['status']) && !in_array($d['status'], ['active', 'inactive'], true))
            $errors[] = 'status invalid';

        $exists = $this->User->findByEmailOrUsername($d['email'] ?? '', $d['username'] ?? '');
        if ($exists)
            $errors[] = 'email/username already exists';

        return $errors;
    }

    // Handle validateUpdate operation.
    private function validateUpdate(array $d, int $currentUserId): array
    {
        $errors = [];
        foreach (['first_name', 'last_name', 'username', 'email'] as $f) {
            if (empty($d[$f]))
                $errors[] = "$f is required";
        }

        if (!empty($d['email']) && !filter_var($d['email'], FILTER_VALIDATE_EMAIL))
            $errors[] = 'email invalid';
        if (!empty($d['username']) && !preg_match('/^[A-Za-z0-9_]{3,}$/', $d['username']))
            $errors[] = 'username invalid';
        if (!empty($d['phone']) && !preg_match('/^0\d{9}$/', (string) $d['phone']))
            $errors[] = 'phone must be 10 digits and start with 0';
        if (!empty($d['password']) && strlen((string) $d['password']) < 8)
            $errors[] = 'password min 8 chars';
        if (!empty($d['status']) && !in_array($d['status'], ['active', 'inactive'], true))
            $errors[] = 'status invalid';

        $exists = $this->User->findByEmailOrUsername($d['email'] ?? '', $d['username'] ?? '');
        if ($exists && (int) ($exists['user_id'] ?? 0) !== $currentUserId) {
            $errors[] = 'email/username already exists';
        }

        return $errors;
    }
}
