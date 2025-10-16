<?php
namespace app\model;

use PDO;
use Exception;

class User
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO users
            (first_name, last_name, username, email, password_hash, phone, role, status, created_at)
            VALUES (:first_name, :last_name, :username, :email, :password_hash, :phone, :role, :status, NOW())";

        $st = $this->db->prepare($sql);
        $ok = $st->execute([
            ':first_name'    => $data['first_name'] ?? '',
            ':last_name'     => $data['last_name'] ?? '',
            ':username'      => $data['username'],
            ':email'         => $data['email'],
            ':password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            ':phone'         => $data['phone'] ?? null,
            ':role'          => 'manager',
            ':status'        => 'active'
        ]);

        if (!$ok) throw new Exception('Failed to create user');
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $parts = [];
        $params = [':id' => $id];

        foreach (['first_name','last_name','username','email','phone','status'] as $f) {
            if (array_key_exists($f, $data)) {
                $parts[] = "$f = :$f";
                $params[":$f"] = $data[$f];
            }
        }
        if (!empty($data['password'])) {
            $parts[] = "password_hash = :password_hash";
            $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (!$parts) return;

        $sql = "UPDATE users SET " . implode(',', $parts) . " WHERE user_id = :id";
        $st = $this->db->prepare($sql);
        $st->execute($params);
    }

    public function delete(int $id): void
    {
        $st = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
        $st->execute([$id]);
    }

    public function findByEmailOrUsername(string $email, string $username): ?array
    {
        $st = $this->db->prepare("SELECT * FROM users WHERE email = ? OR username = ? LIMIT 1");
        $st->execute([$email, $username]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
