<?php
declare(strict_types=1);

namespace app\model\admin;

use app\core\Database;
use PDO;

class Feedback
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = db();
    }

    /**
     * List feedback with filters.
     *
     * @param array $filters
     * @return array
     */
    public function list(array $filters = []): array
    {
        $sql = "
            SELECT
                f.feedback_id,
                f.appointment_id,
                f.rating,
                f.comment,
                f.reply_text,
                f.created_at,
                f.replied_status,
                f.replied_at,
                f.replied_by,

                a.appointment_date,
                a.appointment_time,

                s.name AS service_name,
                b.name AS branch_name,

                CONCAT(u.first_name, ' ', u.last_name) AS customer_name
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN services     s ON s.service_id     = a.service_id
            JOIN branches     b ON b.branch_id      = a.branch_id
            JOIN customers    c ON c.customer_id    = a.customer_id
            JOIN users        u ON u.user_id        = c.user_id
            WHERE 1=1
        ";

        $where  = [];
        $params = [];

        // Rating filter: 1–5
        if (!empty($filters['rating']) && ctype_digit((string)$filters['rating'])) {
            $where[]  = "f.rating = ?";
            $params[] = (int)$filters['rating'];
        }

        // Replied / not replied
        if (!empty($filters['replied'])) {
            if ($filters['replied'] === 'replied') {
                $where[] = "LOWER(f.replied_status) = 'replied'";
            } elseif ($filters['replied'] === 'notReplied') {
                $where[] = "LOWER(f.replied_status) <> 'replied'";
            }
        }

        // Date filter (created_at)
        if (!empty($filters['date'])) {
            $where[]  = "DATE(f.created_at) = ?";
            $params[] = $filters['date'];
        }

        // Text search
        if (!empty($filters['q'])) {
            $where[] = "
                (
                    s.name LIKE ?
                    OR b.name LIKE ?
                    OR CONCAT(u.first_name, ' ', u.last_name) LIKE ?
                    OR f.comment LIKE ?
                )
            ";
            $like = '%' . $filters['q'] . '%';
            // 4 placeholders above
            for ($i = 0; $i < 4; $i++) {
                $params[] = $like;
            }
        }

        if ($where) {
            $sql .= ' AND ' . implode(' AND ', $where);
        }

        $sql .= " ORDER BY f.created_at DESC LIMIT 200";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get a single feedback row with joins (if you ever need a show page).
     */
    public function findById(int $id): ?array
    {
        $sql = "
            SELECT
                f.*,
                a.appointment_date,
                a.appointment_time,
                s.name AS service_name,
                b.name AS branch_name,
                CONCAT(u.first_name, ' ', u.last_name) AS customer_name
            FROM feedback f
            JOIN appointments a ON a.appointment_id = f.appointment_id
            JOIN services     s ON s.service_id     = a.service_id
            JOIN branches     b ON b.branch_id      = a.branch_id
            JOIN customers    c ON c.customer_id    = a.customer_id
            JOIN users        u ON u.user_id        = c.user_id
            WHERE f.feedback_id = ?
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    /**
     * Save / update admin reply for a feedback item.
     */
    public function reply(int $id, string $replyText, int $adminUserId): bool
    {
        $sql = "
            UPDATE feedback
            SET
                reply_text     = ?,
                replied_status = 'Replied',
                replied_at     = NOW(),
                replied_by     = ?
            WHERE feedback_id = ?
        ";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$replyText, $adminUserId, $id]);
    }
}
