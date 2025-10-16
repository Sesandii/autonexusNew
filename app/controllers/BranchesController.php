<?php
namespace app\controllers;

use app\core\Controller;
use app\model\Branch;
use app\model\Manager;

class BranchesController extends Controller
{
    private Branch $Branch;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->Branch = new Branch(); // uses your db() helper internally
    }

 /** GET /branches/create */
    public function create()
    {
        $managers = (new Manager())->all(); // id, code, first_name, last_name, etc.
        $this->view('admin/admin-viewbranches/create', [
            'base'      => BASE_URL,
            'managers'  => $managers,
        ]);
    }


    /** GET /branches */
  public function index()
{
    $branches = $this->Branch->all();

    // read filter params (even if you don’t filter yet)
    $q      = isset($_GET['q']) ? trim((string)$_GET['q']) : '';
    $status = isset($_GET['status']) ? trim((string)$_GET['status']) : 'all';

    $this->view('admin/admin-viewbranches/index', [
        'branches' => $branches,
        'q'        => $q,
        'status'   => $status,
        'base'     => BASE_URL,
    ]);
}


    /** GET /branches/{code} */
public function show($code)
{
    $code = (string)$code;
    $row = $this->Branch->findByCode($code);
    if (!$row) { http_response_code(404); echo "Not found"; return; }

    $this->view('admin/admin-viewbranches/show', [
        'row'  => $row,
        'base' => BASE_URL,
    ]);
}

/** GET /branches/{code}/edit */
    public function edit($code)
    {
        $code = (string)$code;
        $row = $this->Branch->findByCode($code);
        if (!$row) { http_response_code(404); echo "Not found"; return; }

        $managers = (new Manager())->all();

        $this->view('admin/admin-viewbranches/edit', [
            'row'      => $row,
            'base'     => BASE_URL,
            'managers' => $managers,
        ]);
    }

    /** POST /branches (Create) — UI posts the "Add" form here */
    public function store()
    {
        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, creating: true);
        if ($errors) {
            // keep it simple for now; ideally flash + redirect
            http_response_code(422);
            echo implode("\n", $errors);
            return;
        }

        $this->Branch->create($data);
        // For fetch() flows, you can just echo OK
        header('Location: ' . BASE_URL . '/branches');
    }

    /** POST /branches/update/{code} — UI sets code from first table cell (e.g., BR001) */
    public function update($code)
    {
        $code = (string)$code;
        if (!$this->Branch->findByCode($code)) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, creating: false);
        if ($errors) {
            http_response_code(422);
            echo implode("\n", $errors);
            return;
        }

        $this->Branch->updateByCode($code, $data);
       header('Location: ' . BASE_URL . '/branches'); exit;

    }

    /** POST /branches/delete/{code} */
    public function destroy($code)
    {
        $code = (string)$code;
        if (!$this->Branch->findByCode($code)) {
            http_response_code(404);
            echo "Not found";
            return;
        }
        $this->Branch->deleteByCode($code);
        echo "OK";
    }

    /* ----------------- Helpers ----------------- */

    /**
     * Map your existing form names to DB columns (based on your UI):
     * - code            -> branch_code
     * - name            -> name
     * - city or location-> city
     * - working_hours   -> address_line (UI has no address field; we re-use this to store text)
     * - phone/contact   -> phone (limited to varchar(30) in DB)
     * - email           -> email
     * - capacity        -> capacity (int)
     * - staff           -> staff_count (int)
     * - notes           -> notes
     * - status          -> status (active|inactive)
     * - created_at(date)-> created_at (DATETIME)  (we’ll convert date -> date 00:00:00)
     * - manager         -> manager_id (int if given; else NULL)
     */
    private function sanitize(array $src): array
    {
        $get = fn($k) => trim($src[$k] ?? '');

        // map & normalize
        $code   = strtoupper($get('code') ?: $get('branch_id')); // some UIs use branch_id text
        $name   = $get('name') ?: $get('branch_name');
        $city   = $get('city') ?: $get('location');

        // UI doesn't have a real address field, so we store whatever they typed in "working_hours" into address_line
        $address_line = $get('address_line');
        if ($address_line === '') {
            $address_line = $get('working_hours'); // reuse that text to not lose information
        }

        // DB phone is VARCHAR(30) - trim length just in case
        $phone = substr($get('phone') ?: $get('contact'), 0, 30);

        $email = $get('email');
        $status = in_array(($get('status') ?: 'active'), ['active','inactive'], true)
                  ? ($get('status') ?: 'active') : 'active';

        // created_at is DATETIME; form gives date (YYYY-MM-DD)
        $created_date = $get('created_at');
        $created_at = $created_date ? ($created_date . ' 00:00:00') : date('Y-m-d 00:00:00');

        $capacity = is_numeric($get('capacity')) ? (int)$get('capacity') : 0;
        $staff    = is_numeric($get('staff')) ? (int)$get('staff') : 0;

        $notes = $get('notes');

        // manager_id is INT; if UI gives a number in "manager", use it; else set null
        $manager_raw = $get('manager');
        $manager_id = (is_numeric($manager_raw) ? (int)$manager_raw : null);

        return [
            'branch_code' => $code,
            'name'        => $name,
            'city'        => $city,
            'address_line'=> $address_line,
            'phone'       => $phone,
            'email'       => $email,
            'capacity'    => $capacity,
            'staff_count' => $staff,
            'notes'       => $notes,
            'status'      => $status,
            'created_at'  => $created_at,
            'manager_id'  => $manager_id,
        ];
    }

    private function validate(array $d, bool $creating): array
    {
        $e = [];
        if ($creating && $d['branch_code'] === '') $e[] = 'Branch code is required';
        if ($d['name'] === '')                     $e[] = 'Name is required';
        if ($d['city'] === '')                     $e[] = 'City is required';

        if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $e[] = 'Invalid email';
        }

        // very light DATETIME format check
        if ($d['created_at'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $d['created_at'])) {
            $e[] = 'created_at must be DATETIME (YYYY-MM-DD HH:MM:SS)';
        }

        if (!in_array($d['status'], ['active','inactive'], true)) {
            $e[] = 'Invalid status';
        }
        return $e;
    }
}
