<?php
namespace app\controllers\Receptionist;

use app\core\Controller;
use app\model\Receptionist\BillingModel;
use FPDF\FPDF;

class BillingController extends Controller
{
    private BillingModel $billing;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->billing = new BillingModel();
    }

    public function invoices(): void
{
    $invoices = $this->billing->getInvoices();
    $paidInvoices = $this->billing->getPaidInvoices();

    $this->view('Receptionist/Billing/billing', [
        'invoices'     => $invoices,
        'paidInvoices' => $paidInvoices
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
    public function store(int $id): void
    {
        $this->billing->createInvoice($id);

        $this->redirect($this->baseUrl() . '/receptionist/billing');
    }

public function downloadInvoice(int $id): void
{
    $order = $this->billing->getWorkOrderForInvoice($id);

    if (!$order) {
        http_response_code(404);
        exit('Invalid invoice');
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



}
?>