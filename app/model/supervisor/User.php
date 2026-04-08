<?php

namespace app\model\supervisor;

use PDO;

class User
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    public function findById(int $id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function updateProfile(int $userId, array $data): bool
{
    $fields = [];
    foreach ($data as $key => $value) {
        $fields[] = "$key = :$key";
    }

    $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE user_id = :user_id";
    $data['user_id'] = $userId;

    $stmt = $this->pdo->prepare($sql);
    return $stmt->execute($data);
}


/**
 * Get detailed profile data including branch information for a mechanic
 */
public function findMechanicProfile(int $userId)
{
    $sql = "
        SELECT 
            u.*, 
            m.mechanic_code, 
            b.name AS branch_name, 
            b.branch_code
        FROM users u
        JOIN mechanics m ON u.user_id = m.user_id
        JOIN branches b ON m.branch_id = b.branch_id
        WHERE u.user_id = ?
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}


public function findSupervisorProfile(int $userId)
{
    $sql = "
        SELECT 
            u.*, 
            b.name AS branch_name, 
            b.branch_code
        FROM users u
        JOIN supervisors s ON u.user_id = s.user_id
        JOIN branches b ON s.branch_id = b.branch_id
        WHERE u.user_id = ?
    ";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
