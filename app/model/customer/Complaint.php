<?php
namespace app\model\customer;

use app\core\Model;
use PDO;

class Complaint extends Model {

    protected PDO $pdo;

    public function __construct() {
        $this->pdo = db();
    }

    /**
     * Map user_id to customer_id
     */
    private function customerIdByUserId(int $userId): ?int {
        $sql = "SELECT customer_id FROM customers WHERE user_id = :uid LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['uid' => $userId]);
        $cid = $st->fetchColumn();
        return $cid !== false ? (int)$cid : null;
    }

    /**
     * Create a new complaint
     */
    public function create(array $data): int {
        // Get customer_id from user_id if not provided
        $customer_id = $data['customer_id'] ?? $this->customerIdByUserId($data['user_id']);
        
        if (!$customer_id) {
            throw new \Exception('Customer not found for user');
        }

        // Insert with only the columns that exist in the table
        // The table likely has: customer_id, vehicle_id, description, priority, status, created_at (auto)
        $stmt = $this->pdo->prepare("
            INSERT INTO complaints
            (customer_id, vehicle_id, description, priority, status)
            VALUES (:customer_id, :vehicle_id, :description, :priority, :status)
        ");

        $result = $stmt->execute([
            ':customer_id'    => $customer_id,
            ':vehicle_id'     => $data['vehicle_id'] ?? null,
            ':description'    => $data['description'] ?? '',
            ':priority'       => $data['priority'] ?? 'Medium',
            ':status'         => $data['status'] ?? 'Open'
        ]);

        if (!$result) {
            $error = $stmt->errorInfo();
            throw new \Exception("Failed to create complaint: " . ($error[2] ?? 'Unknown error'));
        }

        return (int)$this->pdo->lastInsertId();
    }

    /**
     * Get all complaints for a specific user
     */
    public function getByUserId(int $user_id): array {
        $customer_id = $this->customerIdByUserId($user_id);
        if (!$customer_id) return [];

        $stmt = $this->pdo->prepare("
            SELECT comp.*,
                   CONCAT(v.make, ' ', v.model) AS vehicle,
                   v.license_plate
            FROM complaints comp
            LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
            WHERE comp.customer_id = :customer_id
            ORDER BY comp.created_at DESC
        ");
        $stmt->execute([':customer_id' => $customer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single complaint by ID
     */
    public function getById(int $complaint_id): ?array {
        $stmt = $this->pdo->prepare("
            SELECT comp.*,
                   CONCAT(u.first_name, ' ', u.last_name) AS customer_name,
                   CONCAT(v.make, ' ', v.model) AS vehicle,
                   v.license_plate
            FROM complaints comp
            JOIN customers c ON comp.customer_id = c.customer_id
            JOIN users u ON c.user_id = u.user_id
            LEFT JOIN vehicles v ON comp.vehicle_id = v.vehicle_id
            WHERE comp.complaint_id = :complaint_id
        ");
        $stmt->execute([':complaint_id' => $complaint_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    /**
     * Update complaint status
     */
    public function updateStatus(int $complaint_id, string $status): bool {
        $stmt = $this->pdo->prepare("
            UPDATE complaints 
            SET status = :status 
            WHERE complaint_id = :complaint_id
        ");
        return $stmt->execute([
            ':complaint_id' => $complaint_id,
            ':status' => $status
        ]);
    }
}
