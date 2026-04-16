<?php
namespace app\model\Receptionist;

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

    /**
     * Insert one vehicle for a customer.
     * Generates vehicle_code as VEH001, VEH002, etc. per customer.
     */
    public function addVehicle(int $customerId, array $v, int $index): void
    {
        $stmt = $this->db->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='vehicles'");
$nextId = $stmt->fetchColumn();

$vehicleCode = 'VEH' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
        $sql = "INSERT INTO vehicles 
                (vehicle_code, license_plate, make, model, year, color, customer_id, created_at)
                VALUES 
                (:vehicle_code, :license_plate, :make, :model, :year, :color, :customer_id, NOW())";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':vehicle_code'  => $vehicleCode,
            ':license_plate' => $v['license_plate'],
            ':make'          => $v['make'],
            ':model'         => $v['model'],
            ':year'          => $v['year'],
            ':color'         => $v['color'],
            ':customer_id'   => $customerId
        ]);
    }

    /**
     * Full flow: insert user, get customer_id, insert multiple vehicles
     */
   public function storeCustomerWithVehicles(array $userData, array $vehiclesData): int
{
    // 1️⃣ Insert user
    $userId = $this->createCustomer($userData);

    // 2️⃣ Insert customer record and generate customer_code
    $stmt = $this->db->prepare("INSERT INTO customers (user_id, customer_code, created_at) VALUES (:user_id, :customer_code, NOW())");

    // Generate code like CUS001
    $stmt2 = $this->db->query("SELECT AUTO_INCREMENT FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='customers'");
    $nextId = $stmt2->fetchColumn();
    $customerCode = 'CUS' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

    $stmt->execute([
        ':user_id'       => $userId,
        ':customer_code' => $customerCode
    ]);

    $customerId = (int) $this->db->lastInsertId();

    // 3️⃣ Insert vehicles
    foreach ($vehiclesData as $index => $vehicle) {
        $this->addVehicle($customerId, $vehicle, $index);
    }

    return $customerId;
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

// In CustomerModel

/**
 * Update user info
 */
public function updateUser(int $userId, array $data): void
{
    $sql = "UPDATE users SET
                first_name = :first_name,
                last_name  = :last_name,
                username   = :username,
                email      = :email,
                phone      = :phone
            WHERE user_id = :user_id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([
        ':first_name' => $data['first_name'],
        ':last_name'  => $data['last_name'],
        ':username'   => $data['username'],
        ':email'      => $data['email'],
        ':phone'      => $data['phone'],
        ':user_id'    => $userId
    ]);
}

/**
 * Replace all vehicles for a customer
 */
public function updateVehicles(int $customerId, array $vehicles): void
{
    // Delete existing vehicles
    $stmt = $this->db->prepare("DELETE FROM vehicles WHERE customer_id = :customer_id");
    $stmt->execute(['customer_id' => $customerId]);

    // Insert new vehicles
    foreach ($vehicles as $index => $v) {
        $this->addVehicle($customerId, $v, $index);
    }
}

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