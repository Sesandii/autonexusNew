<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\model\Admin\Mechanic;
use app\model\admin\Branch; // add at the top if you have Branch model


class MechanicsController extends Controller
{
    private Mechanic $Mechanic;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->Mechanic = new Mechanic();
    }

    /** GET /admin-viewmechanics */
    public function index()
    {
        $mechanics = $this->Mechanic->all();
        $this->view('admin/admin-viewmechanics/index', [
            'mechanics' => $mechanics,
            'current'   => 'mechanics',
        ]);
    }

    /** GET /admin/mechanics/create */
    public function create()
{
    $branchModel = new \app\model\admin\Branch();
    $branches = $branchModel->all(); // expects all() returning id/code/name
    
    $this->view('admin/admin-viewmechanics/create', [
        'current'  => 'mechanics',
        'old'      => $_POST ?? [],
        'errors'   => [],
        'branches' => $branches,
    ]);
}
    /** POST /admin/mechanics */
    public function store()
    {
        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, creating:true);

        if ($errors) {
            $this->view('admin/admin-viewmechanics/create', [
                'current' => 'mechanics',
                'old'     => $data,
                'errors'  => $errors,
            ]);
            return;
        }

        $id = $this->Mechanic->create($data);
        $this->redirect(rtrim(BASE_URL,'/')."/admin/mechanics/{$id}");
    }

    /** GET /admin/mechanics/{id} */
    public function show($id)
    {
        $m = $this->Mechanic->find((int)$id);
        if (!$m) {
            http_response_code(404);
            echo "Mechanic not found";
            return;
        }
        $this->view('admin/admin-viewmechanics/show', [
            'mechanic' => $m,
            'current'  => 'mechanics',
        ]);
    }

    /** GET /admin/mechanics/{id}/edit */
   public function edit($id)
{
    $m = $this->Mechanic->find((int)$id);
    if (!$m) { http_response_code(404); echo "Mechanic not found"; return; }

    $branchModel = new \app\model\admin\Branch();
    $branches = $branchModel->all();

    $this->view('admin/admin-viewmechanics/edit', [
        'mechanic' => $m,
        'current'  => 'mechanics',
        'errors'   => [],
        'branches' => $branches,
    ]);
}
    /** POST /admin/mechanics/{id} */
    public function update($id)
    {
        $m = $this->Mechanic->find((int)$id);
        if (!$m) {
            http_response_code(404);
            echo "Mechanic not found";
            return;
        }

        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, creating:false);

        if ($errors) {
            $this->view('admin/admin-viewmechanics/edit', [
                'mechanic' => array_merge($m, $data),
                'current'  => 'mechanics',
                'errors'   => $errors,
            ]);
            return;
        }

        $this->Mechanic->update((int)$id, $data);
        $this->redirect(rtrim(BASE_URL,'/')."/admin/mechanics/{$id}");
    }

    /** POST /admin/mechanics/{id}/delete */
    public function destroy($id)
    {
        $this->Mechanic->delete((int)$id);
        $this->redirect(rtrim(BASE_URL,'/')."/admin-viewmechanics");
    }

    /** Basic trimming/safety */
    private function sanitize(array $data): array
    {
        foreach ($data as $k => $v) {
            if (is_string($v)) $data[$k] = trim($v);
        }
        return $data;
    }

    private function validate(array $d, bool $creating): array
{
    $errors = [];

    if ($creating) {
        if (($d['first_name'] ?? '') === '') $errors[] = 'First name is required.';
        if (($d['last_name'] ?? '') === '')  $errors[] = 'Last name is required.';
        // user_status optional; default 'active'
    }

    // Mechanic status
    if (isset($d['mech_status']) && !in_array($d['mech_status'], ['active','inactive'], true)) {
        $errors[] = 'Mechanic status must be active or inactive.';
    }

    // Experience
    if (isset($d['experience_years']) && $d['experience_years'] !== '' && !is_numeric($d['experience_years'])) {
        $errors[] = 'Experience years must be numeric.';
    }

    // Branch required if you want to force selection
    if (($d['branch_id'] ?? '') === '') {
        $errors[] = 'Branch is required.';
    }

    return $errors;
}

}
