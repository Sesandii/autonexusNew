<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Branch;
use app\model\admin\Manager;
use PDOException;

class BranchesController extends Controller
{
    private Branch $Branch;
    private Manager $Manager;

    // Initialize controller dependencies and request context.
    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->Branch = new Branch();
        $this->Manager = new Manager();
    }

    /** GET /admin/branches/create */
    public function create()
    {
        $this->view('admin/admin-viewbranches/create', [
            'base' => BASE_URL,
            'managers' => $this->availableManagers(),
            'nextCode' => $this->Branch->nextCode(),
            'errors' => [],
            'old' => [],
        ]);
    }


    /** GET /admin/branches */
    public function index()
    {
        $q = isset($_GET['q']) ? trim((string) $_GET['q']) : '';
        $status = isset($_GET['status']) ? trim((string) $_GET['status']) : 'all';

        $branches = $this->Branch->allWithManager($q, $status);

        $this->view('admin/admin-viewbranches/index', [
            'branches' => $branches,
            'q' => $q,
            'status' => $status,
            'base' => BASE_URL,
        ]);
    }

    /** GET /admin/branches/{code} */
    public function show($code)
    {
        $row = $this->Branch->findByCode((string) $code);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $manager = null;
        $managerId = (int) ($row['manager_id'] ?? 0);
        if ($managerId > 0) {
            $manager = $this->Manager->findWithUser($managerId);
        }

        $this->view('admin/admin-viewbranches/show', [
            'row' => $row,
            'base' => BASE_URL,
            'manager' => $manager,
        ]);
    }

    /** GET /admin/branches/{code}/edit */
    public function edit($code)
    {
        $row = $this->Branch->findByCode((string) $code);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $managers = $this->availableManagers((int) ($row['branch_id'] ?? 0));

        $this->view('admin/admin-viewbranches/edit', [
            'row' => $row,
            'base' => BASE_URL,
            'managers' => $managers,
            'errors' => [],
            'old' => [],
        ]);
    }

    /** POST /admin/branches */
    public function store()
    {
        $data = $this->sanitize($_POST);

        if ($data['branch_code'] === '') {
            $data['branch_code'] = $this->Branch->nextCode();
        }

        $errors = $this->validate($data, true);
        if ($errors) {
            http_response_code(422);
            $this->view('admin/admin-viewbranches/create', [
                'base' => BASE_URL,
                'managers' => $this->availableManagers(),
                'nextCode' => $data['branch_code'],
                'errors' => $errors,
                'old' => $this->formValues($data),
            ]);
            return;
        }

        $this->Branch->create($data);
        $this->setAdminToast('success', 'Branch created successfully.');
        header('Location: ' . BASE_URL . '/admin/branches');
        exit;
    }


    /** POST /admin/branches/{code} */
    public function update($code)
    {
        $code = (string) $code;
        $row = $this->Branch->findByCode($code);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        $data = $this->sanitize($_POST);
        $errors = $this->validate($data, false, (int) ($row['branch_id'] ?? 0));
        if ($errors) {
            http_response_code(422);
            $this->view('admin/admin-viewbranches/edit', [
                'row' => array_merge($row, $this->formValues($data)),
                'base' => BASE_URL,
                'managers' => $this->availableManagers((int) ($row['branch_id'] ?? 0)),
                'errors' => $errors,
                'old' => $this->formValues($data),
            ]);
            return;
        }

        $this->Branch->updateByCode($code, $data);
        $this->setAdminToast('success', 'Branch updated successfully.');
        header('Location: ' . BASE_URL . '/admin/branches');
        exit;
    }

    /** POST /admin/branches/{code}/delete */
    public function destroy($code)
    {
        $code = (string) $code;
        $row = $this->Branch->findByCode($code);
        if (!$row) {
            http_response_code(404);
            echo "Not found";
            return;
        }

        try {
            $this->Branch->deleteByCode($code);
            $this->setSuccessToast('Branch deleted successfully.');
        } catch (PDOException $e) {
            $sqlState = (string) ($e->errorInfo[0] ?? '');
            $mysqlCode = (int) ($e->errorInfo[1] ?? 0);

            if ($sqlState === '23000' || $mysqlCode === 1451) {
                $wasArchived = $this->Branch->archiveByCode($code);

                if ($wasArchived && (($row['status'] ?? 'active') !== 'inactive')) {
                    $this->setSuccessToast('Branch has linked records, so it was archived (set inactive) instead of being permanently deleted.');
                } elseif ($wasArchived) {
                    $this->setErrorToast('Branch is already archived and cannot be permanently deleted while linked records exist.');
                } else {
                    $this->setErrorToast('Cannot delete this branch because it is linked to existing records (appointments/services/staff).');
                }
            } else {
                $this->setErrorToast('Failed to delete branch. Please try again.');
            }
        }

        header('Location: ' . BASE_URL . '/admin/branches');
        exit;
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

        $code = strtoupper($get('code') ?: $get('branch_id')); // some UIs use branch_id text
        $name = $get('name') ?: $get('branch_name');
        $city = $get('city') ?: $get('location');

        $address_line = $get('address_line');

        $phone = substr($get('phone') ?: $get('contact'), 0, 30);

        $email = $get('email');
        $status = in_array(($get('status') ?: 'active'), ['active', 'inactive'], true)
            ? ($get('status') ?: 'active') : 'active';

        $created_raw = $get('created_at');
        if ($created_raw === '') {
            $created_at = date('Y-m-d 00:00:00');
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $created_raw)) {
            $created_at = $created_raw . ' 00:00:00';
        } elseif (preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $created_raw)) {
            $created_at = $created_raw;
        } else {
            $created_at = date('Y-m-d 00:00:00');
        }


        $capacity = is_numeric($get('capacity')) ? (int) $get('capacity') : 0;
        $staff = is_numeric($get('staff')) ? (int) $get('staff') : 0;

        $notes = $get('notes');

        $manager_raw = $get('manager');
        $manager_id = ($manager_raw !== '' && is_numeric($manager_raw)) ? (int) $manager_raw : null;

        return [
            'branch_code' => $code,
            'name' => $name,
            'city' => $city,
            'address_line' => $address_line,
            'phone' => $phone,
            'email' => $email,
            'capacity' => $capacity,
            'staff_count' => $staff,
            'notes' => $notes,
            'status' => $status,
            'created_at' => $created_at,
            'manager_id' => $manager_id,
        ];
    }

    // Handle validate operation.
    private function validate(array $d, bool $creating, int $currentBranchId = 0): array
    {
        $e = [];

        if ($d['branch_code'] === '') {
            $e[] = 'Branch code could not be generated';
        }
        if ($d['name'] === '') {
            $e[] = 'Name is required';
        }
        if ($d['city'] === '') {
            $e[] = 'City is required';
        }

        if ($d['email'] !== '' && !filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
            $e[] = 'Invalid email';
        }

        if ($d['phone'] !== '' && !preg_match('/^0\d{9}$/', $d['phone'])) {
            $e[] = 'Phone number must be 10 digits and start with 0';
        }

        if ($d['created_at'] !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$/', $d['created_at'])) {
            $e[] = 'Created at must be a valid date and time';
        }

        if (!in_array($d['status'], ['active', 'inactive'], true)) {
            $e[] = 'Invalid status';
        }

        if ($d['manager_id'] === null) {
            $e[] = 'Manager is required';
        } else {
            $manager = $this->Manager->find((int) $d['manager_id']);
            if (!$manager) {
                $e[] = 'Selected manager is invalid';
            } else {
                $assignedBranch = $this->Branch->findByManagerId((int) $d['manager_id']);
                if ($assignedBranch && (int) ($assignedBranch['branch_id'] ?? 0) !== $currentBranchId) {
                    $e[] = 'Selected manager is already assigned to another branch';
                }
            }
        }

        return array_values(array_unique($e));
    }

    // Handle availableManagers operation.
    private function availableManagers(int $currentBranchId = 0): array
    {
        $managers = $this->Manager->all();
        $available = [];

        foreach ($managers as $manager) {
            $managerId = (int) ($manager['manager_id'] ?? 0);
            if ($managerId <= 0) {
                continue;
            }

            $assignedBranch = $this->Branch->findByManagerId($managerId);
            if ($assignedBranch && (int) ($assignedBranch['branch_id'] ?? 0) !== $currentBranchId) {
                continue;
            }

            $available[] = $manager;
        }

        return $available;
    }

    // Handle formValues operation.
    private function formValues(array $data): array
    {
        return [
            'branch_code' => $data['branch_code'] ?? '',
            'name' => $data['name'] ?? '',
            'city' => $data['city'] ?? '',
            'address_line' => $data['address_line'] ?? '',
            'phone' => $data['phone'] ?? '',
            'email' => $data['email'] ?? '',
            'capacity' => $data['capacity'] ?? 0,
            'staff_count' => $data['staff_count'] ?? 0,
            'notes' => $data['notes'] ?? '',
            'status' => $data['status'] ?? 'active',
            'created_at' => $data['created_at'] ?? '',
            'manager_id' => $data['manager_id'] ?? null,
        ];
    }

    protected function setAdminToast(string $type, string $text): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION['toast_admin'] = [
            'type' => $type,
            'text' => $text,
        ];
    }

}
