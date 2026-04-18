<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\User;
use app\model\admin\Receptionist;
use app\model\admin\Branch;
use Exception;

class ReceptionistsController extends Controller
{
    private User $userModel;
    private Receptionist $recModel;
    private Branch $branchModel;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();

        $this->userModel   = new User();
        $this->recModel    = new Receptionist();
        $this->branchModel = new Branch();
    }

    /**
     * GET /admin/viewreceptionist
     * List all receptionists
     */
    public function index(): void
    {
        $receptionists = $this->recModel->all();

        $this->view('admin/admin-viewreceptionist/index', [
            'pageTitle'     => 'Receptionists Management',
            'current'       => 'receptionists',
            'receptionists' => $receptionists
        ]);
    }

    /**
     * GET /admin/receptionists/show?id=XX
     */
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            die("Invalid receptionist ID");
        }

        $rec = $this->recModel->find($id);
        if (!$rec) {
            die("Receptionist not found");
        }

        $this->view('admin/admin-viewreceptionist/show', [
            'pageTitle' => 'View Receptionist',
            'current'   => 'receptionists',
            'rec'       => $rec
        ]);
    }

    /**
     * GET + POST /admin/receptionists/create
     */
    public function create(): void
    {
        $errors   = [];
        $branches = $this->branchModel->allActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                if (empty($_POST['first_name']) ||
                    empty($_POST['last_name'])  ||
                    empty($_POST['username'])   ||
                    empty($_POST['password'])) {
                    throw new Exception("Required fields missing");
                }

                // create user first
                $userId = $this->userModel->create([
                    'first_name' => $_POST['first_name'],
                    'last_name'  => $_POST['last_name'],
                    'username'   => $_POST['username'],
                    'email'      => $_POST['email'] ?? '',
                    'password'   => $_POST['password'],
                    'phone'      => $_POST['phone'] ?? null,
                    'role'       => 'receptionist',
                    'status'     => $_POST['status'] ?? 'active'
                ]);

                // create receptionist linked to user
                $recId = $this->recModel->create([
                    'receptionist_code' => $_POST['receptionist_code'] ?? null,
                    'user_id'           => $userId,
                    'branch_id'         => $_POST['branch_id'] ?? null,
                    'status'            => $_POST['status'] ?? 'active'
                ]);

                header("Location: " . rtrim(BASE_URL, '/') . "/admin/receptionists/show?id=$recId");
                exit;

            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        $this->view('admin/admin-viewreceptionist/create', [
            'pageTitle' => 'Create Receptionist',
            'current'   => 'receptionists',
            'errors'    => $errors,
            'branches'  => $branches,
        ]);
    }

    /**
     * GET + POST /admin/receptionists/edit?id=XX
     */
    public function edit(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) die("Invalid receptionist ID");

        $rec = $this->recModel->find($id);
        if (!$rec) die("Receptionist not found");

        $errors   = [];
        $branches = $this->branchModel->allActive();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Update user
                $this->userModel->update((int)$rec['user_id'], [
                    'first_name' => $_POST['first_name'] ?? $rec['first_name'],
                    'last_name'  => $_POST['last_name']  ?? $rec['last_name'],
                    'username'   => $_POST['username']   ?? $rec['username'],
                    'email'      => $_POST['email']      ?? $rec['email'],
                    'phone'      => $_POST['phone']      ?? $rec['phone'],
                    'status'     => $_POST['status']     ?? $rec['status'],
                    'password'   => $_POST['password']   ?? '', // only if provided
                ]);

                // Update receptionist
                $this->recModel->update($id, [
                    'receptionist_code' => $_POST['receptionist_code'] ?? $rec['receptionist_code'],
                    'branch_id'         => $_POST['branch_id'] ?? $rec['branch_id'],
                    'status'            => $_POST['status'] ?? $rec['status'],
                ]);

                header("Location: " . rtrim(BASE_URL, '/') . "/admin/receptionists/show?id=$id");
                exit;

            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        $this->view('admin/admin-viewreceptionist/edit', [
            'pageTitle' => 'Edit Receptionist',
            'current'   => 'receptionists',
            'rec'       => $rec,
            'branches'  => $branches,
            'errors'    => $errors,
        ]);
    }

    /**
     * POST /admin/receptionists/delete
     */
    public function delete(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            die("Invalid request");
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) die("Invalid receptionist ID");

        $rec = $this->recModel->find($id);
        if (!$rec) die("Receptionist not found");

        $this->recModel->delete($id);               // receptionist row
        $this->userModel->delete((int)$rec['user_id']); // linked user

        header("Location: " . rtrim(BASE_URL, '/') . "/admin/viewreceptionist");
        exit;
    }

    /**
     * Only admin can access these pages
     */
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'admin') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
