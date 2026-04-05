<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class PaymentModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getInvoicesByCustomerUserId(int $userId): array
    {
        $sql = "
            SELECT
                i.invoice_id,
                i.invoice_no,
                i.total_amount,
                i.discount,
                i.grand_total,
                i.issued_at,
                i.status AS invoice_status,

                wo.work_order_id,

                a.appointment_date,
                a.appointment_time,

                v.license_plate,
                v.make,
                v.model,

                s.name AS service_name
            FROM invoices i
            INNER JOIN work_orders wo ON wo.work_order_id = i.work_order_id
            INNER JOIN appointments a ON a.appointment_id = wo.appointment_id
            INNER JOIN customers c ON c.customer_id = a.customer_id
            INNER JOIN vehicles v ON v.vehicle_id = a.vehicle_id
            LEFT JOIN services s ON s.service_id = a.service_id
            WHERE c.user_id = :user_id
            ORDER BY i.issued_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function getInvoiceForCheckout(int $invoiceId, int $userId): ?array
    {
        $sql = "
            SELECT
                i.invoice_id,
                i.invoice_no,
                i.grand_total,
                i.status,
                u.email,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name
            FROM invoices i
            INNER JOIN work_orders wo ON wo.work_order_id = i.work_order_id
            INNER JOIN appointments a ON a.appointment_id = wo.appointment_id
            INNER JOIN customers c ON c.customer_id = a.customer_id
            INNER JOIN users u ON u.user_id = c.user_id
            WHERE i.invoice_id = :invoice_id
              AND c.user_id = :user_id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'invoice_id' => $invoiceId,
            'user_id'    => $userId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function getInvoiceById(int $invoiceId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM invoices
            WHERE invoice_id = :invoice_id
            LIMIT 1
        ");
        $stmt->execute(['invoice_id' => $invoiceId]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function paymentReferenceExists(string $referenceNo): bool
    {
        $stmt = $this->db->prepare("
            SELECT payment_id
            FROM payments
            WHERE reference_no = :reference_no
            LIMIT 1
        ");
        $stmt->execute(['reference_no' => $referenceNo]);

        return (bool)$stmt->fetchColumn();
    }

    public function markInvoicePaid(
        int $invoiceId,
        float $amount,
        string $referenceNo
    ): bool {
        $this->db->beginTransaction();

        try {
            if ($this->paymentReferenceExists($referenceNo)) {
                $this->db->rollBack();
                return true;
            }

            $invoice = $this->getInvoiceById($invoiceId);
            if (!$invoice) {
                throw new \Exception('Invoice not found.');
            }

            if (($invoice['status'] ?? '') === 'paid') {
                $this->db->rollBack();
                return true;
            }

            $stmt = $this->db->prepare("
                INSERT INTO payments (
                    invoice_id,
                    payment_date,
                    amount,
                    method,
                    reference_no,
                    status
                ) VALUES (
                    :invoice_id,
                    NOW(),
                    :amount,
                    'online',
                    :reference_no,
                    'success'
                )
            ");
            $stmt->execute([
                'invoice_id'    => $invoiceId,
                'amount'        => $amount,
                'reference_no'  => $referenceNo
            ]);

            $stmt = $this->db->prepare("
                UPDATE invoices
                SET status = 'paid'
                WHERE invoice_id = :invoice_id
                  AND status = 'unpaid'
            ");
            $stmt->execute([
                'invoice_id' => $invoiceId
            ]);

            $this->db->commit();
            return true;

        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}