<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;
use Exception;

class Receptionist
{
    private PDO $db;

    public function __construct()
    {
        // uses your global db() helper from core/Database.php
        $this->db = db();
    }

    /**
     * Get all receptionists with related user + branch info
     */
    public function all(): array
    {
        $sql = "
            SELECT
                r.*,
                u.first_name,
                u.last_name,
                u.email,
                u.phone,
                u.status AS user_status,
                b.name AS branch_name,
                b.branch_code
            FROM receptionists r
            JOIN users u ON u.user_id = r.user_id
            LEFT JOIN branches b ON b.branch_id = r.branch_id
            ORDER BY r.receptionist_id DESC
        ";

        $st = $this->db->query($sql);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find a single receptionist by ID (with user + branch info).
     */
    public function find(int $id): ?array
    {
        $sql = "
            SELECT
                r.*,
                u.user_id,
                u.first_name,
                u.last_name,
                u.username,
                u.email,
                u.phone,
                u.alt_phone,
                u.status AS user_status,
                u.created_at AS user_created_at,
                b.name AS branch_name,
                b.branch_code
            FROM receptionists r
            JOIN users u ON u.user_id = r.user_id
            LEFT JOIN branches b ON b.branch_id = r.branch_id
            WHERE r.receptionist_id = :id
            LIMIT 1
        ";

        $st = $this->db->prepare($sql);
        $st->execute([':id' => $id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Create receptionist AFTER the user record is created.
     * Expects at least: user_id.
     */
    public function create(array $data): int
    {
        if (empty($data['user_id'])) {
            throw new Exception('user_id is required to create receptionist');
        }

        $sql = "
            INSERT INTO receptionists
                (receptionist_code, user_id, branch_id, status, created_at)
            VALUES
                (:receptionist_code, :user_id, :branch_id, :status, NOW())
        ";

        $st = $this->db->prepare($sql);
        $ok = $st->execute([
            ':receptionist_code' => $data['receptionist_code'] ?? null,
            ':user_id'           => (int)$data['user_id'],
            ':branch_id'         => $data['branch_id'] ?: null,
            ':status'            => $data['status'] ?? 'active',
        ]);

        if (!$ok) {
            throw new Exception('Failed to create receptionist');
        }

        return (int)$this->db->lastInsertId();
    }

    /**
     * Update receptionist record.
     */
    public function update(int $id, array $data): void
    {
        $parts  = [];
        $params = [':id' => $id];

        foreach (['receptionist_code', 'branch_id', 'status'] as $f) {
            if (array_key_exists($f, $data)) {
                $parts[]       = "$f = :$f";
                $params[":$f"] = $data[$f] ?: null;
            }
        }

        if (!$parts) {
            return; // nothing to update
        }

        $sql = "UPDATE receptionists SET " . implode(', ', $parts) . " WHERE receptionist_id = :id";
        $st  = $this->db->prepare($sql);
        $st->execute($params);
    }

    /**
     * Delete receptionist row (NOT the user).
     */
    public function delete(int $id): void
    {
        $st = $this->db->prepare("DELETE FROM receptionists WHERE receptionist_id = ?");
        $st->execute([$id]);
    }
}
