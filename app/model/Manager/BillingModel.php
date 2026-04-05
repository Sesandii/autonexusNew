<?php
namespace app\model\Manager;

use PDO;

class BillingModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getCompletedWorkOrders(): array
    {
        $sql = "
            SELECT
                wo.work_order_id,
                wo.total_cost,
                wo.status,

                u.first_name,
                u.last_name,

                v.license_plate AS vehicle_no,
                v.make,
                v.model

            FROM work_orders wo
            INNER JOIN appointments a
                ON wo.appointment_id = a.appointment_id
            INNER JOIN customers c
                ON a.customer_id = c.customer_id
            INNER JOIN users u
                ON c.user_id = u.user_id
            INNER JOIN vehicles v
                ON a.vehicle_id = v.vehicle_id
            LEFT JOIN invoices i
                ON i.work_order_id = wo.work_order_id

            WHERE wo.status = 'completed'
              AND i.invoice_id IS NULL
            ORDER BY wo.completed_at DESC
        ";

        return $this->db->query($sql)->fetchAll();
    }

    public function getWorkOrderForInvoice(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT
                wo.*,

                u.first_name,
                u.last_name,
                u.phone,

                v.license_plate AS vehicle_no,
                v.make,
                v.model,
                v.year,
                v.color

            FROM work_orders wo
            INNER JOIN appointments a
                ON wo.appointment_id = a.appointment_id
            INNER JOIN customers c
                ON a.customer_id = c.customer_id
            INNER JOIN users u
                ON c.user_id = u.user_id
            INNER JOIN vehicles v
                ON a.vehicle_id = v.vehicle_id
            LEFT JOIN invoices i
                ON i.work_order_id = wo.work_order_id

            WHERE wo.work_order_id = ?
              AND wo.status = 'completed'
              AND i.invoice_id IS NULL
        ");

        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function createInvoice(int $workOrderId): void
    {
        $this->db->beginTransaction();

        try {
            $stmt = $this->db->prepare("
                SELECT wo.total_cost
                FROM work_orders wo
                LEFT JOIN invoices i ON i.work_order_id = wo.work_order_id
                WHERE wo.work_order_id = ?
                  AND wo.status = 'completed'
                  AND i.invoice_id IS NULL
            ");
            $stmt->execute([$workOrderId]);
            $order = $stmt->fetch();

            if (!$order) {
                throw new \Exception('Work order already invoiced or invalid.');
            }

            $invoiceNo = 'INV-' . date('Y') . '-' . str_pad($workOrderId, 5, '0', STR_PAD_LEFT);

            $stmt = $this->db->prepare("
                INSERT INTO invoices
                (work_order_id, invoice_no, total_amount, discount, grand_total, issued_at, status)
                VALUES (?, ?, ?, 0, ?, NOW(), 'unpaid')
            ");
            $stmt->execute([
                $workOrderId,
                $invoiceNo,
                $order['total_cost'],
                $order['total_cost']
            ]);

            $this->db->commit();

        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function getInvoices(): array
    {
        $sql = "
            SELECT
                i.invoice_id,
                i.invoice_no,
                i.work_order_id,
                i.total_amount,
                i.discount,
                i.grand_total,
                i.issued_at,
                i.status,

                u.first_name,
                u.last_name,

                v.license_plate AS vehicle_no,
                v.make,
                v.model

            FROM invoices i
            INNER JOIN work_orders wo
                ON i.work_order_id = wo.work_order_id
            INNER JOIN appointments a
                ON wo.appointment_id = a.appointment_id
            INNER JOIN customers c
                ON a.customer_id = c.customer_id
            INNER JOIN users u
                ON c.user_id = u.user_id
            INNER JOIN vehicles v
                ON a.vehicle_id = v.vehicle_id

            ORDER BY i.issued_at DESC
        ";

        return $this->db->query($sql)->fetchAll();
    }

    public function getPaidInvoices(): array
    {
        $sql = "
            SELECT
                i.invoice_id,
                i.invoice_no,
                i.work_order_id,
                i.total_amount,
                i.discount,
                i.grand_total,
                i.issued_at,
                i.status,

                u.first_name,
                u.last_name,

                v.license_plate AS vehicle_no,
                v.make,
                v.model

            FROM invoices i
            INNER JOIN work_orders wo
                ON i.work_order_id = wo.work_order_id
            INNER JOIN appointments a
                ON wo.appointment_id = a.appointment_id
            INNER JOIN customers c
                ON a.customer_id = c.customer_id
            INNER JOIN users u
                ON c.user_id = u.user_id
            INNER JOIN vehicles v
                ON a.vehicle_id = v.vehicle_id

            WHERE i.status = 'paid'
            ORDER BY i.issued_at DESC
        ";

        return $this->db->query($sql)->fetchAll();
    }
}
?>