<?php
namespace app\controllers\Manager;

use app\core\Controller;
use app\model\Receptionist\BillingModel;
use FPDF\FPDF;

class BillingController extends Controller
{
        private function guardManager(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $u = $_SESSION['user'] ?? null;

    // Check role
    if (!$u || ($u['role'] ?? '') !== 'manager') {
        header('Location: ' . rtrim(BASE_URL, '/') . '/login');
        exit;
    }

    // Load branch_id if not set yet
    if (!isset($_SESSION['user']['branch_id'])) {
       $stmt = db()->prepare('SELECT branch_id FROM managers WHERE user_id = :uid LIMIT 1');
       
        $stmt->execute(['uid' => $u['user_id']]);
        $manager = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$manager) {
            // Something is wrong: user exists but not a manager in table
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }

        $_SESSION['user']['branch_id'] = $manager['branch_id'];
    }
}

    private BillingModel $billing;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->guardManager(); // 🔐 enforce manager login & branch
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