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

    public function getSupervisorBranch(int $supervisor_id): int
{

    $sql = "SELECT branch_id FROM supervisors WHERE user_id = ? LIMIT 1";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$supervisor_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result) {

        return 0; 
    }

    return (int)$result['branch_id'];
}

public function getWorkorderStats(int $supervisor_id, int $branch_id): array
{
    $default = [
        'total' => 0,
        'in_progress' => 0,
        'my_assigned' => 0,
        'pending_appointments' => 0,
        'on_hold' => 0,
        'completed' => 0
    ];

    try {
        $sql = "
            SELECT
                -- Total work orders assigned to YOU in this branch
                (SELECT COUNT(*) FROM work_orders wo 
                 JOIN appointments app ON wo.appointment_id = app.appointment_id 
                 WHERE app.branch_id = ? AND wo.supervisor_id = ?) AS total,

                -- Your active jobs
                (SELECT COUNT(*) FROM work_orders wo 
                 JOIN appointments app ON wo.appointment_id = app.appointment_id 
                 WHERE wo.status = 'in_progress' AND app.branch_id = ? AND wo.supervisor_id = ?) AS in_progress,

                -- Your jobs on hold
                (SELECT COUNT(*) FROM work_orders wo 
                 JOIN appointments app ON wo.appointment_id = app.appointment_id 
                 WHERE wo.status = 'on_hold' AND app.branch_id = ? AND wo.supervisor_id = ?) AS on_hold,

                -- Your finished jobs
                (SELECT COUNT(*) FROM work_orders wo 
                 JOIN appointments app ON wo.appointment_id = app.appointment_id 
                 WHERE wo.status = 'completed' AND app.branch_id = ? AND wo.supervisor_id = ?) AS completed,

                -- Alias for your total assigned
                (SELECT COUNT(*) FROM work_orders WHERE supervisor_id = ?) AS my_assigned,

                -- Shared branch pool: Appointments waiting for ANY supervisor to create a work order
                (SELECT COUNT(*) FROM appointments a
                 LEFT JOIN work_orders w ON w.appointment_id = a.appointment_id
                 WHERE w.work_order_id IS NULL AND a.branch_id = ?) AS pending_appointments
        ";

        $stmt = $this->pdo->prepare($sql);
        // Bind parameters in correct order
        $stmt->execute([
            $branch_id, $supervisor_id, // total
            $branch_id, $supervisor_id, // in_progress
            $branch_id, $supervisor_id, // on_hold
            $branch_id, $supervisor_id, // completed
            $supervisor_id,             // my_assigned
            $branch_id                  // pending_appointments
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return is_array($row) ? array_merge($default, $row) : $default;

    } catch (\Throwable $e) {
        return $default;
    }
}

    public function getTodayAppointments(int $branch_id): array
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
        WHERE DATE(a.appointment_date) = CURDATE() AND a.branch_id = ? AND a.status IN ('confirmed')
        ORDER BY a.appointment_time ASC
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([$branch_id]); 
    
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

    public function getWeeklyAppointments(int $branch_id): array
{
    $sql = "
        SELECT 
            DATE(appointment_date) AS appt_date, 
            COUNT(*) AS count
        FROM appointments
        WHERE branch_id = ?
        GROUP BY DATE(appointment_date)
        ORDER BY DATE(appointment_date) DESC
        LIMIT 7
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$branch_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}