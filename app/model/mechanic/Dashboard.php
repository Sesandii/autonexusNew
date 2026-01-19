<?php
namespace app\model\mechanic;

use PDO;

class Dashboard
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // Use global db() helper
    }

    /**
     * Get work order statistics for the mechanic
     */
    public function getWorkorderStats(int $mechanic_id): array
{
    $stats = [
        'assigned' => 0,
        'completed' => 0,
        'ongoing' => 0,
        'total' => 0
    ];

    // Total assigned jobs
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as count
        FROM work_orders
        WHERE mechanic_id = :mechanic_id
    ");
    $stmt->execute(['mechanic_id' => $mechanic_id]);
    $stats['total'] = (int)$stmt->fetchColumn();

    // Completed jobs
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as count
        FROM work_orders
        WHERE mechanic_id = :mechanic_id AND status = 'completed'
    ");
    $stmt->execute(['mechanic_id' => $mechanic_id]);
    $stats['completed'] = (int)$stmt->fetchColumn();

    // Ongoing jobs
    $stmt = $this->pdo->prepare("
        SELECT COUNT(*) as count
        FROM work_orders
        WHERE mechanic_id = :mechanic_id AND status = 'ongoing'
    ");
    $stmt->execute(['mechanic_id' => $mechanic_id]);
    $stats['ongoing'] = (int)$stmt->fetchColumn();

    // Assigned but not started (optional)
    $stats['assigned'] = $stats['total'] - $stats['completed'] - $stats['ongoing'];

    return $stats;
}


    /**
     * Get today's appointments for the mechanic
     */
    public function getTodayAppointments(int $mechanicId): array
    {
        $today = date('Y-m-d');

        $sql = "
            SELECT 
                w.work_order_id,
                v.license_plate,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                a.appointment_time,
                s.name,
                w.status
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            JOIN users u ON a.customer_id = u.user_id
            JOIN services s ON a.service_id = s.service_id
            WHERE w.mechanic_id = ?
            AND DATE(a.appointment_date) = ?
            ORDER BY a.appointment_time ASC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$mechanicId, $today]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
