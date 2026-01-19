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
        return (int)$this->pdo->lastInsertId();
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

    // Available appointments (exclude ones with work orders)
    public function getAvailableAppointments(): array
    {
        $sql = "SELECT a.*, s.name AS service_name, c.customer_code
                FROM appointments a
                LEFT JOIN services s ON a.service_id = s.service_id
                LEFT JOIN customers c ON a.customer_id = c.customer_id
                WHERE a.status IN ('requested', 'confirmed')
                  AND a.appointment_id NOT IN (
                      SELECT appointment_id FROM work_orders WHERE appointment_id IS NOT NULL
                  )
                ORDER BY a.appointment_date DESC, a.appointment_time DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Active mechanics
    public function getActiveMechanics(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM mechanics WHERE status = 'active'");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get all work orders for this supervisor
    public function getAll(int $supervisor_id): array
    {
        $sql = "SELECT w.*, a.appointment_date, a.appointment_time,
                       s.name AS service_name, s.base_duration_minutes,
                       m.mechanic_code, v.license_plate, c.customer_code,
                       u.first_name, u.last_name
                FROM work_orders w
                LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
                LEFT JOIN services s ON a.service_id = s.service_id
                LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
                LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
                LEFT JOIN customers c ON a.customer_id = c.customer_id
                LEFT JOIN users u ON c.user_id = u.user_id
                WHERE w.supervisor_id = :supervisor_id
                ORDER BY w.work_order_id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['supervisor_id' => $supervisor_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    public function setStatusFromActor(int $workOrderId, string $newStatus, ?int $actorUserId = null): void
{
    $stmt = $this->pdo->prepare("
        SELECT status, job_start_time, completed_at, appointment_id 
        FROM work_orders 
        WHERE work_order_id = :id 
        LIMIT 1
    ");
    $stmt->execute(['id' => $workOrderId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) return;

    $oldStatus     = $row['status'];
    $jobStart      = $row['job_start_time'];
    $completed     = $row['completed_at'];
    $appointmentId = (int)$row['appointment_id'];

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
     * 2) SET completed_at
     * -----------------------------
     * If new status = completed → set NOW()
     * If changed to anything else → NULL
     */
    if ($newStatus === 'completed') {
        // Always set NOW()
        $completed = date('Y-m-d H:i:s');
    } else {
        // Clear completed time when reverting
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
                   u.last_name
            FROM work_orders w
            LEFT JOIN appointments a ON w.appointment_id = a.appointment_id
            LEFT JOIN services s ON a.service_id = s.service_id
            LEFT JOIN mechanics m ON w.mechanic_id = m.mechanic_id
            LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
            LEFT JOIN customers c ON a.customer_id = c.customer_id
            LEFT JOIN users u ON c.user_id = u.user_id
            WHERE w.supervisor_id = :supervisor_id
            ORDER BY w.work_order_id DESC";

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

            -- Mechanic
            m.mechanic_code,
            mu.first_name AS mechanic_first_name,
            mu.last_name  AS mechanic_last_name,

            -- Customer
            cu.first_name AS customer_first_name,
            cu.last_name  AS customer_last_name,
            c.customer_code
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


}
