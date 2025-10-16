<?php
namespace app\model;

use PDO;

class Branch
{
    private PDO $pdo;

    public function __construct()
    {
        // ✅ use the shared helper from app/core/Database.php
        $this->pdo = db();
    }

    /** Return all branches */
    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM branches ORDER BY branch_code");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Create new branch */
    public function create(array $data): void
    {
        $cols = array_keys($data);
        $sql = "INSERT INTO branches (" . implode(',', $cols) . ")
                VALUES (:" . implode(',:', $cols) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    /** Find branch by code */
    public function findByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM branches WHERE branch_code = :c");
        $stmt->execute(['c' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /** Update branch by code */
    public function updateByCode(string $code, array $data): void
{
    // Do not allow changing branch_code via SET
    if (array_key_exists('branch_code', $data)) {
        unset($data['branch_code']);
    }

    // Build "col = :col" for all keys remaining in $data
    $cols = array_keys($data);
    if (empty($cols)) {
        // nothing to update; exit gracefully
        return;
    }

    $set = implode(',', array_map(fn($k) => "$k = :$k", $cols));

    // Use a distinct placeholder name for WHERE to avoid conflicts
    $sql = "UPDATE branches SET $set WHERE branch_code = :where_code";

    // Bind values for SET + the WHERE code
    $params = $data;
    $params['where_code'] = $code;

    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
}


    /** Delete branch by code */
    public function deleteByCode(string $code): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM branches WHERE branch_code = :c");
        $stmt->execute(['c' => $code]);
    }
}
