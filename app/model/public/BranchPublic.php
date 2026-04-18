<?php
declare(strict_types=1);

namespace app\model\public;

use PDO;

class BranchPublic
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /** Public/active branches for the Home page picker. */
    public function allActive(): array
    {
        // Uses alias so views can read $b['branch_name']
        $sql = "SELECT branch_code, name AS branch_name
                FROM branches
                WHERE status = 'active'
                ORDER BY name";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    public function findNameByCode(string $code): ?string
{
    $sql = "SELECT name FROM branches WHERE branch_code = :c AND COALESCE(status,'active')='active' LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['c' => $code]);
    $name = $stmt->fetchColumn();
    return $name !== false ? (string)$name : null;
}


}
