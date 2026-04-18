<?php
namespace app\model\Receptionist;

use app\core\Model;
use PDO;

class ComplaintModel extends Model {

    protected PDO $pdo;

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

// Add this new method to fetch recent appointments for a customer
// In ComplaintModel.php, update the getRecentAppointmentsByCustomer method:

// In ComplaintModel.php
public function getRecentAppointmentsByCustomer(int $customer_id, int $days = 30): array {

    $days = (int)$days; // safety

    $stmt = $this->pdo->prepare("
        SELECT 
            a.appointment_id,
            a.appointment_date,
            a.appointment_time,
            a.service_id,
            a.status,
            v.make,
            v.model,
            v.license_plate,
            CONCAT(
                a.appointment_date, ' ',
                TIME_FORMAT(a.appointment_time, '%H:%i'), ' - ',
                'Service ', a.service_id, ' (',
                v.make, ' ', v.model, ' - ',
                v.license_plate, ')'
            ) AS display_text
        FROM appointments a
        LEFT JOIN vehicles v ON a.vehicle_id = v.vehicle_id
        WHERE a.customer_id = :customer_id
        AND a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL $days DAY)
        AND a.appointment_date <= CURDATE()
        ORDER BY a.appointment_date DESC, a.appointment_time DESC
    ");

    $stmt->execute([
        ':customer_id' => $customer_id
    ]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// Update the create method to include appointment_id and branch_id
public function create(array $data): int {
    // Fetch user_id from customer_id
    $stmtUser = $this->pdo->prepare("SELECT user_id FROM customers WHERE customer_id = :customer_id");
    $stmtUser->execute([':customer_id' => $data['customer_id']]);
    $user_id = $stmtUser->fetchColumn();

    if (!$user_id) {
        throw new \Exception("Cannot find user linked to customer_id " . $data['customer_id']);
    }

    // Get branch_id from session
    $branch_id = $_SESSION['user']['branch_id'] ?? null;

    $stmt = $this->pdo->prepare("
        INSERT INTO complaints
        (customer_id, vehicle_id, appointment_id, branch_id, subject, description, priority, status, assigned_to_user_id)
        VALUES (:customer_id, :vehicle_id, :appointment_id, :branch_id, :subject, :description, :priority, :status, :assigned_to)
    ");

    $stmt->execute([
        ':customer_id'    => $data['customer_id'],
        ':vehicle_id'     => $data['vehicle_id'],
        ':appointment_id' => $data['appointment_id'] ?: null,  // Convert empty string to null
        ':branch_id'      => $branch_id,
        ':subject'        => $data['subject'] ?? 'General Complaint',
        ':description'    => $data['description'] ?? '',
        ':priority'       => $data['priority'] ?? 'Medium',
        ':status'         => $data['status'] ?? 'Open',
        ':assigned_to'    => $data['assigned_to'] ?? null
    ]);

    return (int)$this->pdo->lastInsertId();
}


    // 2️⃣ Fetch all complaints
public function all(): array {

    $branch_id = $_SESSION['user']['branch_id'] ?? null;

    $stmt = $this->pdo->prepare("
        SELECT comp.*,
               CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
               CONCAT(v.make, ' ', v.model) AS vehicle,
               v.license_plate
        FROM complaints comp
        JOIN customers c ON comp.customer_id = c.customer_id
        JOIN users u ON c.user_id = u.user_id
        LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
        WHERE comp.branch_id = :branch_id
        ORDER BY comp.created_at DESC
    ");

    $stmt->execute([':branch_id' => $branch_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    // 3️⃣ Find complaint by ID
  public function find(int $id): ?array {
    $stmt = $this->pdo->prepare("
        SELECT 
            comp.*,

            -- Customer
            CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
            u.phone,
            u.email,

            -- Vehicle
            CONCAT(v.make, ' ', v.model) AS vehicle,
            v.license_plate AS vehicle_number,

            -- Assigned user
            CONCAT(a.first_name, ' ', a.last_name) AS assigned_to,

            -- Appointment
            ap.appointment_date,
            ap.appointment_time,
            ap.status AS appointment_status,
            ap.notes,

            CONCAT(
    ap.appointment_date, ' ',
    TIME_FORMAT(ap.appointment_time, '%H:%i'), ' - ',
    'Service ', ap.service_id, ' (',
    v.make, ' ', v.model, ' - ',
    v.license_plate, ')'
) AS appointment_display,

            -- Work Order
            wo.work_order_id,
            wo.status AS work_order_status,
            wo.total_cost,
            wo.service_summary,
            wo.started_at,
            wo.completed_at

        FROM complaints comp

        LEFT JOIN customers c ON comp.customer_id = c.customer_id
        LEFT JOIN users u ON c.user_id = u.user_id
        LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
        LEFT JOIN users a ON comp.assigned_to_user_id = a.user_id

        -- Appointment join
        LEFT JOIN appointments ap ON comp.appointment_id = ap.appointment_id

        -- Work order join
        LEFT JOIN work_orders wo ON ap.appointment_id = wo.appointment_id

        WHERE comp.complaint_id = :id
        LIMIT 1
    ");

    $stmt->execute([':id' => $id]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}





    // 5️⃣ Filter complaints
    public function filter(string $search = '', string $status = '', string $priority = ''): array {

    $branch_id = $_SESSION['user']['branch_id'] ?? null;

    $query = "
        SELECT comp.*,
               CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
               CONCAT(v.make, ' ', v.model) AS vehicle,
               v.license_plate
        FROM complaints comp
        JOIN customers c ON comp.customer_id = c.customer_id
        JOIN users u ON c.user_id = u.user_id
        LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
        WHERE comp.branch_id = :branch_id
    ";

    $params = [
        ':branch_id' => $branch_id
    ];

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

// Update the update method
public function update(int $id, array $data): bool {
    // Get branch_id from session
    $branch_id = $_SESSION['user']['branch_id'] ?? null;

    $stmt = $this->pdo->prepare("
        UPDATE complaints SET
            customer_id = :customer_id,
            vehicle_id = :vehicle_id,
            appointment_id = :appointment_id,
            branch_id = COALESCE(:branch_id, branch_id),
            subject = :subject,
            description = :description,
            priority = :priority,
            status = :status,
            assigned_to_user_id = :assigned_to
        WHERE complaint_id = :id
    ");

    $result = $stmt->execute([
        ':customer_id'    => $data['customer_id'],
        ':vehicle_id'     => $data['vehicle_id'],
        ':appointment_id' => $data['appointment_id'] ?: null,
        ':branch_id'      => $branch_id,
        ':subject'        => $data['subject'] ?? 'General Complaint',
        ':description'    => $data['description'] ?? '',
        ':priority'       => $data['priority'] ?? 'Medium',
        ':status'         => $data['status'] ?? 'Open',
        ':assigned_to'    => $data['assigned_to'] ?? null,
        ':id'             => $id
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

// Get user_id from customer_id
public function getUserIdByCustomer(int $customer_id): ?int {
    $stmt = $this->pdo->prepare("SELECT user_id FROM customers WHERE customer_id = :customer_id LIMIT 1");
    $stmt->execute([':customer_id' => $customer_id]);
    $user_id = $stmt->fetchColumn();
    return $user_id ?: null;
}



}
