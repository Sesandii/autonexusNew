<?php
namespace app\model\admin;

use PDO;
use Exception;
use app\model\admin\User; // <-- needed for createUserAndManager()

class Manager
{
    private PDO $db;
    private array $managerCols;

    public function __construct()
    {
        $this->db = db();
        $this->managerCols = $this->db->query("SHOW COLUMNS FROM managers")->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    /* ---------- Utilities ---------- */

    private function hasCol(string $col): bool
    {
        return in_array($col, $this->managerCols, true);
    }

    /**
     * Generate next code like MGR-000001, MGR-000002 ...
     * (scans highest numeric suffix; safe even if rows were deleted)
     */
    /**
 * Generate next code like MAN001, MAN002, ...
 * Reads the highest numeric suffix and increments it.
 */
public function nextCode(): string
{
    if (!$this->hasCol('manager_code')) {
        return 'MAN001';
    }

    // Get the largest numeric part after the 'MAN' prefix (3 chars)
    $sql = "SELECT MAX(CAST(SUBSTRING(manager_code, 4) AS UNSIGNED)) AS max_n
              FROM managers";
    $max = $this->db->query($sql)->fetchColumn();

    $next = ((int)$max) + 1;              // start from 1 if table empty/null
    return 'MAN' . str_pad((string)$next, 3, '0', STR_PAD_LEFT);
}

/**
 * Alternative generator with zero race conditions:
 * Use after insert, based on the auto-increment manager_id.
 * This will produce MAN001, MAN002 ... in lockstep with manager_id.
 */
public function codeFromId(int $managerId): string
{
    return 'MAN' . str_pad((string)$managerId, 3, '0', STR_PAD_LEFT);
}


    /* ---------- Queries ---------- */

    public function all(): array
    {
        $sql = "SELECT m.manager_id, m.manager_code, m.user_id,
                       u.first_name, u.last_name, u.email, u.phone, u.username, u.status, u.created_at
                  FROM managers m
                  JOIN users u ON u.user_id = m.user_id
              ORDER BY m.manager_id ASC"; // oldest -> newest
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

    /**
     * Create a managers row.
     * - Schema-aware for created_at/updated_at.
     */
    public function create(int $userId, string $managerCode): int
    {
        $cols = ['manager_code', 'user_id'];
        $vals = [':code', ':uid'];

        if ($this->hasCol('created_at')) {
            $cols[] = 'created_at';
            $vals[] = 'NOW()';
        }
        if ($this->hasCol('updated_at')) {
            $cols[] = 'updated_at';
            $vals[] = 'NOW()';
        }

        $sql = "INSERT INTO managers (" . implode(',', $cols) . ") VALUES (" . implode(',', $vals) . ")";
        $st  = $this->db->prepare($sql);
        $ok  = $st->execute([':code' => $managerCode, ':uid' => $userId]);
        if (!$ok) throw new Exception('Failed to create manager');

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update managers row (only fields you allow).
     * - Schema-aware for updated_at.
     */
    public function update(int $managerId, array $data): void
    {
        $parts  = [];
        $params = [':id' => $managerId];

        if (isset($data['manager_code'])) {
            $parts[] = "manager_code = :manager_code";
            $params[':manager_code'] = $data['manager_code'];
        }

        if (!$parts) return;

        if ($this->hasCol('updated_at')) {
            $parts[] = "updated_at = NOW()";
        }

        $sql = "UPDATE managers SET " . implode(', ', $parts) . " WHERE manager_id = :id";
        $st  = $this->db->prepare($sql);
        $st->execute($params);
    }

    public function delete(int $managerId): void
    {
        $st = $this->db->prepare("DELETE FROM managers WHERE manager_id = ?");
        $st->execute([$managerId]);
    }

    /**
     * Create user + manager in one transaction.
     * - Uses server-side hashing for password (User::create must do it).
     * - Generates code server-side to prevent tampering.
     *
     * You can switch between `nextCode()` (simple) or `codeFromId()` (race-proof).
     */
    public function createUserAndManager(array $userData, ?string $ignoredClientCode = null): array
    {
        $this->db->beginTransaction();
        try {
            // 1) Create user
            $user = new User();
            $userId = $user->create($userData);

            // 2) Insert manager with a temporary code to get the ID
            $tempCode = 'TMP';
            $managerId = $this->create($userId, $tempCode);

            // 3) Now set final code based on the ID (MAN###)
            $managerCode = $this->codeFromId($managerId);
            $this->update($managerId, ['manager_code' => $managerCode]);

            $this->db->commit();
            return ['user_id' => $userId, 'manager_id' => $managerId, 'manager_code' => $managerCode];
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) $this->db->rollBack();
            throw $e;
        }
    }


    public function findWithUser(int $managerId): ?array
    {
        $sql = "SELECT m.manager_id, m.manager_code, " .
               ($this->hasCol('created_at') ? "m.created_at AS manager_created_at," : "NULL AS manager_created_at,") .
               " u.user_id, u.first_name, u.last_name, u.username, u.email, u.phone, 
                 u.status, u.role, u.created_at AS user_created_at
            FROM managers m
            JOIN users u ON u.user_id = m.user_id
           WHERE m.manager_id = :id
           LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $managerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
