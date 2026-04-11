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
            SUM(CASE WHEN wo.status = 'on_hold' THEN 1 ELSE 0 END) AS onhold,
            SUM(CASE WHEN wo.status NOT IN ('completed','in_progress','on_hold') THEN 1 ELSE 0 END) AS assigned
        FROM work_orders wo
        INNER JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
        WHERE m.user_id = ?
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

public function getTodayAppointments(int $branchId): array
{
    $sql = "
        SELECT 
            CONCAT(v.model, ' ', v.make) AS vehicle,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            a.appointment_time,
            s.name,
            wo.status AS work_status,
            wo.work_order_id,
            wo.mechanic_id
        FROM appointments a
        INNER JOIN work_orders wo ON a.appointment_id = wo.appointment_id
        JOIN vehicles v ON v.vehicle_id = a.vehicle_id
        JOIN customers c ON c.customer_id = a.customer_id
        JOIN users u ON u.user_id = c.user_id
        JOIN services s ON s.service_id = a.service_id
        WHERE a.branch_id = ? 
          AND DATE(a.appointment_date) = CURDATE() AND wo.status NOT IN ('completed')
        ORDER BY a.appointment_time ASC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$branchId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Get the branch ID for a specific mechanic
 */
public function getBranchIdByMechanic(int $mechanicId): ?int
{
    $stmt = $this->pdo->prepare("SELECT branch_id FROM mechanics WHERE mechanic_id = ?");
    $stmt->execute([$mechanicId]);
    return $stmt->fetchColumn() ?: null;
}

/**
 * Count appointments in a branch that are 'confirmed' but NOT yet in work_orders
 */
public function getPendingAppointmentsCountByBranch(?int $branchId): int
{
    if (!$branchId) return 0;

    $sql = "
        SELECT COUNT(*) 
        FROM appointments a
        WHERE a.branch_id = ? 
          AND a.status = 'confirmed'
          AND a.appointment_id NOT IN (
              SELECT appointment_id FROM work_orders WHERE appointment_id IS NOT NULL
          )
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$branchId]);
    return (int)$stmt->fetchColumn();
}

/**
 * Get the single most recent in-progress job for this mechanic
 */
public function getCurrentActiveJob(int $userId): ?array
{
    $sql = "
        SELECT wo.work_order_id, s.name AS service_name, CONCAT(v.make, ' ', v.model) AS vehicle
        FROM work_orders wo
        JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
        JOIN appointments a ON wo.appointment_id = a.appointment_id
        JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        JOIN services s ON a.service_id = s.service_id
        WHERE m.user_id = ? AND wo.status = 'in_progress'
        ORDER BY wo.started_at DESC LIMIT 1
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

}
