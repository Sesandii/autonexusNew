<?php
namespace app\model\Manager;

use PDO;

class ScheduleModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getTeamMembers(int $branchId): array
    {
        $sql = "
            SELECT user_id, first_name, last_name, role
            FROM users
            WHERE role NOT IN ('customer', 'manager')
              AND branch = :branch
            ORDER BY first_name ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['branch' => $branchId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMechanicsForDay(string $date): array
    {
        $sql = "
            SELECT DISTINCT
                m.mechanic_id,
                u.first_name,
                u.last_name,
                m.specialization
            FROM work_orders wo
            INNER JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
            INNER JOIN users u ON m.user_id = u.user_id
            INNER JOIN appointments a ON wo.appointment_id = a.appointment_id
            WHERE a.appointment_date = :date
            ORDER BY u.first_name
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['date' => $date]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getWorkOrdersForMechanic(string $date, int $mechanicId): array
    {
        $sql = "
            SELECT
                wo.work_order_id,
                wo.status,
                wo.total_cost,
                wo.service_summary,
                a.appointment_time,
                v.make AS vehicle_make,
                v.model AS vehicle_model,
                v.license_plate AS vehicle_plate,
                s.name AS service_name
            FROM work_orders wo
            INNER JOIN appointments a ON wo.appointment_id = a.appointment_id
            INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            INNER JOIN services s ON a.service_id = s.service_id
            WHERE a.appointment_date = :date
              AND wo.mechanic_id = :mechanic_id
            ORDER BY a.appointment_time
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'date' => $date,
            'mechanic_id' => $mechanicId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTeamMemberDetails(int $userId): array
{
    $sql = "
        SELECT
            first_name,
            last_name,
            phone,
            email,
            street_address,
            city,
            state
        FROM users
        WHERE user_id = :user_id
          AND role NOT IN ('customer')
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['user_id' => $userId]);

    return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
}

}
