<?php
namespace app\model\Manager;

use app\core\Model;
use PDO;

class AppointmentModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

// Get appointments for a specific branch on a date
public function getAppointmentsByDateAndBranch(string $date, int $branchId): array
{
    $stmt = $this->db->prepare("
        SELECT 
            a.appointment_id, 
            a.appointment_date, 
            a.appointment_time, 
            a.status, 
            a.notes,
            a.assigned_to,
            a.branch_id,
            u.first_name, 
            u.last_name,
            v.make, 
            v.model, 
            v.license_plate,
            s.name as service_name,
            b.name as branch_name,
            CONCAT(us.first_name, ' ', us.last_name) as assigned_person
        FROM appointments a
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        INNER JOIN services s ON a.service_id = s.service_id
        INNER JOIN branches b ON a.branch_id = b.branch_id
        LEFT JOIN supervisors sup ON a.assigned_to = sup.supervisor_id
        LEFT JOIN users us ON sup.user_id = us.user_id
        WHERE a.appointment_date = :date
          AND a.branch_id = :branch_id
        ORDER BY a.appointment_time ASC
    ");
    $stmt->execute([
        'date' => $date,
        'branch_id' => $branchId
    ]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Add these methods to your AppointmentModel class

public function getAppointmentById(int $id): ?array
{
    $stmt = $this->db->prepare("
        SELECT 
            a.appointment_id, 
            a.appointment_date, 
            a.appointment_time, 
            a.status, 
            a.notes,
            a.assigned_to,
            a.branch_id,
            a.customer_id,
            a.vehicle_id,
            a.service_id,
            u.first_name, 
            u.last_name,
            u.phone,
            v.make, 
            v.model, 
            v.license_plate,
            s.name as service_name,
            b.name as branch_name,
            CONCAT(us.first_name, ' ', us.last_name) as assigned_person
        FROM appointments a
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        INNER JOIN services s ON a.service_id = s.service_id
        INNER JOIN branches b ON a.branch_id = b.branch_id
        LEFT JOIN supervisors sup ON a.assigned_to = sup.supervisor_id
        LEFT JOIN users us ON sup.user_id = us.user_id
        WHERE a.appointment_id = :id
    ");
    $stmt->execute(['id' => $id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ?: null;
}

public function getSupervisorsByBranch(int $branchId): array
{
    $stmt = $this->db->prepare("
        SELECT 
            s.supervisor_id,
            s.supervisor_code,
            CONCAT(u.first_name, ' ', u.last_name) AS name
        FROM supervisors s
        INNER JOIN users u ON s.user_id = u.user_id
        WHERE s.branch_id = :branch_id
        AND s.status = 'active'
        AND u.status = 'active'
        ORDER BY u.first_name ASC
    ");

    $stmt->execute(['branch_id' => $branchId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function updateAppointmentAssignment(int $appointmentId, ?int $supervisorId, ?string $notes = null): bool
{
    // Normalize dropdown value
    if ($supervisorId === '' || $supervisorId === 0) {
        $supervisorId = null;
    }

    // 🔥 BUSINESS RULE
    if ($supervisorId === null) {
        $status = 'Requested';
    } else {
        $status = 'Confirmed';
    }

    $stmt = $this->db->prepare("
        UPDATE appointments 
        SET assigned_to = :assigned_to,
            notes = :notes,
            status = :status
        WHERE appointment_id = :appointment_id
    ");

    return $stmt->execute([
        'assigned_to' => $supervisorId,
        'notes' => $notes,
        'status' => $status,
        'appointment_id' => $appointmentId
    ]);
}

}