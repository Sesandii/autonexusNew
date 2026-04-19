<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\BillingModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class BillingController extends Controller
{
    private BillingModel $billing;

    private function guardReceptionist(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;

        if (!$u || ($u['role'] ?? '') !== 'receptionist') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        if (!isset($_SESSION['user']['branch_id'])) {
            $stmt = db()->prepare('SELECT branch_id FROM receptionists WHERE user_id = :uid LIMIT 1');
            $stmt->execute(['uid' => $u['user_id']]);
            $receptionist = $stmt->fetch(\PDO::FETCH_ASSOC);

            if (!$receptionist) {
                header('Location: ' . rtrim(BASE_URL, '/') . '/login');
                exit;
            }

            $_SESSION['user']['branch_id'] = $receptionist['branch_id'];
        }
    }

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->guardReceptionist();
        $this->billing = new BillingModel();
    }

    public function invoices(): void
    {
        $status = $_GET['status'] ?? null;

        $invoices    = $this->billing->getInvoices($status);
        $paidInvoices = $this->billing->getPaidInvoices();

        $this->view('Receptionist/Billing/billing', [
            'invoices'     => $invoices,
            'paidInvoices' => $paidInvoices,
            'status'       => $status,
        ]);
    }

    public function create(): void
    {
        $orders = $this->billing->getCompletedWorkOrders();

        $this->view('Receptionist/Billing/createInvoice', [
            'orders' => $orders,
        ]);
    }

    public function preview(int $id): void
    {
        $order = $this->billing->getWorkOrderForInvoice($id);

        if (!$order) {
            $this->view('Receptionist/Billing/invoicePreview', [
                'message' => 'Work order not found or already invoiced.',
            ]);
            return;
        }
$existing = $this->billing->getInvoiceByWorkOrderId($id);

if ($existing) {
    header('Location: ' . BASE_URL . '/receptionist/billing?error=Invoice already exists');
    exit;
}

        $this->view('Receptionist/Billing/invoicePreview', [
            'order' => $order,
        ]);
    }

    // ✅ FIX: $id now comes from the router path param (not $_GET fallback)
   public function store(int $id): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ' . BASE_URL . '/receptionist/billing/create');
        exit;
    }

    try {
        $this->billing->createInvoice($id);

        // ALWAYS redirect using work_order_id
        header('Location: ' . BASE_URL . '/receptionist/billing/printInvoice/' . $id);
        exit;

    } catch (\Throwable $e) {
        echo "ERROR: " . $e->getMessage();
        exit;
    }
}



   // ✅ FIX: $id now comes from the router path param (not $_GET)
    public function downloadInvoicePdf(int $id): void
    {
        $order = $this->billing->getInvoiceForPrint($id);

        if (!$order) {
            http_response_code(404);
            $this->view('Receptionist/error', ['message' => 'Invoice not found.']);
            return;
        }

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);

        ob_start();
        $this->view('Receptionist/Billing/invoicePrint', ['order' => $order]);
        $html = ob_get_clean();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("invoice_{$id}.pdf", ['Attachment' => true]);
        exit;
    }

    public function printInvoice(int $id): void
{
    $order = $this->billing->getInvoiceForPrint($id);

    if (!$order) {
        echo "Invoice not found.";
        return;
    }

    $this->view('Receptionist/Billing/invoicePrint', [
        'order' => $order
    ]);
}

    public function paidInvoices(): void
    {
        $invoices = $this->billing->getPaidInvoices();

        $this->view('Receptionist/Billing/paidInvoices', [
            'invoices' => $invoices,
        ]);
    }

    public function markAsPaid(): void
    {
        $id         = (int)($_GET['id'] ?? 0);
        $branchId   = $_SESSION['user']['branch_id'];

        if (!$id) {
            http_response_code(400);
            $this->view('Receptionist/error', ['message' => 'Missing invoice ID.']);
            return;
        }

        // ✅ FIX: Branch-scope check — receptionist can only mark invoices from their branch
        $invoice = $this->billing->getInvoiceByIdAndBranch($id, $branchId);

        if (!$invoice) {
            http_response_code(403);
            $this->view('Receptionist/error', ['message' => 'Invoice not found or access denied.']);
            return;
        }

        $this->billing->updateInvoiceStatus($id, 'paid');

        header('Location: ' . BASE_URL . '/receptionist/billing');
        exit;
    }
}