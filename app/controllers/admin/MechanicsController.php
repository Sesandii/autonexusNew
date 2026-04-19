<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\model\Admin\Mechanic;
use app\model\admin\Branch; // add at the top if you have Branch model


class MechanicsController extends Controller
{
    private Mechanic $Mechanic;

    // Initialize controller dependencies and request context.
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->Mechanic = new Mechanic();
    }

    /** GET /admin-viewmechanics */
    public function index()
    {
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        $status = isset($_GET['status']) ? trim((string) $_GET['status']) : 'all';
        $mechanics = $this->Mechanic->all($q, $status);
        $this->view('admin/admin-viewmechanics/index', [
            'mechanics' => $mechanics,
            'current' => 'mechanics',
            'q' => $q,
            'status' => $status,
            'base' => BASE_URL,
        ]);
    }

    /** GET /admin/mechanics/create */
    public function create()
    {
        $branchModel = new \app\model\admin\Branch();
        $branches = $branchModel->all(); // expects all() returning id/code/name

        $this->view('admin/admin-viewmechanics/create', [
            'current' => 'mechanics',
            'old' => $_POST ?? [],
            'errors' => [],
            'branches' => $branches,
        ]);
    }
    /** POST /admin/mechanics */
    public function store()
    {
        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, creating: true);

        if ($errors) {
            $this->view('admin/admin-viewmechanics/create', [
                'current' => 'mechanics',
                'old' => $data,
                'errors' => $errors,
                'branches' => (new \app\model\admin\Branch())->all(),
            ]);
            return;
        }

        $id = $this->Mechanic->create($data);
        $this->setSuccessToast('Mechanic created successfully.');
        $this->redirect(BASE_URL . '/admin/mechanics');
    }

    /** GET /admin/mechanics/{id} */
    public function show($id)
    {
        $m = $this->Mechanic->find((int) $id);
        if (!$m) {
            http_response_code(404);
            echo "Mechanic not found";
            return;
        }
        $this->view('admin/admin-viewmechanics/show', [
            'mechanic' => $m,
            'current' => 'mechanics',
        ]);
    }

    /** GET /admin/mechanics/{id}/edit */
    public function edit($id)
    {
        $m = $this->Mechanic->find((int) $id);
        if (!$m) {
            http_response_code(404);
            echo "Mechanic not found";
            return;
        }

        $branchModel = new \app\model\admin\Branch();
        $branches = $branchModel->all();

        $this->view('admin/admin-viewmechanics/edit', [
            'mechanic' => $m,
            'current' => 'mechanics',
            'errors' => [],
            'branches' => $branches,
        ]);
    }
    /** POST /admin/mechanics/{id} */
    public function update($id)
    {
        $m = $this->Mechanic->find((int) $id);
        if (!$m) {
            http_response_code(404);
            echo "Mechanic not found";
            return;
        }

        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, false, (int) ($m['user_id'] ?? 0));

        if ($errors) {
            $this->view('admin/admin-viewmechanics/edit', [
                'mechanic' => array_merge($m, $data),
                'current' => 'mechanics',
                'errors' => $errors,
                'branches' => (new \app\model\admin\Branch())->all(),
            ]);
            return;
        }

        $this->Mechanic->update((int) $id, $data);
        $this->setSuccessToast('Mechanic updated successfully.');
        $this->redirect(rtrim(BASE_URL, '/') . "/admin/mechanics/{$id}");
    }

    /** POST /admin/mechanics/{id}/delete */
    public function destroy($id)
    {
        $this->Mechanic->delete((int) $id);
        $this->setSuccessToast('Mechanic deleted successfully.');
        $this->redirect(rtrim(BASE_URL, '/') . "/admin/mechanics");
    }

    /** Basic trimming/safety */
    private function sanitize(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_string($v))
                $data[$k] = trim($v);
        }
        return $data;
    }

    // Handle validate operation.
    private function validate(array $d, bool $creating, ?int $currentUserId = null): array
    {
        $errors = [];

        if (($d['first_name'] ?? '') === '')
            $errors[] = 'First name is required.';
        if (($d['last_name'] ?? '') === '')
            $errors[] = 'Last name is required.';
        if (($d['branch_id'] ?? '') === '')
            $errors[] = 'Branch is required.';

        if (isset($d['user_status']) && !in_array($d['user_status'], ['active', 'inactive', 'pending'], true)) {
            $errors[] = 'User status must be active, inactive, or pending.';
        }

        if (isset($d['mech_status']) && !in_array($d['mech_status'], ['active', 'inactive'], true)) {
            $errors[] = 'Mechanic status must be active or inactive.';
        }

        if (isset($d['phone']) && $d['phone'] !== '' && !preg_match('/^0\d{9}$/', (string) $d['phone'])) {
            $errors[] = 'Phone number must be 10 digits and start with 0.';
        }

        if (isset($d['email']) && $d['email'] !== '' && !filter_var((string) $d['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email must be a valid address.';
        }

        if (isset($d['experience_years']) && $d['experience_years'] !== '' && !is_numeric($d['experience_years'])) {
            $errors[] = 'Experience years must be numeric.';
        }

        $email = trim((string) ($d['email'] ?? ''));
        if ($email !== '' && $this->Mechanic->emailExists($email, $currentUserId)) {
            $errors[] = 'Email already exists. Please use another email.';
        }

        $phone = trim((string) ($d['phone'] ?? ''));
        if ($phone !== '' && $this->Mechanic->phoneExists($phone, $currentUserId)) {
            $errors[] = 'Phone already exists. Please use another phone number.';
        }

        return $errors;
    }

}
