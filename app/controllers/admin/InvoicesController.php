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

    /* ============================
     * CREATE PAGE (list completed work orders)
     * ============================ */
    public function create(): void
    {
        $this->view('admin/admin-viewinvoices/create', [
            'current'    => 'invoices',
            'workOrders' => $this->invoice->completedWorkOrders(),
        ]);
    }

    /* ============================
     * STORE INVOICE
     * ============================ */
    public function store(): void
    {
        $data = [
            'work_order_id' => (int)($_POST['work_order_id'] ?? 0),
            'invoice_no'    => 'INV-' . time(),
            'total_amount'  => (float)($_POST['total_amount'] ?? 0),
            'discount'      => (float)($_POST['discount'] ?? 0),
            'grand_total'   => (float)($_POST['grand_total'] ?? 0),
        ];

        $this->invoice->create($data);

        header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-viewinvoices');
        exit;
    }

    /* ============================
     * DOWNLOAD PDF
     * URL: /admin/admin-viewinvoices/download?id=#
     * ============================ */
    public function download(): void
    {
        $id = (int)($_GET['id'] ?? 0);
        $invoice = $this->invoice->find($id);

        if (!$invoice) {
            http_response_code(404);
            echo "Invoice not found";
            return;
        }

        // Ensure Dompdf exists (composer installed + autoload loaded)
        if (!class_exists(\Dompdf\Dompdf::class)) {
            http_response_code(500);
            echo "<pre>Dompdf not found. Run: composer require dompdf/dompdf</pre>";
            return;
        }

        // Render invoice HTML to string
        $B = rtrim(BASE_URL,'/');
        ob_start();
        // Make $invoice available inside the pdf view
        $invoice_for_pdf = $invoice; // safety alias if needed
        $invoice = $invoice_for_pdf;
        include APP_ROOT . '/views/admin/admin-viewinvoices/pdf.php';
        $html = ob_get_clean();

        // Dompdf options (use fully qualified class names)
        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', true);

        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $fileName = 'Invoice_' . ($invoice['invoice_no'] ?? 'INV') . '.pdf';

        // Force download
        $dompdf->stream($fileName, ['Attachment' => true]);
        exit;
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
