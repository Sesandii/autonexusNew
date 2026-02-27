<?php
namespace app\model\supervisor;

use PDO;
use DateTime;
use DateInterval;

class WorkOrder
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // global db() helper
    }
    // Create new work order
    public function create(array $data): int
{
    $sql = "INSERT INTO work_orders 
            (appointment_id, mechanic_id, service_summary, total_cost, status, supervisor_id)
            VALUES (:appointment_id, :mechanic_id, :service_summary, :total_cost, :status, :supervisor_id)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'appointment_id' => $data['appointment_id'],
        'mechanic_id'    => $data['mechanic_id'] ?: null,
        'service_summary'=> $data['service_summary'],
        'total_cost'     => $data['total_cost'],
        'status'         => $data['status'],
        'supervisor_id'  => $data['supervisor_id']
    ]);

    $workOrderId = (int)$this->pdo->lastInsertId();

    // ✅ Sync appointment status (OPEN → CONFIRMED)
    $this->updateAppointmentStatus(
        (int)$data['appointment_id'],
        $data['status']
    );

    return $workOrderId;
}
    // Update work order fields (does NOT handle status/timestamps)
    public function update(int $id, array $data, int $supervisor_id): void
{
    $stmt = $this->pdo->prepare("SELECT supervisor_id FROM work_orders WHERE work_order_id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || ((int)$row['supervisor_id'] !== $supervisor_id)) {
        throw new \Exception("Unauthorized update");
    }

    // Only update normal fields, not job_start_time or completed_at
    $sql = "UPDATE work_orders SET 
                appointment_id = :appointment_id,
                mechanic_id = :mechanic_id,
                service_summary = :service_summary,
                total_cost = :total_cost
            WHERE work_order_id = :id
              AND supervisor_id = :supervisor_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'appointment_id'  => $data['appointment_id'],
        'mechanic_id'     => $data['mechanic_id'] ?? null,
        'service_summary' => $data['service_summary'],
        'total_cost'      => $data['total_cost'],
        'id'              => $id,
        'supervisor_id'   => $supervisor_id
    ]);
}


    // Delete work order
    public function delete(int $id, int $supervisor_id): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM work_orders WHERE work_order_id = :id AND supervisor_id = :supervisor_id"
        );
        $stmt->execute(['id' => $id, 'supervisor_id' => $supervisor_id]);
    }

    // Check if appointment already has a work order
    public function getAppointmentExists(int $appointment_id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM work_orders WHERE appointment_id = :id");
        $stmt->execute(['id' => $appointment_id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getAvailableAppointments(int $supervisorId): array
    {
        // Step 1: Get supervisor branch
        $stmt = $this->pdo->prepare(
            "SELECT branch_id FROM supervisors WHERE user_id = ?"
        );
        $stmt->execute([$supervisorId]);
        $branchId = $stmt->fetchColumn();
    
        if (!$branchId) {
            return [];
        }
    
        // Step 2: Fetch appointments only from that branch
        $sql = "
            SELECT a.*, 
                   s.name AS service_name, 
                   c.customer_code
            FROM appointments a
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            WHERE a.branch_id = ?
              AND a.status IN ('requested', 'confirmed')
              AND a.appointment_id NOT IN (
                  SELECT appointment_id 
                  FROM work_orders 
                  WHERE appointment_id IS NOT NULL
              )
            ORDER BY a.appointment_date DESC, a.appointment_time DESC
        ";
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$branchId]);
    
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Active mechanics
    public function getActiveMechanics(): array
{
    $sql = "
        SELECT 
            m.mechanic_id,
            m.mechanic_code,
            m.specialization,

            -- Count of open jobs
            SUM(CASE WHEN w.status = 'open' THEN 1 ELSE 0 END) AS open_jobs,

            -- Count of in_progress jobs
            SUM(CASE WHEN w.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_jobs,

            -- Count of completed jobs
            SUM(CASE WHEN w.status = 'completed' THEN 1 ELSE 0 END) AS completed_jobs,

            -- Current job in progress (if any)
            MAX(
                CASE 
                    WHEN w.status = 'in_progress' THEN CONCAT('WO-', w.work_order_id, ' (', s.name, ')')
                    ELSE NULL
                END
            ) AS current_job
        FROM mechanics m
        LEFT JOIN work_orders w ON w.mechanic_id = m.mechanic_id
        LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
        LEFT JOIN services s ON a.service_id = s.service_id
        GROUP BY m.mechanic_id, m.mechanic_code, m.specialization
        ORDER BY m.mechanic_code
    ";

    $stmt = $this->pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
    // Get all work orders for this supervisor
    public function getAll(): array
{
    $sql = "SELECT w.*, 
                   a.appointment_date, 
                   a.appointment_time,
                   s.name AS service_name, 
                   s.base_duration_minutes,
                   m.mechanic_code, 
                   p.supervisor_code,
                   v.license_plate, 
                   c.customer_code,
                   u.first_name, 
                   u.last_name
            FROM work_orders w
            LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            LEFT JOIN supervisors p ON w.supervisor_id = p.user_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            LEFT JOIN users u ON c.user_id = u.user_id
            ORDER BY w.work_order_id DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(); // ✅ REQUIRED

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

    // Find single work order
    public function find(int $id): ?array
    {
        $sql = "SELECT w.*, a.appointment_date, a.appointment_time,
                       s.service_id, s.name AS service_name, s.default_price, s.base_duration_minutes,
                       m.mechanic_code, v.license_plate, v.model, v.make,
                       c.customer_code, u.first_name AS customer_first_name, u.last_name AS customer_last_name, mu.first_name AS mechanic_first_name,mu.last_name AS mechanic_last_name
                FROM work_orders w
                LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
                LEFT JOIN services s ON a.service_id = s.service_id
                LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
                LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
                LEFT JOIN customers c ON a.customer_id = c.customer_id
                LEFT JOIN users u ON c.user_id = u.user_id
                LEFT JOIN users mu ON m.user_id = mu.user_id
                WHERE w.work_order_id = :id
                LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    // Get base duration of service
    public function getServiceByAppointment(int $appointment_id): ?array
{
    $sql = "
        SELECT 
            s.service_id,
            s.base_duration_minutes
        FROM appointments a
        JOIN services s ON s.service_id = a.service_id
        WHERE a.appointment_id = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$appointment_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row ?: null;
}

    // Update status and timestamps
    // Update status and timestamps with pause/resume support
// Update status and timestamps with pause/resume support
public function setStatusFromActor(int $workOrderId, string $newStatus, ?int $actorUserId = null): void
{
    // Get work order info, join through appointments → services to get base_duration_minutes
    $stmt = $this->pdo->prepare("
        SELECT 
            wo.status,
            wo.job_start_time,
            wo.completed_at,
            wo.appointment_id,
            a.service_id,
            s.base_duration_minutes
        FROM work_orders wo
        LEFT JOIN appointments a ON a.appointment_id = wo.appointment_id
        LEFT JOIN services s ON s.service_id = a.service_id
        WHERE wo.work_order_id = :id
        LIMIT 1
    ");
    $stmt->execute(['id' => $workOrderId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return;

    $oldStatus          = $row['status'];
    $jobStart           = $row['job_start_time'];
    $completed          = $row['completed_at'];
    $appointmentId      = (int)$row['appointment_id'];
    $baseDurationMin    = (int)($row['base_duration_minutes'] ?? 30); // fallback 30min
    $serviceId          = $row['service_id'];

    date_default_timezone_set('Asia/Colombo');

    /**
     * -----------------------------
     * 1) SET job_start_time
     * -----------------------------
     * Only when moving from OPEN → IN_PROGRESS
     */
    if (($oldStatus === 'open' || $oldStatus === null) && $newStatus === 'in_progress') {
        $jobStart = date('Y-m-d H:i:s');
    }

    /**
     * -----------------------------
     * 2) Handle pause/resume
     * -----------------------------
     * If moving from in_progress → on_hold: calculate remaining seconds
     * If moving from on_hold → in_progress: resume from paused seconds
     */
    if ($oldStatus === 'in_progress' && $newStatus === 'on_hold') {
        // Calculate elapsed seconds
        if ($jobStart) {
            $jobStartTime = new \DateTime($jobStart);
            $now = new \DateTime();
            $elapsedSec = $now->getTimestamp() - $jobStartTime->getTimestamp();
            // Store paused_remaining_seconds temporarily in DB or use another field if available
            // For now, we can keep it in job_start_time as negative (hack) or use separate column
            // Ideally, add paused_remaining_seconds column
        }
    }

    // Completed timestamp
    if ($newStatus === 'completed') {
        $completed = date('Y-m-d H:i:s');
    } else {
        $completed = null;
    }

    /**
     * -----------------------------
     * 3) UPDATE database
     * -----------------------------
     */
    $sql = "UPDATE work_orders
            SET status = :status,
                job_start_time = :job_start_time,
                completed_at = :completed_at
            WHERE work_order_id = :id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'status'         => $newStatus,
        'job_start_time' => $jobStart,
        'completed_at'   => $completed,
        'id'             => $workOrderId
    ]);

    // Sync appointment status
    $this->updateAppointmentStatus($appointmentId, $newStatus);
}

public function getAssigned(int $supervisor_id): array
{
    $sql = "SELECT w.*, 
                   a.appointment_date, 
                   a.appointment_time,
                   s.name AS service_name, 
                   s.base_duration_minutes,
                   m.mechanic_code,
                   v.license_plate, 
                   v.make,
                   v.model,
                   c.customer_code,
                   u.first_name, 
                   u.last_name,
                   COUNT(DISTINCT p.id) AS photo_count,
                   SUM(ch.status = 'completed') AS checklist_completed
            FROM work_orders w
            LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            LEFT JOIN service_photos p ON p.work_order_id = w.work_order_id
            LEFT JOIN checklist ch ON ch.work_order_id = w.work_order_id
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE w.supervisor_id = :supervisor_id
            GROUP BY w.work_order_id
            ORDER BY w.work_order_id DESC
            ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['supervisor_id' => $supervisor_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getFullJobDetails($id)
{
    $sql = "SELECT 
            w.*,

            -- Appointment
            a.appointment_date,
            a.appointment_time,
            a.notes,

            -- Service
            s.name AS service_name,
            s.base_duration_minutes,

            -- Vehicle
            v.make,
            v.model,
            v.license_plate,
            v.year,
            v.color,
            v.mileage,

            -- Mechanic
            m.mechanic_code,
            mu.first_name AS mechanic_first_name,
            mu.last_name  AS mechanic_last_name,

            -- Customer
            cu.first_name AS customer_first_name,
            cu.last_name  AS customer_last_name,
            c.customer_code,
            cu.phone,
            cu.street_address,
            cu.city,
            cu.state
            FROM work_orders w

        JOIN appointments a ON w.appointment_id = a.appointment_id
        JOIN services s ON a.service_id = s.service_id
        JOIN vehicles v ON a.vehicle_id = v.vehicle_id

        -- Customer chain
        JOIN customers c ON a.customer_id = c.customer_id
        JOIN users cu ON c.user_id = cu.user_id

        -- Mechanic chain
        LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
        LEFT JOIN users mu ON m.user_id = mu.user_id

        WHERE w.work_order_id = :id
        LIMIT 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $workOrder = $stmt->fetch();

    if (!$workOrder) return false;

    // Attach checklist
    $sql2 = "SELECT * FROM checklist WHERE work_order_id = :id";
    $stm2 = $this->pdo->prepare($sql2);
    $stm2->execute(['id' => $id]);
    $workOrder['checklist'] = $stm2->fetchAll();

    // Attach photos
    $sql3 = "SELECT * FROM service_photos WHERE work_order_id = :id";
    $stm3 = $this->pdo->prepare($sql3);
    $stm3->execute(['id' => $id]);
    $workOrder['photos'] = $stm3->fetchAll();

    return $workOrder;
}

public function getServiceIdByAppointment(int $appointmentId): ?int
{
    $sql = "SELECT service_id FROM appointments WHERE appointment_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$appointmentId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    return $row['service_id'] ?? null;
}

public function getLastInsertId(): int
{
    return (int)$this->pdo->lastInsertId();
}

public function createAndReturnId(array $data): int
{
    $sql = "INSERT INTO work_orders 
            (appointment_id, mechanic_id, service_summary, total_cost, status, supervisor_id)
            VALUES (:appointment_id, :mechanic_id, :service_summary, :total_cost, :status, :supervisor_id)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'appointment_id' => $data['appointment_id'],
        'mechanic_id'    => $data['mechanic_id'],
        'service_summary'=> $data['service_summary'],
        'total_cost'     => $data['total_cost'],
        'status'         => $data['status'],
        'supervisor_id'  => $data['supervisor_id']
    ]);

    return (int)$this->pdo->lastInsertId();
}

public function getServicePhotos($workOrderId)
    {
        $stmt = $this->pdo->prepare(
            "SELECT file_name FROM service_photos WHERE work_order_id = ?"
        );
        $stmt->execute([$workOrderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getCompletedWorkOrders(): array
{
    $sql = "
        SELECT 
            w.work_order_id,
            w.status,
            m.mechanic_code,
            a.appointment_id,
            a.customer_id,
            a.appointment_date,
            a.appointment_time,
            v.license_plate AS vehicle_number,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name
        FROM work_orders w
        INNER JOIN appointments a ON w.appointment_id = a.appointment_id
        INNER JOIN mechanics m ON w.mechanic_id = m.mechanic_id
        INNER JOIN users u ON a.customer_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        WHERE w.status = 'completed'
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getServiceSummaryFromChecklist(int $workOrderId): array
{
    $sql = "
        SELECT
            item_name,
            status
        FROM checklist
        WHERE work_order_id = :id
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $workOrderId]);

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function getCompletedWorkOrdersWithoutReport(): array
{
    $sql = "
        SELECT w.work_order_id, a.customer_id, a.appointment_date, a.appointment_time, 
               v.license_plate AS vehicle_number,
               u.first_name AS customer_first_name,
               u.last_name AS customer_last_name
        FROM work_orders w
        INNER JOIN appointments a ON w.appointment_id = a.appointment_id
        INNER JOIN users u ON a.customer_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        LEFT JOIN reports r ON w.work_order_id = r.work_order_id
        WHERE w.status = 'completed' AND r.report_id IS NULL
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

private function updateAppointmentStatus(int $appointmentId, string $workOrderStatus): void
{
    $map = [
        'open'        => 'confirmed',
        'in_progress' => 'in_service',
        'completed'   => 'completed',
    ];

    if (!isset($map[$workOrderStatus])) {
        return;
    }

    $stmt = $this->pdo->prepare(
        "UPDATE appointments SET status = :status WHERE appointment_id = :id"
    );
    $stmt->execute([
        'status' => $map[$workOrderStatus],
        'id'     => $appointmentId
    ]);
}

    public function getAllWorkOrders() {
        return $this->pdo->query("
            SELECT w.*, m.mechanic_code AS mechanic_name
            FROM work_orders w
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
        ")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignMechanic($workOrderId, $mechanicId)
{
    $sql = "UPDATE work_orders 
            SET mechanic_id = :mechanic_id,
                status = 'open'
            WHERE work_order_id = :id";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        ':mechanic_id' => $mechanicId,
        ':id'          => $workOrderId
    ]);
}


    public function updateStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE work_orders SET status=? WHERE work_order_id=?");
        return $stmt->execute([$status, $id]);
    }

    public function countWorkOrdersByMechanic($mechanicId)
{
    $sql = "
        SELECT COUNT(*) 
        FROM work_orders
        WHERE mechanic_id = ?
        AND status IN ('open', 'in_progress')
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$mechanicId]);
    return (int) $stmt->fetchColumn();
}


public function getAppointmentById(int $appointmentId): array|false
{
    $sql = "
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.status,
            a.service_id,
            s.name AS service_name,
            v.vehicle_id,
            CONCAT(v.make, ' ', v.model) AS vehicle,
            c.customer_id,
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name
        FROM appointments a
        JOIN services s ON s.service_id = a.service_id
        JOIN vehicles v ON v.vehicle_id = a.vehicle_id
        JOIN customers c ON c.customer_id = a.customer_id
        JOIN users u ON u.user_id = c.user_id
        WHERE a.appointment_id = ?
        LIMIT 1
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$appointmentId]);
    return $stmt->fetch(\PDO::FETCH_ASSOC);
}

/**
 * Count active work orders for a given mechanic code (open or in_progress)
 */
public function countActiveByMechanicCode(string $mechanicCode, int $excludeWorkOrderId = 0): int
{
    $sql = "SELECT COUNT(*) 
            FROM work_orders w
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            WHERE m.mechanic_code = :mechanic_code
              AND w.status IN ('open','in_progress')
              AND w.work_order_id != :exclude_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':mechanic_code' => $mechanicCode,
        ':exclude_id'    => $excludeWorkOrderId
    ]);

    return (int)$stmt->fetchColumn();
}

/**
 * Get mechanic record by mechanic_id
 */
public function getMechanicById(int $mechanicId): ?array
{
    $sql = "SELECT * FROM mechanics WHERE mechanic_id = :id LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $mechanicId]);
    $mechanic = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $mechanic ?: null;
}

// In app/model/supervisor/WorkOrder.php

public function updateMechanicStatus(string $mechanicCode): void
{
    // Count active work orders for this mechanic
    $activeCount = $this->countActiveByMechanicCode($mechanicCode);

    // Update status in mechanics table
    $status = $activeCount >= 5 ? 'busy' : 'available';

    $sql = "UPDATE mechanics SET status = :status WHERE mechanic_code = :code";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':status' => $status,
        ':code'   => $mechanicCode
    ]);
}

public function hasActiveInProgressJob(int $mechanicId): bool
{
    $sql = "SELECT COUNT(*) 
            FROM work_orders 
            WHERE mechanic_id = :mid 
            AND status = 'in_progress'";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':mid' => $mechanicId]);

    return $stmt->fetchColumn() > 0;
}

public function getScheduledWorkOrdersByMechanicCode(string $mechanicCode)
{
    $sql = "SELECT wo.*, s.base_duration_minutes
            FROM work_orders wo
            JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
            JOIN appointments a ON wo.appointment_id = a.appointment_id
            JOIN services s ON a.service_id = s.service_id
            WHERE m.mechanic_code = ?
            ORDER BY wo.work_order_id ASC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$mechanicCode]);
    $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    $scheduledOrders = [];
    $previousEnd = null;

    foreach ($orders as $order) {

        $duration = (int)($order['base_duration_minutes'] ?? 60);

        if ($previousEnd === null) {
            $start = !empty($order['job_start_time'])
                ? new \DateTime($order['job_start_time'])
                : new \DateTime();
        } else {
            $start = clone $previousEnd;
        }

        $end = clone $start;
        $end->modify("+{$duration} minutes");

        $order['calculated_start'] = $start->format('Y-m-d H:i:s');
        $order['calculated_end']   = $end->format('Y-m-d H:i:s');

        $previousEnd = clone $end;

        $scheduledOrders[] = $order;
    }

    return $scheduledOrders;
}



public function updateStatusAppointment($appointmentId, $status)
{
    $sql = "UPDATE appointments 
            SET status = :status
            WHERE appointment_id = :id";

    $stmt = $this->pdo->prepare($sql);

    return $stmt->execute([
        ':status' => $status,
        ':id' => $appointmentId
    ]);
}

/**
 * Update appointment status for a given appointment ID
 */
public function setAppointmentStatus(int $appointmentId, string $status): void
{
    $sql = "UPDATE appointments SET status = :status WHERE appointment_id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':status' => $status,
        ':id'     => $appointmentId
    ]);
}

public function savePausedTime(int $id, int $seconds)
{
    $pdo = db();

    $stmt = $pdo->prepare("
        UPDATE work_orders
        SET paused_remaining_seconds = ?
        WHERE work_order_id = ?
    ");

    $stmt->execute([$seconds, $id]);
}

public function resumeFromPaused(int $id)
{
    $pdo = db();

    $stmt = $pdo->prepare("
        UPDATE work_orders
        SET job_start_time = NOW(),
            paused_remaining_seconds = NULL
        WHERE work_order_id = ?
    ");

    $stmt->execute([$id]);
}



}