<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\manager\BillingModel;

class BillingController extends BaseManagerController  // Remove BaseManagerController if not exists
{
    private BillingModel $billing;  // This is $billing, not $billingModel

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        
        $this->billing = new BillingModel();  // This is $billing
    }

    public function invoices(): void
{
    $status = $_GET['status'] ?? null;

    $data['status'] = $status; // pass to view
    $data['invoices'] = $this->billing->getInvoices($status);
    $data['paidInvoices'] = $this->billing->getPaidInvoices();

    $this->view('manager/Billing/billing', $data);
}

    public function printInvoice(int $id): void
{
    $invoice = $this->billing->getWorkOrderForInvoice($id);

    if (!$invoice) {
        echo "Invoice not found";
        return;
    }

    $invoice['invoice_no'] = $invoice['invoice_no'] ?? 'N/A';

    $this->view('Receptionist/Billing/invoicePrint', [
        'order' => $invoice
    ]);
}
}

