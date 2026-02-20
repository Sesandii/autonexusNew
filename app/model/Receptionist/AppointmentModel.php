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
        SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status, a.notes,
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


}


?>