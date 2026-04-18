<?php

declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Payment;

class PaymentsController extends Controller
{
    private Payment $payment;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->payment = new Payment();
    }

    public function index(): void
    {
        $filters = [
            'q'      => trim((string)($_GET['q'] ?? '')),
            'status' => trim((string)($_GET['status'] ?? '')),
            'method' => trim((string)($_GET['method'] ?? '')),
        ];

        $this->view('admin/admin-viewpayments/index', [
            'current'        => 'payments',
            'pageTitle'      => 'Payments Management',
            'records'        => $this->payment->getAll($filters),
            'summary'        => $this->payment->summary(),
            'invoiceOptions' => $this->payment->getInvoiceOptions(),
            'filters'        => $filters,
        ]);
    }

    public function store(): void
    {
        $this->payment->createManualPayment([
            'invoice_id'    => $_POST['invoice_id'] ?? 0,
            'amount'        => $_POST['amount'] ?? 0,
            'method'        => $_POST['method'] ?? '',
            'reference_no'  => $_POST['reference_no'] ?? '',
            'status'        => $_POST['status'] ?? 'pending',
        ]);

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewpayments');
        exit;
    }

    public function cancelInvoice(): void
    {
        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        if ($invoiceId <= 0) {
            http_response_code(400);
            echo 'Invalid invoice';
            return;
        }

        $this->payment->cancelInvoice($invoiceId);

        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewpayments');
        exit;
    }

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