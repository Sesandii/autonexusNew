<?php
namespace app\model\admin;

use PDO;
use Exception;

class Mechanic
{
    private PDO $db;
    private string $table = 'mechanics';

    public function __construct()
    {
        $this->db = db();
    }

    /** List with joined user fields */
    public function all(): array
{
    $sql = "SELECT 
                m.mechanic_id, m.mechanic_code, m.user_id, m.branch_id,
                m.specialization, m.experience_years, m.status AS mech_status, m.created_at,
                u.first_name, u.last_name, u.email, u.phone, u.status AS user_status,
                b.branch_code, b.name AS branch_name
            FROM mechanics m
            JOIN users u    ON u.user_id = m.user_id
            LEFT JOIN branches b ON b.branch_id = m.branch_id
            ORDER BY m.mechanic_id DESC";
    return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
}

    /** Single record */
   public function find(int $mechanic_id): ?array
{
    $st = $this->db->prepare(
        "SELECT 
            m.mechanic_id, m.mechanic_code, m.user_id, m.branch_id,
            m.specialization, m.experience_years, m.status AS mech_status, m.created_at,
            u.first_name, u.last_name, u.email, u.phone, u.status AS user_status,
            b.branch_code, b.name AS branch_name
         FROM mechanics m
         JOIN users u    ON u.user_id = m.user_id
         LEFT JOIN branches b ON b.branch_id = m.branch_id
         WHERE m.mechanic_id = ?"
    );
    $st->execute([$mechanic_id]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

    /** Create: insert user (role=mechanic), then mechanic */
    public function create(array $d): int
    {
        $this->db->beginTransaction();
        try {
            // 1) users
            $userSql = "INSERT INTO users
                        (first_name, last_name, username, email, password_hash, phone, role, status, created_at)
                        VALUES (:first, :last, :username, :email, :hash, :phone, 'mechanic', :status, NOW())";
            $u = $this->db->prepare($userSql);

            $first = $d['first_name'] ?? '';
            $last  = $d['last_name']  ?? '';
            $username = $this->uniqueUsername($first, $last);
            $u->execute([
                ':first'    => $first,
                ':last'     => $last,
                ':username' => $username,
                ':email'    => $d['email'] ?? null,
                ':hash'     => password_hash($d['password'] ?? 'autonexus', PASSWORD_DEFAULT),
                ':phone'    => $d['phone'] ?? null,
                ':status'   => ($d['user_status'] ?? 'active'),
            ]);
            $userId = (int)$this->db->lastInsertId();
            if (!$userId) throw new Exception('Failed to create user');

            // 2) mechanics
            $mechSql = "INSERT INTO mechanics
                        (mechanic_code, user_id, branch_id, specialization, experience_years, status, created_at)
                        VALUES (:code, :user_id, :branch_id, :spec, :exp, :status, NOW())";
            $m = $this->db->prepare($mechSql);
            $m->execute([
                ':code'     => $d['mechanic_code'] ?: $this->nextMechanicCode(),
                ':user_id'  => $userId,
                ':branch_id'=> $d['branch_id'] !== '' ? (int)$d['branch_id'] : null,
                ':spec'     => $d['specialization'] ?? null,
                ':exp'      => (int)($d['experience_years'] ?? 0),
                ':status'   => $d['mech_status'] ?? 'active',
            ]);

            $mechanicId = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $mechanicId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Update both user + mechanic */
    public function update(int $mechanic_id, array $d): void
    {
        // find user_id
        $st = $this->db->prepare("SELECT user_id FROM mechanics WHERE mechanic_id = ?");
        $st->execute([$mechanic_id]);
        $userId = (int)($st->fetchColumn() ?: 0);
        if (!$userId) throw new Exception('Mechanic not found');

        $this->db->beginTransaction();
        try {
            // users: allow first/last/email/phone/status
            $parts = [];
            $params = [':id' => $userId];
            foreach (['first_name','last_name','email','phone','user_status'] as $f) {
                if (array_key_exists($f, $d)) {
                    $col = $f === 'user_status' ? 'status' : $f;
                    $parts[] = "$col = :$f";
                    $params[":$f"] = $d[$f];
                }
            }
            if (!empty($d['password'])) {
                $parts[] = "password_hash = :password_hash";
                $params[':password_hash'] = password_hash($d['password'], PASSWORD_DEFAULT);
            }
            if ($parts) {
                $sql = "UPDATE users SET " . implode(', ', $parts) . " WHERE user_id = :id";
                $u = $this->db->prepare($sql);
                $u->execute($params);
            }

            // mechanics
            $m = $this->db->prepare(
                "UPDATE mechanics
                 SET mechanic_code = :code,
                     branch_id = :branch_id,
                     specialization = :spec,
                     experience_years = :exp,
                     status = :mstatus
                 WHERE mechanic_id = :mid"
            );
            $m->execute([
                ':code'      => $d['mechanic_code'] ?? null,
                ':branch_id' => ($d['branch_id'] !== '' ? (int)$d['branch_id'] : null),
                ':spec'      => $d['specialization'] ?? null,
                ':exp'       => (int)($d['experience_years'] ?? 0),
                ':mstatus'   => $d['mech_status'] ?? 'active',
                ':mid'       => $mechanic_id,
            ]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /** Delete both sides */
    public function delete(int $mechanic_id): void
    {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("SELECT user_id FROM mechanics WHERE mechanic_id = ?");
            $st->execute([$mechanic_id]);
            $userId = (int)($st->fetchColumn() ?: 0);
            if (!$userId) { $this->db->rollBack(); return; }

            $d1 = $this->db->prepare("DELETE FROM mechanics WHERE mechanic_id = ?");
            $d1->execute([$mechanic_id]);
            $d2 = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            $d2->execute([$userId]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function nextMechanicCode(): string
    {
        $row = $this->db->query("SELECT mechanic_code FROM mechanics ORDER BY mechanic_id DESC LIMIT 1")
                        ->fetch(PDO::FETCH_ASSOC);
        $last = $row['mechanic_code'] ?? 'MEC000';
        $num  = (int)preg_replace('/\D/', '', $last);
        return sprintf('MEC%03d', $num + 1);
    }

    private function uniqueUsername(string $first, string $last): string
    {
        $base = strtolower(preg_replace('/\W+/', '', $first . $last)) ?: 'mechanic';
        $try  = $base; $i = 1;
        $st = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        while (true) {
            $st->execute([$try]);
            if ((int)$st->fetchColumn() === 0) return $try;
            $try = $base . $i++;
        }
    }
}
