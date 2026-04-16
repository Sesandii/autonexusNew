<?php
namespace app\model\Manager;

use app\core\Model;
use PDO;

class ComplaintModel extends Model {

public function __construct() {
        $this->pdo = db();
    }

   // 4️⃣ Get complaints by customer
    public function getCustomerByPhone(string $phone): ?array {
    $stmt = $this->pdo->prepare("
        SELECT c.customer_id, u.first_name, u.last_name, u.email, u.phone
        FROM customers c
        JOIN users u ON c.user_id = u.user_id
        WHERE u.phone = :phone
        LIMIT 1
    ");
    $stmt->execute([':phone' => $phone]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}


    public function getVehiclesByCustomer(int $customer_id): array {
    $stmt = $this->pdo->prepare("
        SELECT vehicle_id, make, model, license_plate
        FROM vehicles
        WHERE customer_id = :customer_id
        ORDER BY created_at DESC
    ");
    $stmt->execute([':customer_id' => $customer_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



    // 1️⃣ Create new complaint
    public function create(array $data): int {
    // Fetch user_id automatically from customer_id
    $stmtUser = $this->pdo->prepare("SELECT user_id FROM customers WHERE customer_id = :customer_id");
    $stmtUser->execute([':customer_id' => $data['customer_id']]);
    $user_id = $stmtUser->fetchColumn();

    if (!$user_id) {
        throw new \Exception("Cannot find user linked to customer_id " . $data['customer_id']);
    }

    $stmt = $this->pdo->prepare("
    INSERT INTO complaints
    (customer_id, vehicle_id, subject, description, priority, status, assigned_to_user_id)
    VALUES (:customer_id, :vehicle_id, :subject, :description, :priority, :status, :assigned_to)
");

    $stmt->execute([
    ':customer_id' => $data['customer_id'],
    ':vehicle_id'  => $data['vehicle_id'],
    ':subject'     => $data['subject'] ?? 'General Complaint',
    ':description' => $data['description'] ?? '',
    ':priority'    => $data['priority'] ?? 'Medium',
    ':status'      => $data['status'] ?? 'Open',
    ':assigned_to' => $data['assigned_to'] ?? null
]);

    return (int)$this->pdo->lastInsertId();
}

 /*   public function create(array $data): int {
 $stmt = $this->pdo->prepare("
        INSERT INTO complaints
        (customer_id, user_id, vehicle_id, complaint_date, complaint_time, description, priority, status, assigned_to)
        VALUES (:customer_id, :user_id, :vehicle_id, :complaint_date, :complaint_time, :description, :priority, :status, :assigned_to)
    ");
    $stmt->execute([
        ':customer_id'    => $data['customer_id'],
        ':user_id'        => $data['user_id'],
        ':vehicle_id'     => $data['vehicle_id'],
        ':complaint_date' => $data['complaint_date'] ?? null,
        ':complaint_time' => $data['complaint_time'] ?? null,
        ':description'    => $data['description'] ?? '',
        ':priority'       => $data['priority'] ?? 'Medium',
        ':status'         => $data['status'] ?? 'Open',
        ':assigned_to'    => $data['assigned_to'] ?? null
    ]);
    return (int)$this->pdo->lastInsertId();
}*/


    // 2️⃣ Fetch all complaints
    public function all(): array {
    $stmt = $this->pdo->query("
        SELECT comp.*,
               CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
               CONCAT(v.make, ' ', v.model) AS vehicle,
               v.license_plate
        FROM complaints comp
        JOIN customers c ON comp.customer_id = c.customer_id
        JOIN users u ON c.user_id = u.user_id
        LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
        ORDER BY comp.created_at DESC
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


    // 3️⃣ Find complaint by ID
   public function find(int $id): ?array {
    $stmt = $this->pdo->prepare("
    SELECT 
        comp.*,
        CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
        u.phone,
        u.email,
        CONCAT(v.make, ' ', v.model) AS vehicle,
        v.license_plate AS vehicle_number,
        CONCAT(a.first_name, ' ', a.last_name) AS assigned_to
    FROM complaints comp
    LEFT JOIN customers c ON comp.customer_id = c.customer_id
    LEFT JOIN users u ON c.user_id = u.user_id
    LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
    LEFT JOIN users a ON comp.assigned_to_user_id = a.user_id
    WHERE comp.complaint_id = :id
    LIMIT 1
");
    $stmt->execute([':id' => $id]);
    $complaint = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$complaint) {
        error_log("Complaint not found for ID $id"); 
        return null;
    }

    return $complaint;
}

// Get user_id linked to a customer_id
public function getUserIdByCustomer(int $customer_id): ?int {
    $stmt = $this->pdo->prepare("SELECT user_id FROM customers WHERE customer_id = :customer_id LIMIT 1");
    $stmt->execute([':customer_id' => $customer_id]);
    $user_id = $stmt->fetchColumn();
    return $user_id ? (int)$user_id : null;
}



    // 5️⃣ Filter complaints
    public function filter(string $search = '', string $status = '', string $priority = ''): array {
    $query = "
        SELECT comp.*,
               CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
               CONCAT(v.make, ' ', v.model) AS vehicle,
               v.license_plate
        FROM complaints comp
        JOIN customers c ON comp.customer_id = c.customer_id
        JOIN users u ON c.user_id = u.user_id
        LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
        WHERE 1=1
    ";
    $params = [];

    if ($search !== '') {
        $query .= " AND comp.description LIKE :search";
        $params[':search'] = '%' . $search . '%';
    }
    if ($status !== '') {
        $query .= " AND comp.status = :status";
        $params[':status'] = $status;
    }
    if ($priority !== '') {
        $query .= " AND comp.priority = :priority";
        $params[':priority'] = $priority;
    }

    $query .= " ORDER BY comp.created_at DESC";

    $stmt = $this->pdo->prepare($query);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get all supervisors
public function getSupervisors(): array {
    $stmt = $this->pdo->query("
        SELECT user_id, first_name, last_name
        FROM users
        WHERE role = 'supervisor'
        ORDER BY first_name
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // 6️⃣ Update complaint
public function update(int $id, array $data): bool {

    $stmt = $this->pdo->prepare("
        UPDATE complaints SET
            customer_id = :customer_id,
            vehicle_id = :vehicle_id,
            subject = :subject,
            description = :description,
            priority = :priority,
            status = :status,
            assigned_to_user_id = :assigned_to
        WHERE complaint_id = :id
    ");

    $result = $stmt->execute([
        ':customer_id' => $data['customer_id'],
        ':vehicle_id'  => $data['vehicle_id'],
        ':subject'     => $data['subject'] ?? 'General Complaint',
        ':description' => $data['description'] ?? '',
        ':priority'    => $data['priority'] ?? 'Medium',
        ':status'      => $data['status'] ?? 'Open',
        ':assigned_to' => $data['assigned_to'] ?? null,
        ':id'          => $id
    ]);

    if (!$result) {
        $error = $stmt->errorInfo();
        throw new \Exception("SQL Update Error: " . $error[2]);
    }

    return $result;
}



 public function delete(int $id): bool {
    $stmt = $this->pdo->prepare("DELETE FROM complaints WHERE complaint_id = :id");
    return $stmt->execute([':id' => $id]);
}

// Get customer info by ID
public function getCustomerById(int $customerId): ?array {
    $stmt = $this->pdo->prepare("
        SELECT c.customer_id, u.first_name, u.last_name, u.phone, u.email
        FROM customers c
        JOIN users u ON c.user_id = u.user_id
        WHERE c.customer_id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $customerId]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

public function getCustomerAppointments(int $customerId): array
    {
        $stmt = $this->pdo->prepare("
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
            // Work orders
            $stmtWO = $this->pdo->prepare("
                SELECT wo.*, m.first_name AS mechanic_first, m.last_name AS mechanic_last,
                       sup.first_name AS supervisor_first, sup.last_name AS supervisor_last
                FROM work_orders wo
                LEFT JOIN users m ON wo.mechanic_id = m.user_id
                LEFT JOIN users sup ON wo.supervisor_id = sup.user_id
                WHERE wo.appointment_id = :appointment_id
            ");
            $stmtWO->execute(['appointment_id' => $appt['appointment_id']]);
            $appt['work_orders'] = $stmtWO->fetchAll(PDO::FETCH_ASSOC);

            // Complaints
            $stmtC = $this->pdo->prepare("
                SELECT c.*
                FROM complaints c
                WHERE c.appointment_id = :appointment_id
            ");
            $stmtC->execute(['appointment_id' => $appt['appointment_id']]);
            $appt['complaints'] = $stmtC->fetchAll(PDO::FETCH_ASSOC);
        }

        return $appointments;
    }

public function getCustomerComplaints(int $customerId): array {
    $stmt = $this->pdo->prepare("
        SELECT *
        FROM complaints
        WHERE customer_id = :customer_id
        ORDER BY created_at DESC
    ");
    $stmt->execute(['customer_id' => $customerId]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}