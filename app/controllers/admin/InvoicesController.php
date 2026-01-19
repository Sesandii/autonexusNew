<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Invoice;
use Dompdf\Dompdf;
use Dompdf\Options;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


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

    public function download(): void
{
    $id = (int)($_GET['id'] ?? 0);
    $invoice = $this->invoice->find($id);

    if (!$invoice) {
        http_response_code(404);
        echo "Invoice not found";
        return;
    }

    // Generate HTML
    ob_start();
    include APP_ROOT . '/views/admin/admin-viewinvoices/pdf.php';
    $html = ob_get_clean();

    // Dompdf setup
    $options = new Options();
    $options->set('defaultFont', 'DejaVu Sans');
    $options->set('isRemoteEnabled', true);

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    // Filename
    $fileName =
        'Invoice_' .
        preg_replace('/\s+/', '_', $invoice['first_name']) .
        '_' . $invoice['invoice_no'] . '.pdf';

    // Force download
    $dompdf->stream($fileName, [
        'Attachment' => true
    ]);
}

public function email(): void
{
    $id = (int)($_GET['id'] ?? 0);
    $invoice = $this->invoice->find($id);

    if (!$invoice) {
        http_response_code(404);
        echo "Invoice not found";
        return;
    }

    /* ==========================
     * 1. Generate PDF
     * ========================== */
    $dompdf = new Dompdf();

    ob_start();
    include APP_ROOT . '/views/admin/admin-viewinvoices/pdf.php';
    $html = ob_get_clean();

    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $pdfContent = $dompdf->output();

    /* ==========================
     * 2. Send Email
     * ========================== */
    require BASE_PATH . '/vendor/autoload.php';

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'yourgmail@gmail.com';
        $mail->Password   = 'YOUR_APP_PASSWORD';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('yourgmail@gmail.com', 'AutoNexus');
        $mail->addAddress($invoice['email'], $invoice['first_name']);

        $fileName = 'Invoice_' . $invoice['invoice_no'] . '.pdf';
        $mail->addStringAttachment($pdfContent, $fileName);

        $mail->isHTML(true);
        $mail->Subject = 'Your Invoice - ' . $invoice['invoice_no'];

        $mail->Body = "
            <p>Dear {$invoice['first_name']},</p>
            <p>Thank you for choosing <strong>AutoNexus</strong>.</p>
            <p>Your invoice <strong>{$invoice['invoice_no']}</strong> is attached.</p>
            <p>Total: <strong>Rs.{$invoice['grand_total']}</strong></p>
            <br>
            <p>Regards,<br>AutoNexus Team</p>
        ";

        $mail->send();

        header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-viewinvoices/show?id=' . $id . '&sent=1');
        exit;

    } catch (Exception $e) {
        echo "Email failed: {$mail->ErrorInfo}";
    }
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
