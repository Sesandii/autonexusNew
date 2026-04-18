<?php
namespace app\model\Manager;

use PDO;

class ReportModel
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /** =====================
     *  Revenue & Sales Report
     * ===================== */
    public function revenueReport(string $from, string $to, array $metrics = [], ?int $serviceId = null)
    {
        $selects = [];

if (in_array('total_revenue', $metrics)) {
    $selects[] = "SUM(i.grand_total) AS total_revenue";
}

if (in_array('invoice_count', $metrics)) {
    $selects[] = "COUNT(i.invoice_id) AS invoice_count";
}

// IMPORTANT: never use SELECT * with GROUP BY
if (empty($selects)) {
    $selects[] = "SUM(i.grand_total) AS total_revenue";
}


        $sql = "SELECT DATE(i.issued_at) AS date, " . implode(", ", $selects) . "
                FROM invoices i
                JOIN work_orders w ON w.work_order_id = i.work_order_id
                JOIN appointments a ON a.appointment_id = w.appointment_id
                WHERE i.issued_at BETWEEN :from AND :to";

        if ($serviceId) $sql .= " AND a.service_id = :sid";

        $sql .= " GROUP BY DATE(i.issued_at) ORDER BY DATE(i.issued_at) ASC";

        $stmt = $this->db->prepare($sql);
        $params = [':from'=>$from, ':to'=>$to];
        if ($serviceId) $params[':sid'] = $serviceId;
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

     public function getServices(): array {
        return $this->db->query("SELECT service_id, name FROM services ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
    }


     public function pendingServices(string $from, string $to, ?int $branchId = null)
{
    $sql = "SELECT 
            w.work_order_id,
            w.status,
            w.job_start_time,
            w.started_at,
            w.total_cost,
            a.appointment_date,
            v.license_plate,
            s.name AS service_name,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            CASE 
                WHEN w.status = 'open'        THEN 'Pending'
                WHEN w.status = 'on_hold'     THEN 'On Hold'
                WHEN w.status = 'in_progress' 
                     AND w.job_start_time < NOW() THEN 'Overdue'
                WHEN w.status = 'in_progress' THEN 'In Progress'
                ELSE w.status
            END AS computed_status
        FROM work_orders w
        JOIN appointments a ON a.appointment_id = w.appointment_id
        JOIN vehicles     v ON v.vehicle_id     = a.vehicle_id
        JOIN services     s ON s.service_id     = a.service_id
        JOIN customers    c ON c.customer_id    = a.customer_id
        JOIN users        u ON u.user_id        = c.user_id
        WHERE w.status IN ('open', 'in_progress', 'on_hold')
          AND (
              w.job_start_time BETWEEN :from AND :to  -- has a date, filter by it
              OR w.job_start_time IS NULL              -- no date yet, always include
          )";

    if ($branchId) $sql .= " AND a.branch_id = :bid";

    $sql .= " ORDER BY 
                CASE w.status
                    WHEN 'in_progress' THEN 1
                    WHEN 'open'        THEN 2
                    WHEN 'on_hold'     THEN 3
                END,
                w.job_start_time ASC";

    $params = [':from' => $from, ':to' => $to];
    if ($branchId) $params[':bid'] = $branchId;

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

   

    /** =====================
     *  Customer Feedback Summary
     * ===================== */
   /* public function customerFeedback(string $from, string $to)
    {
        $sql = "SELECT f.feedback_id, f.rating, f.comment,
                       c.customer_code, a.appointment_date
                FROM feedback f
                JOIN appointments a ON a.appointment_id = f.appointment_id
                JOIN customers c ON c.customer_id = a.customer_id
                WHERE f.created_at BETWEEN :from AND :to
                ORDER BY f.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':from'=>$from, ':to'=>$to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** =====================
     *  Vehicle Service History
     * ===================== */
   /* public function vehicleHistory(int $vehicleId)
    {
        $sql = "SELECT w.work_order_id, w.started_at, w.completed_at,
                       s.name AS service_name, w.total_cost
                FROM work_orders w
                JOIN appointments a ON a.appointment_id = w.appointment_id
                JOIN services s ON s.service_id = a.service_id
                WHERE a.vehicle_id = :vid
                ORDER BY w.started_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':vid'=>$vehicleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** =====================
     *  Appointment & Workload
     * ===================== */
   /* public function appointmentWorkload(string $from, string $to)
    {
        $sql = "SELECT a.appointment_id, a.appointment_date, a.status,
                       COUNT(w.work_order_id) AS jobs_done
                FROM appointments a
                LEFT JOIN work_orders w ON w.appointment_id = a.appointment_id
                WHERE a.appointment_date BETWEEN :from AND :to
                GROUP BY a.appointment_id
                ORDER BY a.appointment_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':from'=>$from, ':to'=>$to]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/

   public function serviceCompletionReport(string $from, string $to, ?int $branchId = null)
{
    $sql = "SELECT 
                 w.work_order_id,
            s.name                                          AS service_name,
            DATE_FORMAT(w.started_at,   '%Y-%m-%d')         AS started_at,
            DATE_FORMAT(w.completed_at, '%Y-%m-%d')         AS completed_at,
            SEC_TO_TIME(
                GREATEST(
                    TIMESTAMPDIFF(SECOND, w.started_at, w.completed_at) 
                    - COALESCE(w.paused_remaining_seconds, 0),
                    0
                )
            )                                               AS total_duration,
            v.license_plate,
            CONCAT(v.make, ' ', v.model, ' (', v.year, ')') AS vehicle,
            CONCAT(u.first_name, ' ', u.last_name)          AS customer_name
            FROM work_orders w
            JOIN appointments a ON a.appointment_id = w.appointment_id
            JOIN services     s ON s.service_id     = a.service_id
            JOIN vehicles     v ON v.vehicle_id     = a.vehicle_id
            JOIN customers    c ON c.customer_id    = a.customer_id
            JOIN users        u ON u.user_id        = c.user_id
            WHERE w.status     = 'completed'
              AND w.started_at    IS NOT NULL
              AND w.completed_at  IS NOT NULL
              AND w.completed_at BETWEEN :from AND :to";

    if ($branchId) $sql .= " AND a.branch_id = :bid";

    $sql .= " ORDER BY w.completed_at DESC";

    $params = [':from' => $from, ':to' => $to];
    if ($branchId) $params[':bid'] = $branchId;

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}