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

     /** =====================
     *  Pending & Overdue Services
     * ===================== */
    public function pendingServices(string $from, string $to, array $statuses = ['pending','overdue'])
    {
        $placeholders = implode(",", array_fill(0, count($statuses), "?"));
        $sql = "SELECT a.appointment_id, a.appointment_date, a.status,
                       v.license_plate, s.name AS service_name
                FROM appointments a
                JOIN vehicles v ON v.vehicle_id = a.vehicle_id
                JOIN services s ON s.service_id = a.service_id
                WHERE a.status IN ($placeholders)
                  AND a.appointment_date BETWEEN ? AND ?
                ORDER BY a.appointment_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(array_merge($statuses, [$from, $to]));
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** =====================
     *  Mechanic / Technician Performance
     * ===================== */
    /*public function mechanicPerformance(string $from, string $to, array $metrics = [], ?int $mechanicId = null)
    {
        $selects = ["m.mechanic_code", "u.first_name", "u.last_name"];
        if (in_array('jobs_done', $metrics)) $selects[] = "COUNT(w.work_order_id) AS jobs_done";
        if (in_array('avg_time', $metrics)) $selects[] = "AVG(TIMESTAMPDIFF(MINUTE, w.started_at, w.completed_at)) AS avg_time";
        if (in_array('revenue', $metrics)) $selects[] = "SUM(w.total_cost) AS revenue";

        $sql = "SELECT " . implode(", ", $selects) . "
                FROM work_orders w
                JOIN mechanics m ON m.mechanic_id = w.mechanic_id
                JOIN users u ON u.user_id = m.user_id
                WHERE w.completed_at BETWEEN :from AND :to";

        if ($mechanicId) $sql .= " AND w.mechanic_id = :mid";

        $sql .= " GROUP BY w.mechanic_id";

if (in_array('jobs_done', $metrics)) {
    $sql .= " ORDER BY jobs_done DESC";
}

        $stmt = $this->db->prepare($sql);
        $params = [':from'=>$from, ':to'=>$to];
        if ($mechanicId) $params[':mid'] = $mechanicId;
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
    }

    /** =====================
     *  Dynamic Filters
     * ===================== */
    public function getServices()
    {
        $stmt = $this->db->query("SELECT service_id, name FROM services WHERE status='active' ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMechanics()
    {
        $stmt = $this->db->query("
            SELECT m.mechanic_id, CONCAT(u.first_name,' ',u.last_name) AS name
            FROM mechanics m
            JOIN users u ON u.user_id = m.user_id
            WHERE m.status='active'
            ORDER BY name ASC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
