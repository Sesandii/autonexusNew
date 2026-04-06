<?php

declare(strict_types=1);

namespace app\model\admin;

use PDO;
use Exception;

class Payment
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function summary(): array
    {
        return [
            'collected_total' => (float)$this->db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='success'")->fetchColumn(),
            'pending_count'   => (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='pending'")->fetchColumn(),
            'failed_count'    => (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='failed'")->fetchColumn(),
            'success_count'   => (int)$this->db->query("SELECT COUNT(*) FROM payments WHERE status='success'")->fetchColumn(),
            'cash_total'      => (float)$this->db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='success' AND method='cash'")->fetchColumn(),
            'card_total'      => (float)$this->db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='success' AND method='card'")->fetchColumn(),
            'online_total'    => (float)$this->db->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='success' AND method='online'")->fetchColumn(),
        ];
    }

    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT
                p.payment_id,
                p.invoice_id,
                p.payment_date,
                p.amount,
                p.method,
                p.reference_no,
                p.status AS payment_status,

                i.invoice_no,
                i.grand_total,
                i.status AS invoice_status,
                i.issued_at,

                wo.work_order_id,
                a.appointment_id,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                u.email AS customer_email,
                u.phone AS customer_phone,
                s.name AS service_name,
                b.name AS branch_name
            FROM payments p
            INNER JOIN invoices i ON i.invoice_id = p.invoice_id
            INNER JOIN work_orders wo ON wo.work_order_id = i.work_order_id
            INNER JOIN appointments a ON a.appointment_id = wo.appointment_id
            INNER JOIN customers c ON c.customer_id = a.customer_id
            INNER JOIN users u ON u.user_id = c.user_id
            INNER JOIN services s ON s.service_id = a.service_id
            LEFT JOIN branches b ON b.branch_id = a.branch_id
            WHERE 1 = 1
        ";

        $params = [];

        if (!empty($filters['q'])) {
            $sql .= "
                AND (
                    i.invoice_no LIKE :q
                    OR p.reference_no LIKE :q
                    OR CONCAT(u.first_name, ' ', u.last_name) LIKE :q
                    OR u.email LIKE :q
                    OR s.name LIKE :q
                )
            ";
            $params[':q'] = '%' . trim((string)$filters['q']) . '%';
        }

        if (!empty($filters['status'])) {
            $sql .= " AND p.status = :status ";
            $params[':status'] = trim((string)$filters['status']);
        }

        if (!empty($filters['method'])) {
            $sql .= " AND p.method = :method ";
            $params[':method'] = trim((string)$filters['method']);
        }

        $sql .= " ORDER BY p.payment_date DESC, p.payment_id DESC ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getInvoiceOptions(): array
    {
        $sql = "
            SELECT
                i.invoice_id,
                i.invoice_no,
                i.grand_total,
                i.status,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                s.name AS service_name,
                COALESCE((
                    SELECT SUM(p.amount)
                    FROM payments p
                    WHERE p.invoice_id = i.invoice_id
                      AND p.status = 'success'
                ), 0) AS paid_amount
            FROM invoices i
            INNER JOIN work_orders wo ON wo.work_order_id = i.work_order_id
            INNER JOIN appointments a ON a.appointment_id = wo.appointment_id
            INNER JOIN customers c ON c.customer_id = a.customer_id
            INNER JOIN users u ON u.user_id = c.user_id
            INNER JOIN services s ON s.service_id = a.service_id
            WHERE i.status <> 'cancelled'
            ORDER BY i.issued_at DESC, i.invoice_id DESC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createManualPayment(array $data): int
    {
        $invoiceId = (int)($data['invoice_id'] ?? 0);
        $amount    = (float)($data['amount'] ?? 0);
        $method    = trim((string)($data['method'] ?? ''));
        $status    = trim((string)($data['status'] ?? 'pending'));
        $reference = trim((string)($data['reference_no'] ?? ''));

        if ($invoiceId <= 0) {
            throw new Exception('Invoice is required');
        }

        if ($amount <= 0) {
            throw new Exception('Amount must be greater than zero');
        }

        if (!in_array($method, ['cash', 'card', 'online'], true)) {
            throw new Exception('Invalid payment method');
        }

        if (!in_array($status, ['success', 'failed', 'pending'], true)) {
            throw new Exception('Invalid payment status');
        }

        $invoiceStmt = $this->db->prepare("SELECT invoice_id, grand_total, status FROM invoices WHERE invoice_id = :invoice_id LIMIT 1");
        $invoiceStmt->execute([':invoice_id' => $invoiceId]);
        $invoice = $invoiceStmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            throw new Exception('Invoice not found');
        }

        if (($invoice['status'] ?? '') === 'cancelled') {
            throw new Exception('Cannot add payment to a cancelled invoice');
        }

        $this->db->beginTransaction();

        try {
            $insert = $this->db->prepare(" 
                INSERT INTO payments (invoice_id, payment_date, amount, method, reference_no, status)
                VALUES (:invoice_id, NOW(), :amount, :method, :reference_no, :status)
            ");

            $insert->execute([
                ':invoice_id'    => $invoiceId,
                ':amount'        => $amount,
                ':method'        => $method,
                ':reference_no'  => $reference !== '' ? $reference : null,
                ':status'        => $status,
            ]);

            $paymentId = (int)$this->db->lastInsertId();

            $paidStmt = $this->db->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE invoice_id = :invoice_id AND status='success'");
            $paidStmt->execute([':invoice_id' => $invoiceId]);
            $paidTotal = (float)$paidStmt->fetchColumn();

            $invoiceStatus = ($paidTotal >= (float)$invoice['grand_total']) ? 'paid' : 'unpaid';
            $updateInvoice = $this->db->prepare("UPDATE invoices SET status = :status WHERE invoice_id = :invoice_id");
            $updateInvoice->execute([
                ':status'     => $invoiceStatus,
                ':invoice_id' => $invoiceId,
            ]);

            $this->db->commit();
            return $paymentId;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function cancelInvoice(int $invoiceId): bool
    {
        $check = $this->db->prepare("SELECT COUNT(*) FROM payments WHERE invoice_id = :invoice_id AND status='success'");
        $check->execute([':invoice_id' => $invoiceId]);
        $successCount = (int)$check->fetchColumn();

        if ($successCount > 0) {
            throw new Exception('This invoice already has successful payments. Cancel/refund audit needs a separate refund table.');
        }

        $stmt = $this->db->prepare("UPDATE invoices SET status = 'cancelled' WHERE invoice_id = :invoice_id");
        return $stmt->execute([':invoice_id' => $invoiceId]);
    }
}