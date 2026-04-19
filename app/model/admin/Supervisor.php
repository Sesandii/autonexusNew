<?php
namespace app\model\admin;

use PDO;

class Supervisor
{
    // Initialize model dependencies and database access.
    public function __construct(private PDO $db)
    {
    }

    // Handle all operation.
    public function all(string $q = '', string $status = 'all'): array
    {
        $sql = "SELECT s.supervisor_id, s.supervisor_code, s.user_id, s.branch_id, s.manager_id,
                       s.status, s.created_at,
                       u.first_name, u.last_name, u.email, u.phone,
                       b.branch_code, b.name AS branch_name,
                       m.manager_code,
                       mu.first_name AS manager_first_name,
                       mu.last_name  AS manager_last_name
                  FROM supervisors s
                  JOIN users u ON u.user_id = s.user_id
             LEFT JOIN branches b ON b.branch_id = s.branch_id
             LEFT JOIN managers m ON m.manager_id = s.manager_id
             LEFT JOIN users mu ON mu.user_id = m.user_id
                 WHERE 1 = 1";

        $params = [];

        if ($q !== '') {
            $like = '%' . $q . '%';
            $sql .= " AND (
                        s.supervisor_code LIKE ?
                     OR u.first_name LIKE ?
                     OR u.last_name LIKE ?
                     OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?
                     OR u.email LIKE ?
                     OR u.phone LIKE ?
                     OR b.branch_code LIKE ?
                     OR b.name LIKE ?
                     OR m.manager_code LIKE ?
                    )";
            $params = array_fill(0, 9, $like);
        }

        if (in_array($status, ['active', 'inactive'], true)) {
            $sql .= " AND s.status = ?";
            $params[] = $status;
        }

