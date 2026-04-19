<?php
namespace app\model\Manager;

use PDO;

class ProfileModel
{
    private \PDO $db;
    
    public function __construct()
    {
        $this->db = db();
    }
    
    public function getUserProfile(int $userId): ?array
    {
        $sql = "
            SELECT 
                u.user_id,
                u.first_name,
                u.last_name,
                u.username,
                u.email,
                u.phone,
                u.alt_phone,
                u.profile_picture,
                u.street_address,
                u.city,
                u.state,
                u.role,
                u.status,
                u.created_at,
                b.name AS branch_name,
                b.branch_code,
                b.city AS branch_city,
                b.address_line AS branch_address,
                b.phone AS branch_phone,
                b.email AS branch_email
            FROM users u
            LEFT JOIN managers m ON u.user_id = m.user_id
            LEFT JOIN branches b ON m.branch_id = b.branch_id
            WHERE u.user_id = :user_id
            LIMIT 1
        ";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        
        return $stmt->fetch() ?: null;
    }
    
    public function updateProfile(int $userId, array $data): bool
    {
        $allowedFields = ['first_name', 'last_name', 'phone', 'alt_phone', 
                         'street_address', 'city', 'state'];
        
        $updates = [];
        $params = [':user_id' => $userId];
        
        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updates[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = :user_id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}