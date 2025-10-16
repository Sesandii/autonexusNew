<?php
namespace app\model;

use PDO;
use Exception;

class Manager
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function all(): array
    {
        $sql = "SELECT m.manager_id, m.manager_code, m.user_id,
                       u.first_name, u.last_name, u.email, u.phone, u.username, u.status, u.created_at
                FROM managers m
                JOIN users u ON u.user_id = m.user_id
                ORDER BY m.manager_id DESC";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $st = $this->db->prepare(
            "SELECT m.*, u.first_name, u.last_name, u.email, u.phone, u.username, u.status
             FROM managers m
             JOIN users u ON u.user_id = m.user_id
             WHERE m.manager_id = ?"
        );
        $st->execute([$id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(int $userId, string $managerCode): int
    {
        $st = $this->db->prepare("INSERT INTO managers (manager_code, user_id, created_at, updated_at)
                                  VALUES (?, ?, NOW(), NOW())");
        $ok = $st->execute([$managerCode, $userId]);
        if (!$ok) throw new Exception('Failed to create manager');
        return (int)$this->db->lastInsertId();
    }

    public function update(int $managerId, array $data): void
    {
        $parts = [];
        $params = [':id' => $managerId];
        if (isset($data['manager_code'])) {
            $parts[] = "manager_code = :manager_code";
            $params[':manager_code'] = $data['manager_code'];
        }
        if (!$parts) return;
        $sql = "UPDATE managers SET " . implode(',', $parts) . ", updated_at = NOW() WHERE manager_id = :id";
        $st = $this->db->prepare($sql);
        $st->execute($params);
    }

    public function delete(int $managerId): void
    {
        $st = $this->db->prepare("DELETE FROM managers WHERE manager_id = ?");
        $st->execute([$managerId]);
    }

    public function createUserAndManager(array $userData, string $managerCode): array
    {
        $this->db->beginTransaction();
        try {
            $user = new User(); // in app\model
            $userId = $user->create($userData);
            $managerId = $this->create($userId, $managerCode);
            $this->db->commit();
            return ['user_id' => $userId, 'manager_id' => $managerId];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

   public function findWithUser(int $managerId): ?array
{
    $pdo = db();
    $sql = "
        SELECT 
            m.manager_id,
            m.manager_code,
            m.created_at AS manager_created_at,
            u.user_id,
            u.first_name,
            u.last_name,
            u.username,
            u.email,
            u.phone,
            u.status,
            u.role,
            u.created_at AS user_created_at
        FROM managers m
        INNER JOIN users u ON u.user_id = m.user_id
        WHERE m.manager_id = :id
        LIMIT 1
    ";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $managerId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

    public function search(string $q = '', string $status = 'all'): array
{
    $where = [];
    $bind  = [];

    // Status filter
    if ($status === 'active' || $status === 'inactive') {
        $where[] = 'u.status = ?';
        $bind[]  = $status;
    }

    // Keyword filter (id / name / username / email / code)
    if ($q !== '') {
        // If it's a number, allow direct manager_id match
        $maybeId = ctype_digit($q) ? (int)$q : null;

        $where[] =
            '('
            . 'u.first_name LIKE ? OR u.last_name LIKE ? OR u.username LIKE ? OR '
            . 'u.email LIKE ? OR m.manager_code LIKE ?'
            . ($maybeId !== null ? ' OR m.manager_id = ?' : '')
            . ')';

        $kw = '%' . $q . '%';
        array_push($bind, $kw, $kw, $kw, $kw, '%' . $q . '%');
        if ($maybeId !== null) $bind[] = $maybeId;
    }

    $sql =
        "SELECT m.manager_id, m.manager_code, m.user_id,
                u.first_name, u.last_name, u.email, u.phone, u.username, u.status, u.created_at
         FROM managers m
         JOIN users u ON u.user_id = m.user_id"
        . (count($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        . " ORDER BY m.manager_id DESC";

    $st = $this->db->prepare($sql);
    $st->execute($bind);
    return $st->fetchAll(PDO::FETCH_ASSOC);
}

}