        $sql .= " ORDER BY CAST(SUBSTRING(s.supervisor_code, 4) AS UNSIGNED) ASC";

        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    }

    // Handle findByCode operation.
    public function findByCode(string $code): ?array
    {
        $st = $this->db->prepare(
            "SELECT s.*, u.first_name, u.last_name, u.email, u.phone, u.status AS user_status,
                    b.branch_code, b.name AS branch_name,
                    m.manager_code,
                    mu.first_name AS manager_first_name,
                    mu.last_name  AS manager_last_name
               FROM supervisors s
               JOIN users u ON u.user_id = s.user_id
          LEFT JOIN branches b ON b.branch_id = s.branch_id
          LEFT JOIN managers m ON m.manager_id = s.manager_id
          LEFT JOIN users mu ON mu.user_id = m.user_id
              WHERE s.supervisor_code = :c
              LIMIT 1"
        );
        $st->execute([':c' => $code]);
        $row = $st->fetch();
        return $row ?: null;
    }

    // Handle nextCode operation.
    private function nextCode(): string
    {
        $max = $this->db->query(
            "SELECT MAX(CAST(SUBSTRING(supervisor_code,4) AS UNSIGNED))
               FROM supervisors
              WHERE supervisor_code REGEXP '^SUP[0-9]+'"
        )->fetchColumn();
        $n = (int) $max + 1;
        return 'SUP' . str_pad((string) $n, 3, '0', STR_PAD_LEFT);
    }

    // Handle branchManagerId operation.
    private function branchManagerId(int $branchId): ?int
    {
        $st = $this->db->prepare("SELECT manager_id FROM branches WHERE branch_id=:b LIMIT 1");
        $st->execute([':b' => $branchId]);
        $val = $st->fetchColumn();
        return $val !== false ? (int) $val : null;
    }

    // Handle emailExists operation.
    public function emailExists(string $email, ?int $excludeUserId = null): bool
    {
        $email = trim($email);
        if ($email === '') {
            return false;
        }

        if ($excludeUserId !== null) {
            $st = $this->db->prepare("SELECT 1 FROM users WHERE email = :email AND user_id <> :uid LIMIT 1");
            $st->execute([':email' => $email, ':uid' => $excludeUserId]);
            return (bool) $st->fetchColumn();
        }

        $st = $this->db->prepare("SELECT 1 FROM users WHERE email = :email LIMIT 1");
        $st->execute([':email' => $email]);
        return (bool) $st->fetchColumn();
    }

    // Handle phoneExists operation.
    public function phoneExists(string $phone, ?int $excludeUserId = null): bool
    {
        $phone = trim($phone);
        if ($phone === '') {
            return false;
        }

        if ($excludeUserId !== null) {
            $st = $this->db->prepare("SELECT 1 FROM users WHERE phone = :phone AND user_id <> :uid LIMIT 1");
            $st->execute([':phone' => $phone, ':uid' => $excludeUserId]);
            return (bool) $st->fetchColumn();
        }

        $st = $this->db->prepare("SELECT 1 FROM users WHERE phone = :phone LIMIT 1");
        $st->execute([':phone' => $phone]);
        return (bool) $st->fetchColumn();
    }

    /** returns supervisor_code */
    public function create(array $d): string
    {
        $this->db->beginTransaction();

        try {
            $pwd = trim((string) ($d['password'] ?? ''));
            if ($pwd === '') {
                $pwd = 'Supervisor@123';
            }

            $stU = $this->db->prepare(
                "INSERT INTO users(first_name,last_name,email,password_hash,phone,role,status,created_at)
                 VALUES(:fn,:ln,:email,:phash,:phone,'supervisor',:ustatus,NOW())"
            );
            $stU->execute([
                ':fn' => $d['first_name'],
                ':ln' => $d['last_name'],
                ':email' => $d['email'] ?? null,
                ':phash' => password_hash($pwd, PASSWORD_DEFAULT),
                ':phone' => $d['phone'] ?? null,
                ':ustatus' => $d['status'] ?? 'active',
            ]);
            $uid = (int) $this->db->lastInsertId();

            $code = trim($d['supervisor_code'] ?? '') ?: $this->nextCode();
            $branchId = isset($d['branch_id']) && $d['branch_id'] !== '' ? (int) $d['branch_id'] : null;
            $managerId = isset($d['manager_id']) && $d['manager_id'] !== '' ? (int) $d['manager_id'] : null;

            if ($branchId !== null) {
                $bm = $this->branchManagerId($branchId);
                if ($bm === null) {
                    throw new \RuntimeException('Selected branch not found or has no manager.');
                }
                if ($managerId === null) {
                    $managerId = $bm;
                } elseif ($managerId !== $bm) {
                    throw new \RuntimeException('Manager does not belong to the selected branch.');
                }
            }

            $stS = $this->db->prepare(
                "INSERT INTO supervisors(supervisor_code,user_id,branch_id,manager_id,status,created_at)
                 VALUES(:code,:uid,:branch,:manager,:status,NOW())"
            );
            $stS->execute([
                ':code' => $code,
                ':uid' => $uid,
                ':branch' => $branchId,
                ':manager' => $managerId,
                ':status' => $d['status'] ?? 'active',
            ]);

            $this->db->commit();
            return $code;
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Handle updateByCode operation.
    public function updateByCode(string $code, array $d): void
    {
        $s = $this->findByCode($code);
        if (!$s)
            return;

        $this->db->beginTransaction();

        try {
            $branchId = isset($d['branch_id']) && $d['branch_id'] !== '' ? (int) $d['branch_id'] : null;
            $managerId = isset($d['manager_id']) && $d['manager_id'] !== '' ? (int) $d['manager_id'] : null;

            if ($branchId !== null) {
                $bm = $this->branchManagerId($branchId);
                if ($bm === null) {
                    throw new \RuntimeException('Selected branch not found or has no manager.');
                }
                if ($managerId === null) {
                    $managerId = $bm;
                } elseif ($managerId !== $bm) {
                    throw new \RuntimeException('Manager does not belong to the selected branch.');
                }
            }

            $sqlU = "UPDATE users
                        SET first_name=:fn,last_name=:ln,email=:email,phone=:phone"
                . (!empty($d['password']) ? ", password_hash=:phash" : "")
                . ", status=:ustatus
                      WHERE user_id=:uid";
            $stU = $this->db->prepare($sqlU);
            $params = [
                ':fn' => $d['first_name'],
                ':ln' => $d['last_name'],
                ':email' => $d['email'] ?? null,
                ':phone' => $d['phone'] ?? null,
                ':ustatus' => $d['status'] ?? 'active',
                ':uid' => (int) $s['user_id'],
            ];
            if (!empty($d['password'])) {
                $params[':phash'] = password_hash($d['password'], PASSWORD_DEFAULT);
            }
            $stU->execute($params);

            $stS = $this->db->prepare(
                "UPDATE supervisors
                    SET supervisor_code=:newcode, branch_id=:branch, manager_id=:manager, status=:status
                  WHERE supervisor_code=:code"
            );
            $stS->execute([
                ':newcode' => ($d['supervisor_code'] ?: $code),
                ':branch' => $branchId,
                ':manager' => $managerId,
                ':status' => $d['status'] ?? 'active',
                ':code' => $code,
            ]);

            $this->db->commit();
        } catch (\Throwable $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    // Handle deleteByCode operation.
    public function deleteByCode(string $code): void
    {
        $s = $this->findByCode($code);
        if (!$s)
            return;

        $this->db->beginTransaction();
        $this->db->prepare("DELETE FROM supervisors WHERE supervisor_code=:c")->execute([':c' => $code]);
        $this->db->prepare("DELETE FROM users WHERE user_id=:u")->execute([':u' => (int) $s['user_id']]);
        $this->db->commit();
    }
}
