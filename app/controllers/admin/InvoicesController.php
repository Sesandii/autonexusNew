<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Invoice;

class InvoicesController extends Controller
{
    private Invoice $invoice;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->invoice = new Invoice();
    }

    /* ============================
 * SHOW COMPLETED WORK ORDERS
 * ============================ */
public function create(): void
{
    $this->view('admin/admin-viewinvoices/create', [
        'current'    => 'invoices',
        'workOrders' => $this->invoice->completedWorkOrders()
    ]);
}

/* ============================
 * STORE INVOICE
 * ============================ */
public function store(): void
{
    $data = [
        'work_order_id' => (int)$_POST['work_order_id'],
        'invoice_no'    => 'INV-' . time(),
        'total_amount' => (float)$_POST['total_amount'],
        'discount'     => (float)$_POST['discount'],
        'grand_total'  => (float)$_POST['grand_total']
    ];

    $this->invoice->create($data);

    header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-viewinvoices');
    exit;
}


    /* ============================
     * LIST PAGE
     * ============================ */
    public function index(): void
    {
        $this->view('admin/admin-viewinvoices/index', [
            'current'  => 'invoices',
            'invoices' => $this->invoice->getAll(),
            'summary'  => $this->invoice->summary(),
        ]);
    }

    /* ============================
     * SHOW PAGE
     * ============================ */
    public function show(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $invoice = $this->invoice->find($id);

        if (!$invoice) {
            http_response_code(404);
            echo "Invoice not found";
            return;
        }

        $this->view('admin/admin-viewinvoices/show', [
            'invoice' => $invoice,
            'current' => 'invoices',
        ]);
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || ($u['role'] ?? '') !== 'admin') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
