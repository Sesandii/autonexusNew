<?php
namespace app\model\Manager;

use PDO;

class CustomerModel
{
    protected PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Inserts a new user with role=customer.
     * Returns the newly created user_id.
     */
    public function createCustomer(array $data): int
    {
        $sql = "INSERT INTO users 
                (first_name, last_name, username, email, password_hash, phone, role, status, created_at)
                VALUES 
                (:first_name, :last_name, :username, :email, :password_hash, :phone, 'customer', 'active', NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':first_name'     => $data['first_name'],
            ':last_name'      => $data['last_name'],
            ':username'       => $data['username'],
            ':email'          => $data['email'],
            ':password_hash'  => password_hash($data['password'], PASSWORD_DEFAULT),
            ':phone'          => $data['phone'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Fetch the customer_id created by trigger for a given user_id
     */
    public function getCustomerIdByUserId(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT customer_id FROM customers WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }

    public function getAppointmentsByCustomer($customerId)
{
    $sql = "SELECT *
            FROM appointments
            WHERE customer_id = ?
            ORDER BY appointment_date DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute([$customerId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getWorkOrdersByAppointments($appointmentIds)
{
    if (empty($appointmentIds)) return [];

    $placeholders = implode(',', array_fill(0, count($appointmentIds), '?'));

    $sql = "SELECT wo.*,
                   m.first_name AS mechanic_first,
                   m.last_name AS mechanic_last,
                   s.first_name AS supervisor_first,
                   s.last_name AS supervisor_last
            FROM work_orders wo
            LEFT JOIN users m ON wo.mechanic_id = m.user_id
            LEFT JOIN users s ON wo.supervisor_id = s.user_id
            WHERE wo.appointment_id IN ($placeholders)";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($appointmentIds);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function getComplaintsByAppointments($appointmentIds)
{
    if (empty($appointmentIds)) return [];

    $placeholders = implode(',', array_fill(0, count($appointmentIds), '?'));

    $sql = "SELECT *
            FROM complaints
            WHERE appointment_id IN ($placeholders)
            ORDER BY created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute($appointmentIds);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
   

   
/**
     * Get all customers with their primary vehicle
     */
    public function getAllCustomers(): array
    {
        $sql = "
        SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            u.email,
            u.phone,
            u.status,
            c.customer_id,      
            c.customer_code,
            v.make,
            v.model,
            v.year,
            COUNT(v.vehicle_id) AS vehicle_count
        FROM users u
        INNER JOIN customers c ON u.user_id = c.user_id
        LEFT JOIN vehicles v ON c.customer_id = v.customer_id
        WHERE u.role = 'customer'
        GROUP BY u.user_id, c.customer_id, c.customer_code
        ORDER BY u.created_at DESC

        ";


        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
 * Get a single customer by customer_id including all their vehicles
 */
public function getCustomerById(int $customerId): array
{
    // Fetch user + customer info
    $sql = "
        SELECT 
            u.user_id,
            u.first_name,
            u.last_name,
            u.username,
            u.email,
            u.phone,
            u.alt_phone,
            u.street_address,
            u.city,
            u.state,
            u.status,
            c.customer_id,
            c.customer_code,
            c.created_at AS customer_since
        FROM users u
        INNER JOIN customers c ON u.user_id = c.user_id
        WHERE c.customer_id = :customer_id
        LIMIT 1
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['customer_id' => $customerId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) return [];

    // Fetch all vehicles for this customer
    $sql2 = "SELECT * FROM vehicles WHERE customer_id = :customer_id ORDER BY vehicle_id ASC";
    $stmt2 = $this->db->prepare($sql2);
    $stmt2->execute(['customer_id' => $customerId]);
    $customer['vehicles'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    return $customer;
}



/**
 * Get customer appointments with work orders and complaints
 */
public function getCustomerAppointments(int $customerId): array
{
    // Fetch appointments
    $stmt = $this->db->prepare("
        SELECT a.*, v.make, v.model, v.year, s.name AS service_name
        FROM appointments a
        LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        LEFT JOIN services s ON a.service_id = s.service_id
        WHERE a.customer_id = :customer_id
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");
    $stmt->execute(['customer_id' => $customerId]);
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($appointments as &$appt) {
        // Fetch work orders
        $stmtWO = $this->db->prepare("
            SELECT wo.*, m.first_name AS mechanic_first, m.last_name AS mechanic_last,
                   sup.first_name AS supervisor_first, sup.last_name AS supervisor_last
            FROM work_orders wo
            LEFT JOIN users m ON wo.mechanic_id = m.user_id
            LEFT JOIN users sup ON wo.supervisor_id = sup.user_id
            WHERE wo.appointment_id = :appointment_id
        ");
        $stmtWO->execute(['appointment_id' => $appt['appointment_id']]);
        $appt['work_orders'] = $stmtWO->fetchAll(PDO::FETCH_ASSOC);

        // Fetch complaints
        $stmtC = $this->db->prepare("
            SELECT c.*
            FROM complaints c
            WHERE c.appointment_id = :appointment_id
        ");
        $stmtC->execute(['appointment_id' => $appt['appointment_id']]);
        $appt['complaints'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);
    }

    return $appointments;
}
}