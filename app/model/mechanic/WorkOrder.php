<?php
namespace app\model\mechanic;

use PDO;
use app\core\Database;

class WorkOrder 
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // same helper used for supervisor
    }

    /** Get assigned jobs for a single mechanic */
    public static function getAssignedJobs(int $mechanic_id): array
    {
        $pdo = db();

        $sql = "
            SELECT 
                w.work_order_id,
                w.service_summary,
                w.started_at,
                w.completed_at,
                w.status,
                s.base_duration_minutes,
                s.name,
                u.first_name,
                u.last_name,
                u.street_address,
                u.city,
                u.state,
                a.appointment_date,
                a.appointment_time,
                v.make,
                v.model,
                v.license_plate,
                m.mechanic_code
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            JOIN users u ON a.customer_id = u.user_id
            JOIN services s ON a.service_id = s.service_id
            JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            WHERE w.mechanic_id = ?
            ORDER BY w.started_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$mechanic_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Get assigned jobs for multiple mechanics */
    public static function getAssignedJobsMultiple(array $mechanic_ids): array
    {
        if (empty($mechanic_ids)) return [];

        $pdo = db();
        $placeholders = implode(',', array_fill(0, count($mechanic_ids), '?'));

        $sql = "
            SELECT 
                w.work_order_id,
                w.service_summary,
                w.started_at,
                w.completed_at,
                w.status,
                u.first_name,
                u.last_name,
                u.street_address,
                u.city,
                s.base_duration_minutes,
                s.name,
                u.state,
                a.appointment_date,
                a.appointment_time,
                v.make,
                v.model,
                v.license_plate,
                m.mechanic_code
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            JOIN users u ON a.customer_id = u.user_id
            JOIN services s ON a.service_id = s.service_id
            JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            WHERE w.mechanic_id IN ($placeholders)
            ORDER BY w.started_at DESC
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($mechanic_ids);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Get single job */
    public static function getSingleJob(int $work_order_id): ?array
    {
        $pdo = db();

        $sql = "
            SELECT
                w.*,
                a.appointment_date,
                a.appointment_time,
                a.notes,
    
                v.make,
                v.model,
                v.year,
                v.license_plate,
                v.mileage,
                v.color,
                v.vin,
                u.first_name,
                u.last_name,
                u.street_address,
                u.city,
                u.state,
                m.mechanic_code AS assigned_mechanic_code,
                m.mechanic_id
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            JOIN services s ON a.service_id = s.service_id
            JOIN users u ON a.customer_id = u.user_id
            JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            WHERE w.work_order_id = :id
            LIMIT 1
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $work_order_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /** Mechanic updates job status */
    public function setStatusMechanic(int $workOrderId, string $newStatus, int $mechanicId): void
{
    $stmt = $this->pdo->prepare("SELECT status, job_start_time, completed_at, mechanic_id FROM work_orders WHERE work_order_id = :id LIMIT 1");
    $stmt->execute(['id' => $workOrderId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || (int)$row['mechanic_id'] !== $mechanicId) return;

    $oldStatus = $row['status'];
    $jobStart = $row['job_start_time'];
    $completed = $row['completed_at'];

    date_default_timezone_set('Asia/Colombo');

    if (($oldStatus === 'open' || $oldStatus === null) && $newStatus === 'in_progress') {
        $jobStart = date('Y-m-d H:i:s');
    }

    if ($newStatus === 'completed' && $completed === null) {
        $completed = date('Y-m-d H:i:s');
    }

    $sql = "UPDATE work_orders
            SET status = :status,
                job_start_time = :job_start_time,
                completed_at = :completed_at
            WHERE work_order_id = :id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'status' => $newStatus,
        'job_start_time' => $jobStart,
        'completed_at' => $completed,
        'id' => $workOrderId
    ]);
}


    /** Get all jobs (mechanic tab) */
    public static function getAllJobs(): array
    {
        $pdo = db();

        $sql = "
            SELECT 
                w.work_order_id,
                w.service_summary,
                w.started_at,
                w.completed_at,
                w.status,
                s.name,
                s.base_duration_minutes,
                w.mechanic_id,
                cu.first_name,
                cu.last_name,
                cu.street_address,
                cu.city,
                cu.state,
                a.appointment_date,
                a.appointment_time,
                v.make,
                v.model,
                v.license_plate,
                m.mechanic_code
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            JOIN customers c ON a.customer_id = c.customer_id
            JOIN users cu ON c.user_id = cu.user_id
            JOIN services s ON a.service_id = s.service_id
            JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            ORDER BY w.started_at DESC
        ";

        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
