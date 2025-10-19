<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\core\Database;
use app\model\admin\Supervisor;
use app\model\admin\Branch;

class SupervisorsController extends Controller
{
    private Supervisor $Supervisor;
public function __construct() {
    $this->Supervisor = new Supervisor(db());
}


    /** GET /admin/supervisors */
    public function index(): void
    {
        $supervisors = $this->Supervisor->all();
        $this->view('admin/admin-viewsupervisor/index', [
            'current'     => 'supervisors',
            'supervisors' => $supervisors,
        ]);
    }

    /** GET /admin/supervisors/create */
   public function create(): void
{
    $branchModel = new Branch(db());
    $branches = $branchModel->allWithManager();

    $this->view('admin/admin-viewsupervisor/create', [
        'current'  => 'supervisors',
        'old'      => [],
        'errors'   => [],
        'branches' => $branches,   // ← pass to view
    ]);
}

    /** POST /admin/supervisors */
    public function store(): void
    {
        $data   = $this->sanitize($_POST);
        $errors = $this->validate($data, creating:true);

        if ($errors) {
            $this->view('admin/admin-viewsupervisor/create', [
                'current' => 'supervisors',
                'old'     => $data,
                'errors'  => $errors,
            ]);
            return;
        }

        $code = $this->Supervisor->create($data); // returns supervisor_code
        $this->redirect(rtrim(BASE_URL,'/') . "/admin/supervisors/{$code}");
    }

    /** GET /admin/supervisors/{code} */
    public function show(string $code): void
    {
        $s = $this->Supervisor->findByCode($code);
        if (!$s) { http_response_code(404); echo "Supervisor not found"; return; }

        $this->view('admin/admin-viewsupervisor/show', [
            'current' => 'supervisors',
            's'       => $s,
        ]);
    }

    /** GET /admin/supervisors/{code}/edit */
    public function edit(string $code): void
    {
        $s = $this->Supervisor->findByCode($code);
        if (!$s) { http_response_code(404); echo "Supervisor not found"; return; }

        $this->view('admin/admin-viewsupervisor/edit', [
            'current' => 'supervisors',
            's'       => $s,
            'errors'  => [],
        ]);
    }

    /** POST /admin/supervisors/{code} */
    public function update(string $code): void
    {
        $s = $this->Supervisor->findByCode($code);
        if (!$s) { http_response_code(404); echo "Supervisor not found"; return; }

        $data   = $this->sanitize($_POST);
        $errors = $this->validate($data, creating:false);

        if ($errors) {
            $this->view('admin/admin-viewsupervisor/edit', [
                'current' => 'supervisors',
                's'       => array_merge($s, $data),
                'errors'  => $errors,
            ]);
            return;
        }

        $this->Supervisor->updateByCode($code, $data);
        $newCode = $data['supervisor_code'] ?: $code;
        $this->redirect(rtrim(BASE_URL,'/') . "/admin/supervisors/{$newCode}");
    }

    /** POST /admin/supervisors/{code}/delete */
    public function destroy(string $code): void
    {
        $this->Supervisor->deleteByCode($code);
        $this->redirect(rtrim(BASE_URL,'/')."/admin/supervisors");
    }

    /* Helpers */
    private function sanitize(array $data): array {
        foreach ($data as $k=>$v) if (is_string($v)) $data[$k]=trim($v);
        return $data;
    }
    private function validate(array $d, bool $creating): array {
        $e=[];
        if ($creating) {
            if (($d['first_name']??'')==='') $e[]='First name is required.';
            if (($d['last_name'] ??'')==='') $e[]='Last name is required.';
        }
        if (isset($d['status']) && !in_array($d['status'], ['active','inactive'], true)) {
            $e[]='Status must be active or inactive.';
        }
        return $e;
    }
}
