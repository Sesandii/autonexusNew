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

}
