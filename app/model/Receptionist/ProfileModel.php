<?php
namespace app\model\Receptionist;

use app\core\Model;
use PDO;

class ProfileModel

{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // GET user profile
    public function getById(int $userId): ?array
{
    $stmt = $this->db->prepare("
        SELECT 
            u.*,
            b.branch_id,
            b.branch_code,
            b.name AS branch_name,
            b.city AS branch_city,
            b.address_line AS branch_address,
            b.phone AS branch_phone,
            b.email AS branch_email
        FROM users u
        LEFT JOIN receptionists r ON r.user_id = u.user_id
        LEFT JOIN branches b ON b.branch_id = r.branch_id
        WHERE u.user_id = ?
        LIMIT 1
    ");

    $stmt->execute([$userId]);
    return $stmt->fetch() ?: null;
}
    // UPDATE user profile
    public function update(int $userId, array $data): bool
    {
        $sql = "
            UPDATE users
            SET 
                first_name = :first_name,
                last_name = :last_name,
                phone = :phone,
                alt_phone = :alt_phone,
                street_address = :street_address,
                city = :city,
                state = :state
            WHERE user_id = :user_id
        ";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            ':first_name' => $data['first_name'],
            ':last_name' => $data['last_name'],
            ':phone' => $data['phone'],
            ':alt_phone' => $data['alt_phone'],
            ':street_address' => $data['street_address'],
            ':city' => $data['city'],
            ':state' => $data['state'],
            ':user_id' => $userId
        ]);
    }
}