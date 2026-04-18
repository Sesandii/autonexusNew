<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Receptionist\BillingModel;
use FPDF\FPDF;

class BillingController extends BaseManagerController
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

    $this->view('Manager/Billing/billing', [
        'invoices'     => $invoices,
        'paidInvoices' => $paidInvoices
    ]);
}

public function downloadInvoice(int $id): void
{
    $order = $this->billing->getWorkOrderForInvoice($id);

    if (!$order) {
        http_response_code(404);
        exit('Invalid invoice');
    }

    $this->view('Manager/Billing/invoicePrint', [
        'order' => $order
    ]);
}
/**
 * Show only PAID invoices
 */
public function paidInvoices(): void
{
    $invoices = $this->billing->getPaidInvoices();

    $this->view('Manager/Billing/paidInvoices', [
        'invoices' => $invoices
    ]);
}



}
?>