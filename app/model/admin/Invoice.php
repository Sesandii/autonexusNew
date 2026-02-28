<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;

class Invoice
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    /* ============================
 * COMPLETED WORK ORDERS
 * (without invoices)
 * ============================ */
public function completedWorkOrders(): array
{
    $sql = "
        SELECT
            wo.work_order_id,
            wo.total_cost,
            wo.completed_at,

            a.appointment_date,
            a.appointment_time,

            u.first_name,
            u.last_name,

            s.name AS service_name
        FROM work_orders wo
        JOIN appointments a ON a.appointment_id = wo.appointment_id
        JOIN customers c ON c.customer_id = a.customer_id
        JOIN users u ON u.user_id = c.user_id
        JOIN services s ON s.service_id = a.service_id
        LEFT JOIN invoices i ON i.work_order_id = wo.work_order_id
        WHERE wo.status = 'completed'
          AND i.invoice_id IS NULL
        ORDER BY wo.completed_at DESC
    ";

    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

/* ============================
 * CREATE INVOICE
 * ============================ */
public function create(array $data): int
{
    $sql = "
        INSERT INTO invoices (
            work_order_id,
            invoice_no,
            total_amount,
            discount,
            grand_total,
            status
        ) VALUES (
            :work_order_id,
            :invoice_no,
            :total_amount,
            :discount,
            :grand_total,
            'unpaid'
        )
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($data);

    return (int)$this->db->lastInsertId();
}
 
    /* ============================
     * LIST INVOICES (for table)
     * ============================ */
    public function getAll(): array
    {
        $sql = "
            SELECT
                i.invoice_id,
                i.invoice_no,
                i.grand_total,
                i.status,
                i.issued_at,

                u.first_name,
                u.last_name,

                s.name AS service_name
            FROM invoices i
            JOIN work_orders wo ON wo.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = wo.appointment_id
            JOIN customers c ON c.customer_id = a.customer_id
            JOIN users u ON u.user_id = c.user_id
            JOIN services s ON s.service_id = a.service_id
            ORDER BY i.issued_at DESC
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ============================
     * SINGLE INVOICE (show page)
     * ============================ */
    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                i.*,
                wo.total_cost,
                wo.service_summary,
                a.appointment_date,
                a.appointment_time,

                u.first_name,
                u.last_name,
                u.email,
                u.phone,

                v.make,
                v.model,
                v.license_plate,

                s.name AS service_name
            FROM invoices i
            JOIN work_orders wo ON wo.work_order_id = i.work_order_id
            JOIN appointments a ON a.appointment_id = wo.appointment_id
            JOIN customers c ON c.customer_id = a.customer_id
            JOIN users u ON u.user_id = c.user_id
            JOIN vehicles v ON v.vehicle_id = a.vehicle_id
            JOIN services s ON s.service_id = a.service_id
            WHERE i.invoice_id = :id
            LIMIT 1
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /* ============================
     * SUMMARY CARDS
     * ============================ */
    public function summary(): array
    {
        return [
            'total'   => (float)$this->db->query("SELECT COALESCE(SUM(grand_total),0) FROM invoices")->fetchColumn(),
            'paid'    => (float)$this->db->query("SELECT COALESCE(SUM(grand_total),0) FROM invoices WHERE status='paid'")->fetchColumn(),
            'pending' => (float)$this->db->query("SELECT COALESCE(SUM(grand_total),0) FROM invoices WHERE status='unpaid'")->fetchColumn(),
        ];
    }
}
