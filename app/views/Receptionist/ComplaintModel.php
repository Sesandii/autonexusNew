<?php
namespace app\model\Receptionist;

use app\core\Model;
use PDO;

class ComplaintModel extends Model {

    protected PDO $pdo;

    public function __construct() {
        $this->pdo = db();
    }

    // 1️⃣ Create new complaint
    public function create(array $data): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO complaints
            (customer_name, phone, email, vehicle, vehicle_number, complaint_date, complaint_time, description, priority, status, assigned_to)
            VALUES (:customer_name, :phone, :email, :vehicle, :vehicle_number, :complaint_date, :complaint_time, :description, :priority, :status, :assigned_to)
        ");
        $stmt->execute([
            ':customer_name'   => $data['customer_name'] ?? '',
            ':phone'           => $data['phone'] ?? '',
            ':email'           => $data['email'] ?? '',
            ':vehicle'         => $data['vehicle'] ?? '',
            ':vehicle_number'  => $data['vehicle_number'] ?? '',
            ':complaint_date'  => $data['complaint_date'] ?? null,
            ':complaint_time'  => $data['complaint_time'] ?? null,
            ':description'     => $data['description'] ?? '',
            ':priority'        => $data['priority'] ?? 'Medium',
            ':status'          => $data['status'] ?? 'Open',
            ':assigned_to'     => $data['assigned_to'] ?? null
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    // 2️⃣ Fetch all complaints
    public function all(): array {
        $stmt = $this->pdo->query("SELECT * FROM complaints ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 3️⃣ Find complaint by ID
    public function find(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM complaints WHERE complaint_id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    // 4️⃣ Get complaints by customer
    public function getByCustomer(string $customer_name): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM complaints
            WHERE customer_name = :customer_name
            ORDER BY complaint_date DESC
        ");
        $stmt->execute([':customer_name' => $customer_name]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 5️⃣ Filter complaints
    public function filter(string $search = '', string $status = '', string $priority = ''): array {
        $query = "SELECT * FROM complaints WHERE 1=1";
        $params = [];

        if ($search !== '') {
            $query .= " AND description LIKE :search";
            $params[':search'] = '%' . $search . '%';
        }
        if ($status !== '') {
            $query .= " AND status = :status";
            $params[':status'] = $status;
        }
        if ($priority !== '') {
            $query .= " AND priority = :priority";
            $params[':priority'] = $priority;
        }

        $query .= " ORDER BY created_at DESC";

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // 6️⃣ Update complaint
    public function update(int $id, array $data): bool {
        $stmt = $this->pdo->prepare("
            UPDATE complaints SET
                customer_name  = :customer_name,
                phone          = :phone,
                email          = :email,
                vehicle        = :vehicle,
                vehicle_number = :vehicle_number,
                complaint_date = :complaint_date,
                complaint_time = :complaint_time,
                description    = :description,
                priority       = :priority,
                status         = :status,
                assigned_to    = :assigned_to
            WHERE complaint_id = :id
        ");
        return $stmt->execute([
            ':customer_name'   => $data['customer_name'] ?? '',
            ':phone'           => $data['phone'] ?? '',
            ':email'           => $data['email'] ?? '',
            ':vehicle'         => $data['vehicle'] ?? '',
            ':vehicle_number'  => $data['vehicle_number'] ?? '',
            ':complaint_date'  => $data['complaint_date'] ?? null,
            ':complaint_time'  => $data['complaint_time'] ?? null,
            ':description'     => $data['description'] ?? '',
            ':priority'        => $data['priority'] ?? 'Medium',
            ':status'          => $data['status'] ?? 'Open',
            ':assigned_to'     => $data['assigned_to'] ?? null,
            ':id'              => $id
        ]);
    }

 public function delete(int $id): bool {
    $stmt = $this->pdo->prepare("DELETE FROM complaints WHERE complaint_id = :id");
    return $stmt->execute([':id' => $id]);
}



}
