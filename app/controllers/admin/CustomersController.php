<?php
namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Customer;

class CustomersController extends Controller
{
    private Customer $Customer;

    public function __construct(array $config)
    {
        parent::__construct($config);
        $this->Customer = new Customer();
    }

    /** Simple input sanitizer for this controller */
    private function sanitize(array $src): array
    {
        $out = [];
        foreach ($src as $k => $v) {
            if (is_array($v)) { $out[$k] = $this->sanitize($v); continue; }
            $v = trim((string)$v);
            $v = preg_replace('/[^\P{C}\n\r\t]+/u', '', $v);
            $out[$k] = $v;
        }
        return $out;
    }

    /** GET /admin/customers */
    public function index()
    {
        $customers = $this->Customer->all();
        $this->view('admin/admin-viewcustomers/index', ['customers' => $customers]);
    }

    /** GET /admin/customers/create */
    public function create()
    {
        $this->view('admin/admin-viewcustomers/create');
    }

    /** POST /admin/customers */
    public function store()
    {
        $data = $this->sanitize($_POST);

        $errors = [];
        if (empty($data['first_name'])) $errors[] = 'First name is required';
        if (empty($data['last_name']))  $errors[] = 'Last name is required';
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';

        if ($errors) {
            http_response_code(422);
            $this->view('admin/admin-viewcustomers/create', ['errors' => $errors, 'old' => $data]);
            return;
        }

        $custId = $this->Customer->create([
            'first_name'    => $data['first_name'] ?? '',
            'last_name'     => $data['last_name']  ?? '',
            'email'         => $data['email']      ?? null,
            'phone'         => $data['phone']      ?? null,
            'status'        => $data['status']     ?? 'active',
            'password'      => $data['password']   ?? null,
            'customer_code' => $data['customer_code'] ?? null,
        ]);

        $this->redirect(BASE_URL . '/admin/customers' );
    }

    /** GET /admin/customers/{id} */
    public function show($id)
    {
        $customer = $this->Customer->find((int)$id);
        if (!$customer) { http_response_code(404); echo "Customer not found"; return; }
        $this->view('admin/admin-viewcustomers/show', ['c' => $customer]);
    }

    /** GET /admin/customers/{id}/edit */
    public function edit($id)
    {
        $customer = $this->Customer->find((int)$id);
        if (!$customer) { http_response_code(404); echo "Customer not found"; return; }
        $this->view('admin/admin-viewcustomers/edit', ['c' => $customer]);
    }

    /** POST /admin/customers/{id} */
    public function update($id)
    {
        $data = $this->sanitize($_POST);

        $this->Customer->update((int)$id, [
            'first_name'    => $data['first_name'] ?? null,
            'last_name'     => $data['last_name']  ?? null,
            'email'         => $data['email']      ?? null,
            'phone'         => $data['phone']      ?? null,
            'status'        => $data['status']     ?? null,
            'password'      => $data['password']   ?? null,
            'customer_code' => $data['customer_code'] ?? null,
        ]);

       header('Location: ' . rtrim(BASE_URL,'/') . '/admin/customers');
exit;

    }

    /** POST /admin/customers/{id}/delete */
    public function destroy($id)
    {
        $this->Customer->delete((int)$id);
        $this->redirect(BASE_URL . '/admin/customers');
    }

    /** POST /admin/customers/{id}/deactivate */
    public function deactivate($id)
    {
        $id = (int)$id;

        // Ensure customer exists
        $customer = $this->Customer->find($id);
        if (!$customer) {
            http_response_code(404);
            echo "Customer not found";
            return;
        }

        // Update status only (soft delete)
        $this->Customer->update($id, [
            'status' => 'inactive'
        ]);

        // Optionally: add success message
        $_SESSION['flash'] = "Customer has been marked as inactive.";

        $this->redirect(BASE_URL . '/admin/customers');
    }

    /** POST /admin/customers/{id}/activate */
    public function activate($id)
    {
        $id = (int)$id;

        // Ensure customer exists
        $customer = $this->Customer->find($id);
        if (!$customer) {
            http_response_code(404);
            echo "Customer not found";
            return;
        }

        // Update status only (soft "restore")
        $this->Customer->update($id, [
            'status' => 'active'
        ]);

        // Optional flash message
        $_SESSION['flash'] = "Customer has been marked as active.";

        $this->redirect(BASE_URL . '/admin/customers');
    }


}
