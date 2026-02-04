<?php
namespace app\model\mechanic;

use PDO;

class Dashboard
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); 
    }

    public function getMechanicIdByUser(int $mechanic_id ): ?int
{
    $stmt = $this->pdo->prepare("
        SELECT mechanic_id
        FROM mechanics
        WHERE user_id = ?
    ");
    $stmt->execute([$mechanic_id ]);
    return $stmt->fetchColumn() ?: null;
}

public function getWorkorderStatsByUser(int $user_id): array
{
    $sql = "
        SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN wo.status = 'completed' THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN wo.status = 'in_progress' THEN 1 ELSE 0 END) AS ongoing,
            SUM(CASE WHEN wo.status NOT IN ('completed','in_progress') THEN 1 ELSE 0 END) AS assigned
        FROM work_orders wo
        INNER JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
        WHERE m.user_id = ?
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$user_id]);
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
