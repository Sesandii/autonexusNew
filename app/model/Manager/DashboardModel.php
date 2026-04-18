<?php
namespace app\model\Manager;

use PDO;

class DashboardModel
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = \db(); // global function from Database.php
    }

    /** Count of pending services (status = 'open') */
    public function getPendingServicesCount(): int
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM work_orders WHERE status = 'open'"
        );
        return (int) $stmt->fetchColumn();
    }

    /** Count of ongoing services (status = 'in_progress') */
    public function getOngoingServicesCount(): int
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) FROM work_orders WHERE status = 'in_progress'"
        );
        return (int) $stmt->fetchColumn();
    }

    /** Count of today’s appointments */
    public function getTodayAppointmentsCount(): int
    {
        $stmt = $this->pdo->query(
            "SELECT COUNT(*) 
             FROM appointments 
             WHERE appointment_date = CURDATE()"
        );
        return (int) $stmt->fetchColumn();
    }

    /** Most recent 5 work orders with vehicle info */
    public function getRecentActivities(): array
    {
        $stmt = $this->pdo->query(
    "SELECT 
        w.status,
        w.service_summary,
        v.license_plate AS vehicle_number,
        v.model
     FROM work_orders w
     JOIN appointments a ON w.appointment_id = a.appointment_id
     JOIN vehicles v ON a.vehicle_id = v.vehicle_id
     ORDER BY w.work_order_id DESC
     LIMIT 5"
);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}