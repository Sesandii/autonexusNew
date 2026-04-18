<?php
namespace app\model\Receptionist;

use app\core\Model;
use PDO;

class AppointmentModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAppointmentsByDateAndBranch(string $date, int $branchId): array
{
    $stmt = $this->db->prepare("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.notes, a.branch_id, a.assigned_to,
               u.first_name, u.last_name,
               v.make, v.model, v.license_plate,
               s.name as service_name,
               b.name as branch_name
        FROM appointments a
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        INNER JOIN services s ON a.service_id = s.service_id
        INNER JOIN branches b ON a.branch_id = b.branch_id
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
    //Get customer + vehicles by phone number
     
    public function getCustomerByPhone(string $phone): array
    {
        // Fetch user + customer info
        $stmt = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.last_name, u.phone,
                   c.customer_id, c.customer_code
            FROM users u
            INNER JOIN customers c ON u.user_id = c.user_id
            WHERE u.phone = :phone
            LIMIT 1
        ");
        $stmt->execute(['phone' => $phone]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$customer) return [];

        // Fetch vehicles for this customer
        $stmt2 = $this->db->prepare("
            SELECT vehicle_id, vehicle_code, vin, license_plate, make, model, year, color
            FROM vehicles
            WHERE customer_id = :customer_id
            ORDER BY vehicle_id ASC
        ");
        $stmt2->execute(['customer_id' => $customer['customer_id']]);
        $customer['vehicles'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return $customer;
    }


   //Fetch all active services
     
    public function getAllServices(): array
    {
        $stmt = $this->db->prepare("SELECT service_id, service_code, name, default_price FROM services WHERE status = 'active' ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    //Fetch all active packages
     
    public function getAllPackages(): array
    {
        $stmt = $this->db->prepare("SELECT package_code, name, total_price FROM packages WHERE status = 'active' ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    //Fetch combined services and packages
    
    public function getAllServicesAndPackages(): array
    {
        return [
            'services' => $this->getAllServices(),
            'packages' => $this->getAllPackages()
        ];
    }


//Save a new appointment
 
public function saveAppointment(array $data): bool
{
    $stmt = $this->db->prepare("
        INSERT INTO appointments 
        (customer_id, branch_id, vehicle_id, service_id, appointment_date, appointment_time, status, notes, created_at, updated_at)
        VALUES 
        (:customer_id, :branch_id, :vehicle_id, :service_id, :appointment_date, :appointment_time, :status, :notes, NOW(), NOW())
    ");

    return $stmt->execute([
        'customer_id' => $data['customer_id'],
        'branch_id' => $data['branch_id'] ?? 1, // default branch if not provided
        'vehicle_id' => $data['vehicle_id'],
        'service_id' => $data['service_id'],
        'appointment_date' => $data['appointment_date'],
        'appointment_time' => $data['appointment_time'],
        'status' => $data['status'] ?? 'Requested',
        'notes' => $data['notes'] ?? null
    ]);
}

public function getAppointmentsByDate(string $date): array
{
    $stmt = $this->db->prepare("
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.notes, a.branch_id, a.assigned_to,
               u.first_name, u.last_name,
               v.make, v.model, v.license_plate,
               s.name as service_name,
               b.name as branch_name
        FROM appointments a
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        INNER JOIN services s ON a.service_id = s.service_id
        INNER JOIN branches b ON a.branch_id = b.branch_id
        WHERE a.appointment_date = :date
        ORDER BY a.appointment_time ASC
    ");
    $stmt->execute(['date' => $date]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


public function getSupervisorsByBranch(int $branch_id): array
{
    $stmt = $this->db->prepare("
        SELECT s.supervisor_id, u.first_name, u.last_name
        FROM supervisors s
        INNER JOIN users u ON s.user_id = u.user_id
        WHERE s.branch_id = :branch_id
          AND s.status = 'active'
        ORDER BY u.first_name ASC
    ");

    $stmt->execute(['branch_id' => $branch_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getAvailableSupervisors(int $branch_id, string $date, int $maxAssignments = 5): array
{
    $stmt = $this->db->prepare("
        SELECT s.supervisor_id, u.first_name, u.last_name
        FROM supervisors s
        INNER JOIN users u ON s.user_id = u.user_id
        WHERE s.branch_id = :branch_id
          AND s.status = 'active'
          AND (
              SELECT COUNT(*) 
              FROM appointments a 
              WHERE a.assigned_to = s.supervisor_id
                AND a.appointment_date = :date
          ) < :maxAssignments
        ORDER BY u.first_name ASC
    ");

    $stmt->execute([
        'branch_id' => $branch_id,
        'date' => $date,
        'maxAssignments' => $maxAssignments
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function assignSupervisorToAppointment(int $appointmentId, int $supervisorId): bool
{
    $stmt = $this->db->prepare("
        UPDATE appointments
        SET assigned_to = :supervisor_id,
            status = 'confirmed',
            updated_at = NOW()
        WHERE appointment_id = :appointment_id
    ");

    return $stmt->execute([
        'supervisor_id' => $supervisorId,
        'appointment_id' => $appointmentId
    ]);
}

public function getAppointmentById($id): array
{
    $stmt = $this->db->prepare("
        SELECT a.appointment_id, a.customer_id, a.vehicle_id, a.service_id, a.branch_id,
               a.appointment_date, a.appointment_time, a.status, a.notes,
               a.assigned_to,
               u.first_name, u.last_name, u.phone,
               v.make, v.model, v.license_plate,
               sup.supervisor_id,
               us.first_name AS sup_first_name,
               us.last_name AS sup_last_name
        FROM appointments a
        INNER JOIN customers c ON a.customer_id = c.customer_id
        INNER JOIN users u ON c.user_id = u.user_id
        INNER JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        LEFT JOIN supervisors sup ON a.assigned_to = sup.supervisor_id
        LEFT JOIN users us ON sup.user_id = us.user_id
        WHERE a.appointment_id = :id
        LIMIT 1
    ");

    $stmt->execute(['id' => $id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$appointment) {
        throw new \Exception("Appointment not found");
    }

    return $appointment;
}

public function updateAppointment(array $data): bool
{
    $stmt = $this->db->prepare("
        UPDATE appointments
        SET service_id = :service_id,
            branch_id = :branch_id,
            appointment_date = :appointment_date,
            appointment_time = :appointment_time,
            status = :status,
            notes = :notes,
            assigned_to = :assigned_to,
            updated_at = NOW()
        WHERE appointment_id = :appointment_id
    ");

    $stmt->execute([
        'appointment_id'   => $data['appointment_id'],
        'service_id'       => $data['service_id'],
        'branch_id'        => $data['branch_id'],
        'appointment_date' => $data['appointment_date'],
        'appointment_time' => $data['appointment_time'],
        'status'           => $data['status'],
        'notes'            => $data['notes'],
        'assigned_to'      => $data['assigned_to'] ?: null
    ]);

    return $stmt->rowCount() > 0;
}
}


?>