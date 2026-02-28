<?php
declare(strict_types=1);

namespace app\model\customer;

use PDO;

class ServiceReminder
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // your global db() helper
    }

    /**
     * Get reminder info for all vehicles owned by this user.
     *
     * Uses existing tables only:
     *  - customers
     *  - vehicles
     *  - work_orders + appointments (just to get last completed service date)
     */
    public function getForUser(int $userId): array
    {
        if (!$userId) {
            return [];
        }

        $sql = "
            SELECT 
                v.vehicle_id,
                v.make,
                v.model,
                v.license_plate AS reg_no,
                v.current_mileage,
                v.last_service_mileage,
                v.service_interval_km,

                (
                    SELECT wo.completed_at
                    FROM work_orders wo
                    JOIN appointments ap ON ap.appointment_id = wo.appointment_id
                    WHERE ap.vehicle_id = v.vehicle_id
                      AND wo.status = 'completed'
                    ORDER BY wo.completed_at DESC
                    LIMIT 1
                ) AS last_service_date

            FROM vehicles v
            JOIN customers c ON c.customer_id = v.customer_id
            WHERE c.user_id = :uid
            ORDER BY v.vehicle_id DESC
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['uid' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Update current mileage for a vehicle that belongs to this user.
     * Returns true if a row was actually updated.
     */
    public function updateMileage(int $vehicleId, int $userId, int $mileage): bool
    {
        $sql = "
            UPDATE vehicles v
            JOIN customers c ON c.customer_id = v.customer_id
            SET v.current_mileage = :mileage
            WHERE v.vehicle_id = :vid
              AND c.user_id     = :uid
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'mileage' => $mileage,
            'vid'     => $vehicleId,
            'uid'     => $userId,
        ]);

        return $stmt->rowCount() > 0;
    }
}
