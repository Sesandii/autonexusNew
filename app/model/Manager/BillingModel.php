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

    // In app/model/Receptionist/BillingModel.php
// Add this method if it doesn't exist
public function getInvoices(?string $status = null): array
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
        INNER JOIN work_orders wo ON i.work_order_id = wo.work_order_id
        INNER JOIN appointments a ON wo.appointment_id = a.appointment_id
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
    ";

    // 🔥 FILTER LOGIC
    $params = [];

    if ($status && in_array($status, ['paid', 'unpaid', 'cancelled'])) {
        $sql .= " WHERE i.status = ?";
        $params[] = $status;
    }

    $sql .= " ORDER BY i.issued_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll();
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
        INNER JOIN work_orders wo ON i.work_order_id = wo.work_order_id
        INNER JOIN appointments a ON wo.appointment_id = a.appointment_id
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id

        WHERE i.status = 'paid'
        ORDER BY i.issued_at DESC
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll();
}


// Add this method to your Manager BillingModel
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

        WHERE wo.work_order_id = ?
    ");

    $stmt->execute([$id]);
    return $stmt->fetch();
}
}