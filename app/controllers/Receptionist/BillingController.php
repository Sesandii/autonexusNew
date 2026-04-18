<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\BillingModel;
use FPDF\FPDF;

use Dompdf\Dompdf;
use Dompdf\Options;


class BillingController extends Controller
{
    
    private BillingModel $billing;

     private function guardReceptionist(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'receptionist') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
        $stmt = db()->prepare('SELECT branch_id FROM receptionists WHERE user_id = :uid LIMIT 1');
       
        $stmt->execute(['uid' => $u['user_id']]);
        $receptionist = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$receptionist) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $receptionist['branch_id'];
    }
}


    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->guardReceptionist(); // 🔐 enforce manager login & branch
        $this->billing = new BillingModel();
    }

  public function invoices(): void
{
    $status = $_GET['status'] ?? null;

    $invoices = $this->billing->getInvoices($status);
    $paidInvoices = $this->billing->getPaidInvoices();

    $this->view('Receptionist/Billing/billing', [
        'invoices' => $invoices,
        'paidInvoices' => $paidInvoices,
        'status' => $status
    ]);
}

    /** Show completed work orders */
    public function create(): void
{
    $orders = $this->billing->getCompletedWorkOrders();

    $this->view('Receptionist/Billing/createInvoice', [
        'orders' => $orders
    ]);
}

    /** Invoice preview */
    public function preview(int $id): void
    {
        $order = $this->billing->getWorkOrderForInvoice($id);

        if (!$order) {
            die('Work order already invoiced or invalid.');
        }

        $this->view('Receptionist/Billing/invoicePreview', [
            'order' => $order
        ]);
    }

    /** Generate invoice + lock work order */
public function store($id = null): void
{
    if (!$id && isset($_GET['id'])) {
        $id = $_GET['id'];
    }

    if (!$id) {
        http_response_code(404);
        exit("Missing ID");
    }

    try {
        // 1. Create invoice (DB insert)
        $this->billing->createInvoice($id);

        // 2. IMPORTANT: open PRINT PAGE directly in new tab
        header("Location: " . BASE_URL . "/receptionist/billing/printInvoice?id=$id");
        exit;

    } catch (\Throwable $e) {
        http_response_code(500);
        exit($e->getMessage());
    }
}

public function downloadInvoicePdf(): void
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(404);
        exit("Missing invoice ID");
    }

    $order = $this->billing->getWorkOrderForInvoice($id);

    if (!$order) {
        http_response_code(404);
        exit("Invoice not found");
    }

    // 🔧 DomPDF setup
    $options = new Options();
    $options->set('isRemoteEnabled', true); // allows CSS/images

    $dompdf = new Dompdf($options);

    // 📄 Capture HTML from your existing view
    ob_start();
    $this->view('Receptionist/Billing/invoicePrint', [
        'order' => $order
    ]);
    $html = ob_get_clean();

    $dompdf->loadHtml($html);

    // (optional) paper setup
    $dompdf->setPaper('A4', 'portrait');

    $dompdf->render();

    // 📥 force download
    $dompdf->stream("invoice_{$id}.pdf", [
        "Attachment" => true
    ]);

    exit;
}

public function printInvoice($id): void
{
    if (!$id) {
        http_response_code(404);
        exit("Missing ID");
    }

    $order = $this->billing->getWorkOrderForInvoice($id);

    if (!$order) {
        http_response_code(404);
        exit("Invoice not found");
    }

    $this->view('Receptionist/Billing/invoicePrint', [
        'order' => $order
    ]);
}
/**
 * Show only PAID invoices
 */
public function paidInvoices(): void
{
    $invoices = $this->billing->getPaidInvoices();

    $this->view('Receptionist/Billing/paidInvoices', [
        'invoices' => $invoices
    ]);
}

/** Mark invoice as paid */
public function markAsPaid(): void
{
    $id = $_GET['id'] ?? null;

    if (!$id) {
        http_response_code(400);
        exit("Missing invoice ID");
    }

    $this->billing->updateInvoiceStatus((int)$id, 'paid');

    header("Location: " . BASE_URL . "/receptionist/billing");
    exit;
}



}
?>