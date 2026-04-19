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
        $this->pdo = db(); 
    }
    public function create(array $data): int
{
    $sql = "INSERT INTO work_orders 
            (appointment_id, mechanic_id, service_summary, total_cost, status, supervisor_id, started_at)
            VALUES (:appointment_id, :mechanic_id, :service_summary, :total_cost, :status, :supervisor_id, :started_at)";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'appointment_id' => $data['appointment_id'],
        'mechanic_id'    => $data['mechanic_id'] ?: null,
        'service_summary'=> $data['service_summary'],
        'total_cost'     => $data['total_cost'],
        'status'         => $data['status'],
        'supervisor_id'  => $data['supervisor_id'],
        'started_at'      => $data['started_at']
    ]);

    $workOrderId = (int)$this->pdo->lastInsertId();

    $this->updateAppointmentStatus(
        (int)$data['appointment_id'],
        $data['status']
    );

    return $workOrderId;
}
    public function update(int $id, array $data, int $supervisor_id): void
{
    $stmt = $this->pdo->prepare("SELECT supervisor_id FROM work_orders WHERE work_order_id = :id LIMIT 1");
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || ((int)$row['supervisor_id'] !== $supervisor_id)) {
        throw new \Exception("Unauthorized update");
    }

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

    public function delete(int $id, int $supervisor_id): void
    {
        $stmt = $this->pdo->prepare(
            "DELETE FROM work_orders WHERE work_order_id = :id AND supervisor_id = :supervisor_id"
        );
        $stmt->execute(['id' => $id, 'supervisor_id' => $supervisor_id]);
    }

    public function getAppointmentExists(int $appointment_id): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM work_orders WHERE appointment_id = :id");
        $stmt->execute(['id' => $appointment_id]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public function getAvailableAppointments(int $userId): array
{
    $stmt = $this->pdo->prepare("SELECT branch_id, supervisor_id FROM supervisors WHERE user_id = ?");
    $stmt->execute([$userId]);
    $supervisorData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$supervisorData) {
        return [];
    }

    $branchId = $supervisorData['branch_id'];
    $supervisorId = $supervisorData['supervisor_id']; 

    $sql = "
        SELECT a.*, 
               s.name AS service_name, 
               c.customer_code
        FROM appointments a
        LEFT JOIN services s ON a.service_id = s.service_id
        LEFT JOIN customers c ON a.customer_id = c.customer_id
        WHERE a.branch_id = ?
          AND a.assigned_to = ?  -- Now matches the supervisor_id column
          AND a.status IN ('confirmed')
          AND a.appointment_id NOT IN (
              SELECT appointment_id 
              FROM work_orders 
              WHERE appointment_id IS NOT NULL
          )
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$branchId, $supervisorId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getActiveMechanics(): array
{
    $sql = "
        SELECT 
            m.mechanic_id,
            m.mechanic_code,
            m.specialization,
            m.status,

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

public function getAll(int $branchId): array
{
    $sql = "SELECT w.*, 
                   a.appointment_date, 
                   a.appointment_time,
                   s.name AS service_name, 
                   CONCAT(v.make, ' ', v.model) AS vehicle,
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
            LEFT JOIN supervisors p ON w.supervisor_id = p.supervisor_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE p.branch_id = :branch_id
            ORDER BY w.work_order_id DESC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['branch_id' => $branchId]); 

    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    public function find(int $id): ?array
    {
        $sql = "SELECT w.*, a.appointment_date, a.appointment_time,
                       s.service_id, s.name AS service_name, s.default_price, s.base_duration_minutes,
                       m.mechanic_code, v.license_plate, v.model, v.make,v.color,v.last_service_mileage, v.service_interval_km,
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

public function setStatusFromActor(int $workOrderId, string $newStatus, ?int $actorUserId = null): void
{
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
    $baseDurationMin    = (int)($row['base_duration_minutes'] ?? 30); 
    $serviceId          = $row['service_id'];

    date_default_timezone_set('Asia/Colombo');

    if (($oldStatus === 'open' || $oldStatus === null) && $newStatus === 'in_progress') {
        $jobStart = date('Y-m-d H:i:s');
    }

    if ($oldStatus === 'in_progress' && $newStatus === 'on_hold') {
        if ($jobStart) {
            $jobStartTime = new \DateTime($jobStart);
            $now = new \DateTime();
            $elapsedSec = $now->getTimestamp() - $jobStartTime->getTimestamp();
        }
    }

    if ($newStatus === 'completed') {
        $completed = date('Y-m-d H:i:s');
    } else {
        $completed = null;
    }

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
                   CONCAT(v.make, ' ', v.model) AS vehicle,
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
            a.appointment_date,
            a.appointment_time,
            a.notes,
            s.name AS service_name,
            s.base_duration_minutes,
            v.make,
            v.model,
            v.license_plate,
            v.year,
            v.color,
            v.current_mileage,
            m.mechanic_code,
            mu.first_name AS mechanic_first_name,
            mu.last_name  AS mechanic_last_name,
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
        JOIN customers c ON a.customer_id = c.customer_id
        JOIN users cu ON c.user_id = cu.user_id
        LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
        LEFT JOIN users mu ON m.user_id = mu.user_id
        WHERE w.work_order_id = :id
        LIMIT 1";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $workOrder = $stmt->fetch();

    if (!$workOrder) return false;

    $sql2 = "SELECT * FROM checklist WHERE work_order_id = :id";
    $stm2 = $this->pdo->prepare($sql2);
    $stm2->execute(['id' => $id]);
    $workOrder['checklist'] = $stm2->fetchAll();

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


public function getCompletedWorkOrdersWithoutReport(int $branchId)
{
    $db = db();
    $sql = "
        SELECT 
            w.work_order_id,
            v.license_plate AS vehicle_number,
            u.first_name AS customer_first_name
        FROM work_orders w
        JOIN appointments a ON w.appointment_id = a.appointment_id
        JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        -- New link to reach customer names
        JOIN customers c ON a.customer_id = c.customer_id
        JOIN users u ON c.user_id = u.user_id
        WHERE a.branch_id = ? 
          AND w.status = 'completed'
          AND w.work_order_id NOT IN (SELECT work_order_id FROM reports)
    ";
    
    $stmt = $db->prepare($sql);
    $stmt->execute([$branchId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


private function updateAppointmentStatus(int $appointmentId, string $workOrderStatus): void
{
    $map = [
        'open'        => 'assigned',
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

public function countActiveByMechanicCode(string $mechanicCode, int $excludeWorkOrderId = 0): int
{
    $sql = "SELECT COUNT(*) 
            FROM work_orders w
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            WHERE m.mechanic_code = :mechanic_code
              AND w.status IN ('open','in_progress','on_hold')
              AND w.work_order_id != :exclude_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':mechanic_code' => $mechanicCode,
        ':exclude_id'    => $excludeWorkOrderId
    ]);

    return (int)$stmt->fetchColumn();
}

public function getMechanicById(int $mechanicId): ?array
{
    $sql = "SELECT * FROM mechanics WHERE mechanic_id = :id LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $mechanicId]);
    $mechanic = $stmt->fetch(\PDO::FETCH_ASSOC);
    return $mechanic ?: null;
}

public function updateMechanicStatus(string $mechanicCode): void
{
    $stmt = $this->pdo->prepare("SELECT status FROM mechanics WHERE mechanic_code = ?");
    $stmt->execute([$mechanicCode]);
    $currentStatus = strtolower($stmt->fetchColumn() ?: 'active');
    $activeCount = $this->countActiveByMechanicCode($mechanicCode);

    if ($currentStatus === 'active' || $currentStatus === 'busy') {
        $newStatus = ($activeCount >= 3) ? 'busy' : 'active';

        $sql = "UPDATE mechanics SET status = :status WHERE mechanic_code = :code";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':status' => $newStatus,
            ':code'   => $mechanicCode
        ]);
    }
}


public function hasActiveInProgressJob(int $mechanicId, int $excludeWorkOrderId = 0): bool
{
    $sql = "SELECT COUNT(*) 
            FROM work_orders 
            WHERE mechanic_id = :mid 
            AND status = 'in_progress'
            AND work_order_id != :exclude_id";

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        ':mid' => $mechanicId,
        ':exclude_id' => $excludeWorkOrderId
    ]);

    return (int)$stmt->fetchColumn() > 0;
}

public function getScheduledWorkOrdersByMechanicCode(string $mechanicCode)
{
    $sql = "SELECT wo.*, s.base_duration_minutes, s.name
            FROM work_orders wo
            JOIN mechanics m ON wo.mechanic_id = m.mechanic_id
            JOIN appointments a ON wo.appointment_id = a.appointment_id
            JOIN services s ON a.service_id = s.service_id
            WHERE m.mechanic_code = ? AND wo.status != 'completed'
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

public function updateVehicleStatus(int $vehicleId, string $status): bool
{
    $sql = "UPDATE vehicles SET status = :status WHERE vehicle_id = :id";
    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute([
        ':status' => $status,
        ':id'     => $vehicleId
    ]);
}

public function getVehicleIdByWorkOrder(int $workOrderId): ?int
{
    $sql = "SELECT a.vehicle_id 
            FROM work_orders w
            JOIN appointments a ON w.appointment_id = a.appointment_id
            WHERE w.work_order_id = :id 
            LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':id' => $workOrderId]);
    return $stmt->fetchColumn() ?: null;
}

public function getActiveMechanicsByBranch(?int $branchId): array
{
    if (!$branchId) return [];

    $sql = "
        SELECT 
            m.*, 
            u.first_name, 
            u.last_name 
        FROM mechanics m
        INNER JOIN users u ON m.user_id = u.user_id
        WHERE m.branch_id = ?
        ORDER BY m.mechanic_code ASC
    ";
    
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$branchId]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}

public function hasActiveJobInRestrictedStatuss(int $mechanicId): bool
{
    $sql = "SELECT COUNT(*) 
            FROM work_orders 
            WHERE mechanic_id = :mechanic_id 
              AND status IN ('in_progress', 'on_hold', 'completed')";
              
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['mechanic_id' => $mechanicId]);
    
    return (int)$stmt->fetchColumn() > 0;
}


public function hasActiveJobInRestrictedStatus(int $mechanicId, int $excludeWorkOrderId = 0): bool
{
    $sql = "SELECT COUNT(*) 
            FROM work_orders 
            WHERE mechanic_id = :mechanic_id 
              AND work_order_id != :exclude_id 
              AND status IN ('in_progress')";
              
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
        'mechanic_id' => $mechanicId,
        'exclude_id'  => $excludeWorkOrderId
    ]);
    
    return (int)$stmt->fetchColumn() > 0;
}

}