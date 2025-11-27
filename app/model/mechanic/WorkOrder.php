<?php
namespace app\model\mechanic;

use PDO;

class WorkOrder 
{
    private static function getDB(): PDO {
        return db(); // your global db() helper
    }

    public static function getAssignedJobs(int $mechanic_id)
    {
        $db = self::getDB();

        $sql = "
            SELECT 
                w.work_order_id,
                w.service_summary,
                w.started_at,
                w.completed_at,
                w.status,

                -- Customer details (from appointments → users)
                u.first_name,
                u.last_name,
                u.street_address,
                u.city,
                u.state,

                -- Appointment info
                a.appointment_date,
                a.appointment_time,

                -- Mechanic info
                m.mechanic_code
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN users u ON a.customer_id = u.user_id
            JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            WHERE w.mechanic_id = ?
            ORDER BY w.started_at DESC;
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([$mechanic_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
