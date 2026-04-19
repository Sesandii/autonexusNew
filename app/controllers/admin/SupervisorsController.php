<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Supervisor;
use app\model\admin\Branch;

class SupervisorsController extends Controller
{
    private Supervisor $Supervisor;

    // Initialize controller dependencies and request context.
    public function __construct()
    {
        $this->Supervisor = new Supervisor(db());
    }


    /** GET /admin/supervisors */
    public function index(): void
    {
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        $status = isset($_GET['status']) ? trim((string) $_GET['status']) : 'all';
        $supervisors = $this->Supervisor->all($q, $status);
        $this->view('admin/admin-viewsupervisor/index', [
            'current' => 'supervisors',
            'supervisors' => $supervisors,
            'q' => $q,
            'status' => $status,
            'base' => BASE_URL,
        ]);
    }

    /** GET /admin/supervisors/create */
    public function create(): void
    {
        $branchModel = new Branch();
        $branches = $branchModel->allWithManager();

        $this->view('admin/admin-viewsupervisor/create', [
            'current' => 'supervisors',
            'old' => [],
            'errors' => [],
            'branches' => $branches,   // ← pass to view
            'base' => BASE_URL,
        ]);
    }

    /** POST /admin/supervisors */
    public function store(): void
    {
        $data = $this->sanitize($_POST);
        if (($data['password'] ?? '') === '') {
            $data['password'] = 'Supervisor@123';
        }
        $errors = $this->validate($data, true);

        if ($errors) {
            $branchModel = new Branch();
            $this->view('admin/admin-viewsupervisor/create', [
                'current' => 'supervisors',
                'old' => $data,
                'errors' => $errors,
                'branches' => $branchModel->allWithManager(),
                'base' => BASE_URL,
            ]);
            return;
        }

        try {
            $code = $this->Supervisor->create($data); // returns supervisor_code
            $this->setSuccessToast('Supervisor created successfully.');
            $this->redirect(rtrim(BASE_URL, '/') . "/admin/supervisors/{$code}");
        } catch (\Throwable $e) {
            $branchModel = new Branch();
            $this->view('admin/admin-viewsupervisor/create', [
                'current' => 'supervisors',
                'old' => $data,
                'errors' => ['Unable to create supervisor. ' . $e->getMessage()],
                'branches' => $branchModel->allWithManager(),
                'base' => BASE_URL,
            ]);
        }
    }

    /** GET /admin/supervisors/{code} */
    public function show(string $code): void
    {
        $s = $this->Supervisor->findByCode($code);
        if (!$s) {
            http_response_code(404);
            echo "Supervisor not found";
            return;
        }

        $this->view('admin/admin-viewsupervisor/show', [
            'current' => 'supervisors',
            's' => $s,
        ]);
    }

    /** GET /admin/supervisors/{code}/edit */
    public function edit(string $code): void
    {
        $s = $this->Supervisor->findByCode($code);
        if (!$s) {
            http_response_code(404);
            echo "Supervisor not found";
            return;
        }

        $branchModel = new Branch();
        $branches = $branchModel->allWithManager();

        $this->view('admin/admin-viewsupervisor/edit', [
            'current' => 'supervisors',
            's' => $s,
            'errors' => [],
            'branches' => $branches,
            'base' => BASE_URL,
        ]);
    }

    /** POST /admin/supervisors/{code} */
    public function update(string $code): void
    {
        $s = $this->Supervisor->findByCode($code);
        if (!$s) {
            http_response_code(404);
            echo "Supervisor not found";
            return;
        }

        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, false, (int) ($s['user_id'] ?? 0));

        if ($errors) {
            $branchModel = new Branch();
            $this->view('admin/admin-viewsupervisor/edit', [
                'current' => 'supervisors',
                's' => array_merge($s, $data),
                'errors' => $errors,
                'branches' => $branchModel->allWithManager(),
                'base' => BASE_URL,
            ]);
            return;
        }

        try {
            $this->Supervisor->updateByCode($code, $data);
            $newCode = $data['supervisor_code'] ?: $code;
            $this->setSuccessToast('Supervisor updated successfully.');
            $this->redirect(rtrim(BASE_URL, '/') . "/admin/supervisors/{$newCode}");
        } catch (\Throwable $e) {
            $branchModel = new Branch();
            $this->view('admin/admin-viewsupervisor/edit', [
                'current' => 'supervisors',
                's' => array_merge($s, $data),
                'errors' => ['Unable to update supervisor. ' . $e->getMessage()],
                'branches' => $branchModel->allWithManager(),
                'base' => BASE_URL,
            ]);
        }
    }

    /** POST /admin/supervisors/{code}/delete */
    public function destroy(string $code): void
    {
        $this->Supervisor->deleteByCode($code);
        $this->setSuccessToast('Supervisor deleted successfully.');
        $this->redirect(rtrim(BASE_URL, '/') . "/admin/supervisors");
    }

    /* Helpers */
    private function sanitize(array $data): array
    {
        foreach ($data as $k => $v)
            if (is_string($v))
                $data[$k] = trim($v);
        return $data;
    }
    // Handle validate operation.
    private function validate(array $d, bool $creating, ?int $currentUserId = null): array
    {
        $e = [];
        if (($d['first_name'] ?? '') === '')
            $e[] = 'First name is required.';
        if (($d['last_name'] ?? '') === '')
            $e[] = 'Last name is required.';

        if (($d['email'] ?? '') !== '' && !filter_var((string) $d['email'], FILTER_VALIDATE_EMAIL)) {
            $e[] = 'Valid email is required.';
        }

        if (($d['phone'] ?? '') !== '' && !preg_match('/^0\d{9}$/', (string) $d['phone'])) {
            $e[] = 'Phone number must be 10 digits and start with 0.';
        }

        $email = trim((string) ($d['email'] ?? ''));
        if ($email !== '') {
            $excludeUserId = null;
            if (!$creating && !empty($d['user_id'])) {
                $excludeUserId = (int) $d['user_id'];
            }
            if ($this->Supervisor->emailExists($email, $excludeUserId)) {
                $e[] = 'Email already exists. Please use another email.';
            }
        }

        $phone = trim((string) ($d['phone'] ?? ''));
        if ($phone !== '') {
            if ($this->Supervisor->phoneExists($phone, $currentUserId)) {
                $e[] = 'Phone already exists. Please use another phone number.';
            }
        }

        if (isset($d['status']) && !in_array($d['status'], ['active', 'inactive'], true)) {
            $e[] = 'Status must be active or inactive.';
        }

        if (($d['branch_id'] ?? '') === '') {
            $e[] = 'Branch is required.';
        }

        return $e;
    }
}
