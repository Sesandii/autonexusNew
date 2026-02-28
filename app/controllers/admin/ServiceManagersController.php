<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Manager;
use app\model\admin\User;

class ServiceManagersController extends Controller
{
    private Manager $Manager;
    private User $User;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->Manager = new Manager();
        $this->User    = new User();
    }

    public function index()
    {
        $rows   = $this->Manager->all();
        $q      = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
        $status = isset($_GET['status']) ? trim((string)$_GET['status']) : 'all';

        $this->view('admin/admin-viewmanagers/index', [
            'rows'   => $rows,
            'q'      => $q,
            'status' => $status,
            'base'   => BASE_URL,
        ]);
    }

    public function create()
    {
        $this->view('admin/admin-viewmanagers/create');
    }

    public function list()
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->Manager->all(), JSON_UNESCAPED_UNICODE);
    }

    public function show($id)
    {
        $id = (int)$id;
        if ($id <= 0) { http_response_code(404); echo "Not found"; return; }

        $row = $this->Manager->findWithUser($id);
        if (!$row) { http_response_code(404); echo "Not found"; return; }

        $this->view('admin/admin-viewmanagers/show', [
            'row'  => $row,
            'base' => BASE_URL,
        ]);
    }

    public function store()
    {
        $d = $this->sanitize($_POST);
        $errors = $this->validateCreate($d);
        if ($errors) { http_response_code(422); echo implode("\n", $errors); return; }

        try {
            $this->Manager->createUserAndManager([
                'first_name' => $d['first_name'],
                'last_name'  => $d['last_name'],
                'username'   => $d['username'],
                'email'      => $d['email'],
                'password'   => $d['password'],
                'phone'      => $d['phone'] ?? null,
            ], $d['manager_code']);

            header('Location: ' . BASE_URL . '/admin/service-managers'); exit;
        } catch (\Throwable $e) {
            http_response_code(400);
            echo "Error: " . $e->getMessage();
        }
    }

    public function edit($id)
    {
        $id = (int)$id;
        if ($id <= 0) { http_response_code(404); echo "Not found"; return; }

        $row = $this->Manager->findWithUser($id);
        if (!$row) { http_response_code(404); echo "Not found"; return; }

        $this->view('admin/admin-viewmanagers/edit', [
            'row'  => $row,
            'base' => BASE_URL,
        ]);
    }

    public function update($id)
    {
        $d = $this->sanitize($_POST);
        $row = $this->Manager->find((int)$id);
        if (!$row) { http_response_code(404); echo "Not found"; return; }

        $min = [];
        foreach (['first_name','last_name','username','email'] as $f) {
            if (!isset($d[$f]) || $d[$f] === '') $min[] = $f . ' is required';
        }
        if ($min) { http_response_code(422); echo implode("\n", $min); return; }

        $this->User->update((int)$row['user_id'], [
            'first_name' => $d['first_name'],
            'last_name'  => $d['last_name'],
            'username'   => $d['username'],
            'email'      => $d['email'],
            'phone'      => $d['phone'] ?? null,
            'status'     => $d['status'] ?? 'active',
            'password'   => $d['password'] ?? null,
        ]);

        if (isset($d['manager_code']) && $d['manager_code'] !== ($row['manager_code'] ?? null)) {
            $this->Manager->update((int)$id, ['manager_code' => $d['manager_code']]);
        }

        header('Location: ' . BASE_URL . '/admin/service-managers'); exit;
    }

    public function destroy($id)
    {
        $row = $this->Manager->find((int)$id);
        if (!$row) { http_response_code(404); echo "Not found"; return; }

        $this->User->delete((int)$row['user_id']);
        echo "OK";
    }

    private function sanitize(array $in): array
    {
        return array_map(static fn($v) => is_string($v) ? trim($v) : $v, $in);
    }

    private function validateCreate(array $d): array
    {
        $errors = [];
        foreach (['first_name','last_name','username','email','password'] as $f) {
            if (empty($d[$f])) $errors[] = "$f is required";
        }
        if (!empty($d['email']) && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'email invalid';
        if (!empty($d['username']) && !preg_match('/^[A-Za-z0-9_]{3,}$/', $d['username'])) $errors[] = 'username invalid';
        if (!empty($d['password']) && strlen($d['password']) < 6) $errors[] = 'password min 6 chars';

        $exists = $this->User->findByEmailOrUsername($d['email'] ?? '', $d['username'] ?? '');
        if ($exists) $errors[] = 'email/username already exists';

        return $errors;
    }
}
