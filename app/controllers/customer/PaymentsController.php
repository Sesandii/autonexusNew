<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\PaymentModel;
use Stripe\Checkout\Session;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Stripe;
use Stripe\Webhook;
use UnexpectedValueException;

/**
 * Handles customer invoice listing and Stripe payment lifecycle endpoints.
 */
class PaymentsController extends Controller
{
    private PaymentModel $payments;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->payments = new PaymentModel();
    }

    /**
     * Render all invoices/payments visible to the logged-in customer.
     */
    public function index(): void
    {
        $this->requireCustomer();

        $invoices = $this->payments->getInvoicesByCustomerUserId($this->userId());

        $this->view('customer/payments/index', [
            'title'    => 'My Payments',
            'invoices' => $invoices,
        ]);
    }

    /**
     * Create Stripe Checkout session for a selected customer invoice.
     */
    public function checkout(int $id): void
    {
        $this->requireCustomer();

        $invoice = $this->payments->getInvoiceForCheckout($id, $this->userId());

        if (!$invoice) {
            http_response_code(404);
            exit('Invoice not found.');
        }

        if (($invoice['status'] ?? '') === 'paid') {
            $_SESSION['flash'] = 'This invoice is already paid.';
            header('Location: ' . BASE_URL . '/customer/payments');
            exit;
        }

        Stripe::setApiKey(STRIPE_SECRET_KEY);

        $baseAppUrl = rtrim(APP_URL, '/') . rtrim(BASE_URL, '/');

        $session = Session::create([
            'mode' => 'payment',
            'success_url' => $baseAppUrl . '/customer/payments/success?invoice_id=' . (int)$invoice['invoice_id'] . '&session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => $baseAppUrl . '/customer/payments/cancel?invoice_id=' . (int)$invoice['invoice_id'],
            'customer_email' => $invoice['email'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'lkr',
                    'product_data' => [
                        'name' => 'AutoNexus Invoice ' . $invoice['invoice_no'],
                    ],
                    'unit_amount' => (int) round(((float)$invoice['grand_total']) * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'invoice_id' => (string)$invoice['invoice_id'],
                'invoice_no' => (string)$invoice['invoice_no'],
            ],
        ]);

        header('Location: ' . $session->url);
        exit;
    }

    /**
     * Render post-payment success page.
     */
    public function success(): void
    {
        $this->requireCustomer();

        $invoiceId = (int)($_GET['invoice_id'] ?? 0);
        $invoice = $invoiceId > 0 ? $this->payments->getInvoiceById($invoiceId) : null;

        $this->view('customer/payments/success', [
            'title'   => 'Payment Success',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Render post-payment cancellation page.
     */
    public function cancel(): void
    {
        $this->requireCustomer();

        $invoiceId = (int)($_GET['invoice_id'] ?? 0);
        $invoice = $invoiceId > 0 ? $this->payments->getInvoiceById($invoiceId) : null;

        $this->view('customer/payments/cancel', [
            'title'   => 'Payment Cancelled',
            'invoice' => $invoice,
        ]);
    }

    /**
     * Process Stripe webhook events and mark invoice as paid.
     */
    public function webhook(): void
    {
        $payload = @file_get_contents('php://input');
        $sigHeader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                STRIPE_WEBHOOK_SECRET
            );
        } catch (UnexpectedValueException $e) {
            http_response_code(400);
            exit('Invalid payload');
        } catch (SignatureVerificationException $e) {
            http_response_code(400);
            exit('Invalid signature');
        }

        try {
            if ($event->type === 'checkout.session.completed') {
                $session = $event->data->object;

                $invoiceId = (int)($session->metadata->invoice_id ?? 0);

                $referenceNo = '';
                if (!empty($session->payment_intent)) {
                    $referenceNo = (string)$session->payment_intent;
                } elseif (!empty($session->id)) {
                    $referenceNo = (string)$session->id;
                }

                $amount = 0.00;
                if (isset($session->amount_total)) {
                    $amount = ((float)$session->amount_total) / 100;
                }

                if ($invoiceId > 0 && $referenceNo !== '') {
                    $this->payments->markInvoicePaid($invoiceId, $amount, $referenceNo);
                }
            }

            http_response_code(200);
            echo 'OK';
        } catch (\Throwable $e) {
            http_response_code(500);
            echo 'Webhook error: ' . $e->getMessage();
        }
    }
}