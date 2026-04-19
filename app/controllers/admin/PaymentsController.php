<?php

declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Payment;

class PaymentsController extends Controller
{
    private Payment $payment;

    // Initialize controller dependencies and request context.
    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->payment = new Payment();
    }

    // Display the main listing or dashboard page.
    public function index(): void
    {
        $filters = [
            'q' => trim((string) ($_GET['q'] ?? '')),
            'status' => trim((string) ($_GET['status'] ?? '')),
            'method' => trim((string) ($_GET['method'] ?? '')),
        ];

        $this->view('admin/admin-viewpayments/index', [
            'current' => 'payments',
            'pageTitle' => 'Payments Management',
            'records' => $this->payment->getAll($filters),
            'summary' => $this->payment->summary(),
            'invoiceOptions' => $this->payment->getInvoiceOptions(),
            'filters' => $filters,
        ]);
    }

    // Validate input and save a new record.
    public function store(): void
    {
        $this->payment->createManualPayment([
            'invoice_id' => $_POST['invoice_id'] ?? 0,
            'amount' => $_POST['amount'] ?? 0,
            'method' => $_POST['method'] ?? '',
            'reference_no' => $_POST['reference_no'] ?? '',
            'status' => $_POST['status'] ?? 'pending',
        ]);

        $this->setSuccessToast('Payment recorded successfully.');

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewpayments');
        exit;
    }

    // Handle cancelInvoice operation.
    public function cancelInvoice(): void
    {
        $invoiceId = (int) ($_POST['invoice_id'] ?? 0);
        if ($invoiceId <= 0) {
            http_response_code(400);
            echo 'Invalid invoice';
            return;
        }

        $this->payment->cancelInvoice($invoiceId);

        $this->setSuccessToast('Invoice cancelled successfully.');

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewpayments');
        exit;
    }

    // Ensure the current session belongs to an admin user.
    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}