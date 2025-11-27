<?php
namespace app\model\admin;

use PDO;

class Service
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /** Return next code like SER001, SER002 ... */
    public function nextCode(): string
    {
        // Get the highest numeric part after 'SER'
        $sql = "SELECT MAX(CAST(SUBSTRING(service_code, 4) AS UNSIGNED)) AS max_num
                FROM services
                WHERE service_code REGEXP '^SER[0-9]+$'";
        $row = $this->pdo->query($sql)->fetch(PDO::FETCH_ASSOC);
        $next = (int)($row['max_num'] ?? 0) + 1;

        // pad to 3 digits → SER001; if it grows, becomes SER1000 etc.
        return 'SER' . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
    }

    public function create(array $data): int
    {
        $cols = array_keys($data);
        $sql  = "INSERT INTO services (" . implode(',', $cols) . ")
                 VALUES (:" . implode(',:', $cols) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($data);
        return (int)$this->pdo->lastInsertId();
    }

    public function codeExists(string $service_code): bool
    {
        $stmt = $this->pdo->prepare("SELECT 1 FROM services WHERE service_code = :c LIMIT 1");
        $stmt->execute(['c' => $service_code]);
        return (bool)$stmt->fetchColumn();
    }

    public function allWithTypeAndBranches(): array
{
    $sql = "
        SELECT
            s.service_id,
            s.service_code,
            s.name,
            s.description,
            s.default_price,              -- ✅ add this line
            s.status,
            s.created_at,
            st.type_id,
            COALESCE(st.type_name,'Uncategorized') AS type_name,
            COUNT(bs.branch_id) AS branch_count,
            GROUP_CONCAT(
                DISTINCT CONCAT(b.name,' (',b.branch_code,')')
                ORDER BY b.name SEPARATOR ', '
            ) AS branches
        FROM services s
        LEFT JOIN service_types st ON st.type_id = s.type_id
        LEFT JOIN branch_services bs ON bs.service_id = s.service_id
        LEFT JOIN branches b        ON b.branch_id = bs.branch_id
        GROUP BY s.service_id
        ORDER BY s.created_at DESC, s.name ASC
    ";
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}


public function distinctTypesForTabs(): array
{
    $sql = "
        SELECT st.type_id, st.type_name
        FROM service_types st
        JOIN services s ON s.type_id = st.type_id
        GROUP BY st.type_id, st.type_name
        ORDER BY st.type_name
    ";
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

public function findById(int $id): ?array
{
    $sql = "SELECT s.*, COALESCE(st.type_name,'Uncategorized') AS type_name
            FROM services s
            LEFT JOIN service_types st ON st.type_id = s.type_id
            WHERE s.service_id = :id";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

public function updateById(int $id, array $data): void
{
    if (isset($data['service_code'])) unset($data['service_code']); // code immutable
    if (empty($data)) return;

    $set = implode(',', array_map(fn($k) => "$k = :$k", array_keys($data)));
    $sql = "UPDATE services SET $set WHERE service_id = :id";
    $params = $data; $params['id'] = $id;
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute($params);
}

public function deleteById(int $id): void
{
    // branch_service rows will be removed by FK ON DELETE CASCADE
    $stmt = $this->pdo->prepare("DELETE FROM services WHERE service_id = :id");
    $stmt->execute(['id' => $id]);
}

public function allAtomicServices(): array
{
    // return active services that can be included in a package.
    // By default, exclude services whose type name is "Package".
    $sql = "
      SELECT s.service_id, s.service_code, s.name
      FROM services s
      LEFT JOIN service_types st ON st.type_id = s.type_id
      WHERE s.status = 'active'
        AND (st.type_name IS NULL OR LOWER(st.type_name) <> 'package')
      ORDER BY s.name
    ";
    return $this->pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}



}
