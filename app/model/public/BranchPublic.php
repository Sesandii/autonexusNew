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
}
