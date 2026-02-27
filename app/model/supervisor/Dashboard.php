<?php
namespace app\model\supervisor;

use PDO;

class Dashboard
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function getWorkorderStats(int $supervisor_id): array
{
    $default = [
        'total' => 0,
        'in_progress' => 0,
        'my_assigned' => 0,
        'pending_appointments' => 0
    ];

    try {
        $sql = "
            SELECT
                (SELECT COUNT(*) FROM work_orders) AS total,
                (SELECT COUNT(*) FROM work_orders WHERE status = 'in_progress') AS in_progress,
                (SELECT COUNT(*) FROM work_orders WHERE supervisor_id = ?) AS my_assigned,
                (
                    SELECT COUNT(*)
                    FROM appointments a
                    LEFT JOIN work_orders w 
                      ON w.appointment_id = a.appointment_id
                    WHERE
                      w.work_order_id IS NULL
                ) AS pending_appointments
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$supervisor_id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return is_array($row) ? array_merge($default, $row) : $default;

    } catch (\Throwable $e) {
        // Optional: log $e->getMessage()
        return $default;
    }
}


    public function getTodayAppointments(): array
{
    $sql = "
        SELECT 
            a.appointment_id,
            CONCAT(v.model, ' ', v.make) AS vehicle,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            a.appointment_time,
            s.name,
            a.status,
            w.work_order_id
        FROM appointments a
        JOIN vehicles v ON v.vehicle_id = a.vehicle_id
        JOIN customers c ON c.customer_id = a.customer_id
        JOIN users u ON u.user_id = c.user_id
        JOIN services s ON s.service_id = a.service_id
        LEFT JOIN work_orders w ON w.appointment_id = a.appointment_id
        WHERE DATE(a.appointment_date) = CURDATE()
        ORDER BY a.appointment_time ASC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getInProgressJobs(int $supervisor_id): array
{
    $sql = "
        SELECT
            wo.work_order_id,
            CONCAT(v.make, ' ', v.model) AS vehicle,
            m.mechanic_code,
            wo.started_at
        FROM work_orders wo
        JOIN appointments a ON a.appointment_id = wo.appointment_id
        JOIN vehicles v ON v.vehicle_id = a.vehicle_id
        LEFT JOIN mechanics m ON m.mechanic_id = wo.mechanic_id
        WHERE wo.status = 'in_progress'
          AND wo.supervisor_id = ?
        ORDER BY wo.started_at ASC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$supervisor_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



}
