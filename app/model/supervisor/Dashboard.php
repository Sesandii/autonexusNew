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
        $sql = "
            SELECT
                COUNT(*) AS total,
                SUM(status = 'completed') AS completed,
                SUM(status = 'in_progress') AS ongoing
            FROM work_orders
            WHERE supervisor_id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$supervisor_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTodayAppointments(): array
{
    $sql = "
        SELECT 
            CONCAT(v.model, ' ', v.make) AS vehicle,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            a.appointment_time,
            s.name,
            a.status
        FROM appointments a
        JOIN vehicles v ON v.vehicle_id = a.vehicle_id
        JOIN customers c ON c.customer_id = a.customer_id
        JOIN users u ON u.user_id = c.user_id
        JOIN services s ON s.service_id = a.service_id
        WHERE DATE(a.appointment_date) = CURDATE()
        ORDER BY a.appointment_time ASC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}
