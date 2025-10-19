<?php
namespace app\model\admin;

use PDO;

class Branch
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db(); // your global db() function
    }

    public function all(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM branches ORDER BY branch_code");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): void
    {
        $cols = array_keys($data);
        $sql = "INSERT INTO branches (" . implode(',', $cols) . ")
                VALUES (:" . implode(',:', $cols) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM branches WHERE branch_code = :c");
        $stmt->execute(['c' => $code]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function updateByCode(string $code, array $data): void
    {
        if (array_key_exists('branch_code', $data)) unset($data['branch_code']);
        $cols = array_keys($data);
        if (empty($cols)) return;

        $set = implode(',', array_map(fn($k) => "$k = :$k", $cols));
        $sql = "UPDATE branches SET $set WHERE branch_code = :where_code";

        $params = $data;
        $params['where_code'] = $code;

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }

    public function deleteByCode(string $code): void
    {
        $stmt = $this->pdo->prepare("DELETE FROM branches WHERE branch_code = :c");
        $stmt->execute(['c' => $code]);
    }

    /** ✅ Return branches + manager info */
    public function allWithManager(): array
    {
        $sql = "SELECT 
                    b.branch_id, b.branch_code, b.name, b.city,
                    b.manager_id,
                    u.first_name AS m_first, u.last_name AS m_last
                FROM branches b
                LEFT JOIN managers m ON m.manager_id = b.manager_id
                LEFT JOIN users u     ON u.user_id     = m.user_id
                ORDER BY b.name ASC";
        return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}
