<?php
namespace app\model\Receptionist;

use PDO;

class ProfileModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function getUserProfile(int $userId): ?array
    {
        $stmt = $this->db->prepare("
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

                b.name         AS branch_name,
                b.branch_code,
                b.city         AS branch_city,
                b.address_line AS branch_address,
                b.phone        AS branch_phone,
                b.email        AS branch_email

            FROM users u
            LEFT JOIN receptionists r ON u.user_id = r.user_id
            LEFT JOIN branches b      ON r.branch_id = b.branch_id

            WHERE u.user_id = :user_id
            LIMIT 1
        ");

        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function updateProfile(int $userId, array $data): bool
    {
        $allowedFields = ['first_name', 'last_name', 'phone', 'alt_phone',
                          'street_address', 'city', 'state'];

        $updates = [];
        $params  = [':user_id' => $userId];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updates[]        = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($updates)) return false;

        $sql  = "UPDATE users SET " . implode(', ', $updates) . " WHERE user_id = :user_id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
}