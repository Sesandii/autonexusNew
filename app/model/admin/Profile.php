<?php
namespace app\model\admin;

use PDO;

class Profile
{
    private PDO $pdo;
    private ?string $streetCol = null; // street OR street_address

    public function __construct()
    {
        $this->pdo = db();
        $this->streetCol = $this->detectStreetColumn();
    }

    private function detectStreetColumn(): ?string
    {
        // Check which column exists: street or street_address
        $stmt = $this->pdo->query("SHOW COLUMNS FROM users");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        if (in_array('street', $cols, true)) return 'street';
        if (in_array('street_address', $cols, true)) return 'street_address';
        return null;
    }

    public function getAdminById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE user_id = :id AND role = "admin" LIMIT 1');
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateAdmin(int $userId, array $data): void
    {
        // Discover available columns once
        $colsStmt = $this->pdo->query("SHOW COLUMNS FROM users");
        $cols = $colsStmt->fetchAll(PDO::FETCH_COLUMN, 0);

        $hasUpdatedAt = in_array('updated_at', $cols, true);

        // Base fields that should exist in your schema
        $fields = [
            'first_name' => ':first_name',
            'last_name'  => ':last_name',
            'email'      => ':email',
            'phone'      => ':phone',
            'alt_phone'  => ':alt_phone',
            'city'       => ':city',
            'state'      => ':state',
        ];

        // Street column may be either `street` or `street_address` (already detected in $this->streetCol)
        if ($this->streetCol !== null) {
            $fields[$this->streetCol] = ':street';
        }

        // Build SET parts dynamically
        $setParts = [];
        foreach ($fields as $col => $ph) {
            $setParts[] = "{$col}={$ph}";
        }
        if ($hasUpdatedAt) {
            $setParts[] = "updated_at=NOW()";
        }

        $sql = "UPDATE users SET " . implode(', ', $setParts) . " WHERE user_id=:id AND role='admin'";

        $stmt = $this->pdo->prepare($sql);
        $params = [
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'alt_phone'  => $data['alt_phone'],
            'city'       => $data['city'],
            'state'      => $data['state'],
            'id'         => $userId,
        ];
        if ($this->streetCol !== null) {
            $params['street'] = $data['street']; // maps to whichever column exists
        }

        $stmt->execute($params);
    }

}
