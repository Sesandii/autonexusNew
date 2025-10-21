<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Service;
use app\model\admin\ServiceType;
use app\model\admin\Branch;
use app\model\admin\BranchService;

class ServicesController extends Controller
{
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
    }

    /** GET /admin/admin-viewservices */
    public function index(): void
{
    $svcModel = new \app\model\admin\Service();

    $services = $svcModel->allWithTypeAndBranches();   // rows with type + branches
    $tabs     = $svcModel->distinctTypesForTabs();     // to build dynamic tabs

    $this->view('admin/admin-viewservices/index', [
        'pageTitle' => 'Service Management',
        'current'   => 'services',
        'base'      => BASE_URL,
        'services'  => $services,
        'tabs'      => $tabs,
    ]);
}


    /** GET /admin/services/create */
public function create(): void
{
    $types    = (new ServiceType())->all();          // loads existing service types
    $branches = (new Branch())->allActive();
    $nextCode = (new Service())->nextCode();         // pre-fill code

    $this->view('admin/admin-viewservices/create', [
        'types'    => $types,
        'branches' => $branches,
        'nextCode' => $nextCode,
        'base'     => BASE_URL,
        'current'  => 'services',
    ]);
}

/** POST /admin/services */
public function store(): void
{
    $pdo = db();
    $pdo->beginTransaction();
    try {
        $svc   = new Service();
        // Always generate server-side to avoid tampering/race
        $generatedCode = $svc->nextCode();

        $data   = $this->sanitize($_POST);
        $data['service_code'] = $generatedCode;

        $errors = $this->validate($data);
        if ($errors) {
            $pdo->rollBack();
            http_response_code(422);
            echo implode("\n", $errors);
            return;
        }

        // Insert service
        $service_id = $svc->create($data);

        // Attach to branches
        $apply = $_POST['apply_scope'] ?? 'all';
        $bs    = new BranchService();
        $branchModel = new Branch();

        if ($apply === 'all') {
            $branch_ids = $branchModel->idsOfActive();
        } else {
            $branch_ids = array_map('intval', $_POST['branches'] ?? []);
        }

        $bs->attachToBranches($service_id, $branch_ids);

        $pdo->commit();
        header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-viewservices');
        exit;

    } catch (\Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo "Create failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

/** GET /admin/services/{id}/edit */
public function edit($id): void
{
    $id = (int)$id;
    $svc = new \app\model\admin\Service();
    $row = $svc->findById($id);
    if (!$row) { http_response_code(404); echo "Service not found"; return; }

    $types      = (new \app\model\admin\ServiceType())->all();
    $branches   = (new \app\model\admin\Branch())->allActive();
    $bsModel    = new \app\model\admin\BranchService();
    $attached   = $bsModel->branchIdsForService($id);
    $allActive  = array_map(fn($b)=> (int)$b['branch_id'], $branches);

    // Decide default radio: if service is in ALL active branches, preselect "all"
    $isAll = !array_diff($allActive, $attached) && !empty($allActive);

    $this->view('admin/admin-viewservices/edit', [
        'row'        => $row,
        'types'      => $types,
        'branches'   => $branches,
        'attached'   => $attached,
        'applyAll'   => $isAll,
        'base'       => BASE_URL,
        'current'    => 'services',
    ]);
}

/** POST /admin/services/{id} */
public function update($id): void
{
    $id  = (int)$id;
    $pdo = db();
    $pdo->beginTransaction();

    try {
        $svc = new \app\model\admin\Service();
        if (!$svc->findById($id)) { $pdo->rollBack(); http_response_code(404); echo "Not found"; return; }

        $data   = $this->sanitize($_POST);              // same sanitize() as before
        unset($data['created_at']);                     // keep original created_at
        $errors = $this->validate($data);
        if ($errors) { $pdo->rollBack(); http_response_code(422); echo implode("\n",$errors); return; }

        $svc->updateById($id, $data);

        // Branch availability
        $apply = $_POST['apply_scope'] ?? 'all';
        $bs    = new \app\model\admin\BranchService();
        $branchModel = new \app\model\admin\Branch();

        if ($apply === 'all') {
            $branch_ids = $branchModel->idsOfActive();
        } else {
            $branch_ids = array_map('intval', $_POST['branches'] ?? []);
        }
        $bs->replaceForService($id, $branch_ids);

        $pdo->commit();
        header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-viewservices');
        exit;

    } catch (\Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo "Update failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}

/** POST /admin/services/{id}/delete */
public function destroy($id): void
{
    $id = (int)$id;
    $svc = new \app\model\admin\Service();
    if (!$svc->findById($id)) { http_response_code(404); echo "Not found"; return; }

    try {
        $svc->deleteById($id); // FK cascade removes branch_service links
        header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-viewservices');
        exit;
    } catch (\Throwable $e) {
        http_response_code(500);
        echo "Delete failed: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}



    /* ---------------- helpers ---------------- */

    private function sanitize(array $src): array
    {
        $get = fn($k,$d='') => trim((string)($src[$k] ?? $d));

        $service_code = strtoupper($get('service_code'));
        $name         = $get('name');
        $description  = $get('description');
        $type_id      = (int)$get('type_id', 0);

        $duration = (int)($get('base_duration_minutes') !== '' ? $get('base_duration_minutes') : 0);
        $priceRaw = $get('default_price', '0');
        $price    = is_numeric($priceRaw) ? number_format((float)$priceRaw, 2, '.', '') : '0.00';

        $status = in_array($get('status', 'active'), ['active','inactive'], true)
                  ? $get('status', 'active') : 'active';

        return [
            'service_code'         => $service_code,
            'name'                 => $name,
            'description'          => $description,
            'type_id'              => $type_id ?: null,
            'base_duration_minutes'=> $duration,
            'default_price'        => $price,
            'status'               => $status,
            'created_at'           => date('Y-m-d H:i:s'),
        ];
    }

    private function validate(array $d): array
    {
        $e = [];
        if ($d['service_code'] === '') $e[] = 'Service code is required.';
        if ($d['name'] === '')         $e[] = 'Service name is required.';
        if (!is_null($d['type_id']) && !is_int($d['type_id'])) $e[] = 'Invalid type.';
        if (!in_array($d['status'], ['active','inactive'], true)) $e[] = 'Invalid status.';
        if (!preg_match('/^\d+(\.\d{1,2})?$/', (string)$d['default_price'])) $e[] = 'Price must be a number with up to 2 decimals.';
        if (!is_int($d['base_duration_minutes']) || $d['base_duration_minutes'] < 0) $e[] = 'Duration must be a non-negative integer.';
        return $e;
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
