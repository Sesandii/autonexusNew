<?php
namespace app\model\admin;

use PDO;
use Exception;

class Customer
{
    private PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function all(): array
    {
        $sql = "SELECT 
                    c.customer_id, c.customer_code, c.created_at,
                    u.user_id, u.first_name, u.last_name, u.email, u.phone, u.status
                FROM customers c
                JOIN users u ON u.user_id = c.user_id
                ORDER BY c.customer_id";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $customer_id): ?array
    {
        $st = $this->db->prepare(
            "SELECT c.customer_id, c.customer_code, c.created_at,
                    u.user_id, u.first_name, u.last_name, u.email, u.phone, u.status
             FROM customers c
             JOIN users u ON u.user_id = c.user_id
             WHERE c.customer_id = ?"
        );
        $st->execute([$customer_id]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(array $data): int
    {
        $this->db->beginTransaction();
        try {
            $sqlUser = "INSERT INTO users
                        (first_name, last_name, username, email, password_hash, phone, role, status, created_at)
                        VALUES (:first, :last, :username, :email, :hash, :phone, 'customer', :status, NOW())";
            $st = $this->db->prepare($sqlUser);
            $username = $this->uniqueUsername($data['first_name'] ?? '', $data['last_name'] ?? '');
            $st->execute([
                ':first'    => $data['first_name'] ?? '',
                ':last'     => $data['last_name']  ?? '',
                ':username' => $username,
                ':email'    => $data['email']      ?? null,
                ':hash'     => password_hash($data['password'] ?? 'autonexus', PASSWORD_DEFAULT),
                ':phone'    => $data['phone']      ?? null,
                ':status'   => $data['status']     ?? 'active',
            ]);
            $userId = (int)$this->db->lastInsertId();

            $code = $data['customer_code'] ?: $this->nextCustomerCode();
            $st2 = $this->db->prepare("INSERT INTO customers (user_id, customer_code, created_at) VALUES (?, ?, NOW())");
            $st2->execute([$userId, $code]);

            $custId = (int)$this->db->lastInsertId();
            $this->db->commit();
            return $custId;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function update(int $customer_id, array $data): void
    {
        $st = $this->db->prepare("SELECT user_id FROM customers WHERE customer_id = ?");
        $st->execute([$customer_id]);
        $uid = (int)($st->fetchColumn() ?: 0);
        if (!$uid) throw new Exception('Customer not found');

        $parts = [];
        $params = [':id' => $uid];

        foreach (['first_name','last_name','email','phone','status'] as $f) {
            if (array_key_exists($f, $data)) {
                $parts[] = "$f = :$f";
                $params[":$f"] = $data[$f];
            }
        }
        if (!empty($data['password'])) {
            $parts[] = "password_hash = :password_hash";
            $params[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if ($parts) {
            $sql = "UPDATE users SET " . implode(',', $parts) . " WHERE user_id = :id";
            $u = $this->db->prepare($sql);
            $u->execute($params);
        }

        if (isset($data['customer_code']) && $data['customer_code'] !== '') {
            $u2 = $this->db->prepare("UPDATE customers SET customer_code = :code WHERE customer_id = :cid");
            $u2->execute([':code' => $data['customer_code'], ':cid' => $customer_id]);
        }
    }

    public function delete(int $customer_id): void
    {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("SELECT user_id FROM customers WHERE customer_id = ?");
            $st->execute([$customer_id]);
            $uid = (int)($st->fetchColumn() ?: 0);
            if (!$uid) { $this->db->rollBack(); return; }

            $d1 = $this->db->prepare("DELETE FROM customers WHERE customer_id = ?");
            $d1->execute([$customer_id]);
            $d2 = $this->db->prepare("DELETE FROM users WHERE user_id = ?");
            $d2->execute([$uid]);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function nextCustomerCode(): string
    {
        $row = $this->db->query("SELECT customer_code FROM customers ORDER BY customer_id DESC LIMIT 1")
                        ->fetch(PDO::FETCH_ASSOC);
        $last = $row['customer_code'] ?? 'CUST001';
        $num  = (int)preg_replace('/\D/', '', $last);
        return sprintf('CUST%03d', $num + 1);
    }

    private function uniqueUsername(string $first, string $last): string
    {
        $base = strtolower(preg_replace('/\W+/','', $first . $last)) ?: 'customer';
        $try  = $base; $i = 1;
        $st = $this->db->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        while (true) {
            $st->execute([$try]);
            if ((int)$st->fetchColumn() === 0) return $try;
            $try = $base . $i++;
        }
    }
}
