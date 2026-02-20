<?php
declare(strict_types=1);

namespace app\model\admin;

use PDO;
use PHPMailer\PHPMailer\PHPMailer;

class Notifications
{
    private PDO $db;

    public function __construct(?PDO $pdo = null)
    {
        $this->db = $pdo ?? db();
    }

    private function stmt(string $sql, array $params = [])
    {
        $st = $this->db->prepare($sql);
        $st->execute($params);
        return $st;
    }

    /* -------------------------------
     * Templates
     * ------------------------------- */
    public function templates(string $kind = 'manual'): array
    {
        $st = $this->db->prepare("
            SELECT template_key, title, default_subject, default_message, kind
            FROM notification_templates
            WHERE kind = :k
            ORDER BY title
        ");
        $st->execute([':k' => $kind]);
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    public function templateByKey(string $key): ?array
    {
        $st = $this->db->prepare("
            SELECT template_key, title, default_subject, default_message, kind
            FROM notification_templates
            WHERE template_key = :k
            LIMIT 1
        ");
        $st->execute([':k' => $key]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /* -------------------------------
     * Recent list
     * ------------------------------- */
    public function recent(int $limit = 20): array
    {
        $st = $this->db->prepare("
            SELECT
                n.notification_id,
                n.kind,
                n.template_key,
                n.subject,
                n.channel,
                n.audience,
                n.status,
                n.created_at,
                n.sent_at,
                n.recipients_total,
                n.recipients_sent,
                n.recipients_failed
            FROM notifications n
            ORDER BY n.created_at DESC
            LIMIT :lim
        ");
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /* -------------------------------
     * Recipient picking
     * ------------------------------- */

    /** For select list (AJAX): search customers */
    public function searchCustomerUsers(string $q, int $limit = 20): array
    {
        $q = trim($q);
        $st = $this->db->prepare("
            SELECT u.user_id, u.first_name, u.last_name, u.email
            FROM customers c
            JOIN users u ON u.user_id = c.user_id
            WHERE u.status = 'active'
              AND u.email IS NOT NULL AND u.email <> ''
              AND (
                u.first_name LIKE :q OR u.last_name LIKE :q OR u.email LIKE :q
              )
            ORDER BY u.first_name
            LIMIT :lim
        ");
        $st->bindValue(':q', "%{$q}%", PDO::PARAM_STR);
        $st->bindValue(':lim', $limit, PDO::PARAM_INT);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Convert an audience selection into recipients [{user_id,email,name}] */
    public function buildRecipients(string $audience, array $options = []): array
    {
        $audience = strtolower(trim($audience));

        if ($audience === 'all_customers') {
            return $this->db->query("
                SELECT u.user_id, u.email, CONCAT(u.first_name,' ',u.last_name) AS name
                FROM customers c
                JOIN users u ON u.user_id = c.user_id
                WHERE u.status='active' AND u.email IS NOT NULL AND u.email <> ''
            ")->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($audience === 'upcoming_appointments') {
            $days = max(1, (int)($options['days'] ?? 1));
            $st = $this->db->prepare("
                SELECT DISTINCT u.user_id, u.email, CONCAT(u.first_name,' ',u.last_name) AS name
                FROM appointments a
                JOIN customers c ON c.customer_id = a.customer_id
                JOIN users u ON u.user_id = c.user_id
                WHERE u.status='active'
                  AND u.email IS NOT NULL AND u.email <> ''
                  AND a.appointment_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
            ");
            $st->bindValue(':days', $days, PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($audience === 'recent_customers') {
            $days = max(1, (int)($options['days'] ?? 30));
            $st = $this->db->prepare("
                SELECT DISTINCT u.user_id, u.email, CONCAT(u.first_name,' ',u.last_name) AS name
                FROM appointments a
                JOIN customers c ON c.customer_id = a.customer_id
                JOIN users u ON u.user_id = c.user_id
                WHERE u.status='active'
                  AND u.email IS NOT NULL AND u.email <> ''
                  AND a.appointment_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            ");
            $st->bindValue(':days', $days, PDO::PARAM_INT);
            $st->execute();
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($audience === 'selected_users') {
            $ids = $options['user_ids'] ?? [];
            if (!is_array($ids) || empty($ids)) return [];

            $ids = array_values(array_unique(array_map('intval', $ids)));
            $placeholders = implode(',', array_fill(0, count($ids), '?'));

            $st = $this->db->prepare("
                SELECT u.user_id, u.email, CONCAT(u.first_name,' ',u.last_name) AS name
                FROM users u
                WHERE u.user_id IN ($placeholders)
                  AND u.status='active'
                  AND u.email IS NOT NULL AND u.email <> ''
            ");
            $st->execute($ids);
            return $st->fetchAll(PDO::FETCH_ASSOC);
        }

        if ($audience === 'custom') {
            $raw = (string)($options['custom_emails'] ?? '');
            $emails = preg_split('/[\s,;]+/', $raw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
            $uniq = [];
            foreach ($emails as $e) {
                $e = strtolower(trim($e));
                if (filter_var($e, FILTER_VALIDATE_EMAIL)) $uniq[$e] = true;
            }
            $out = [];
            foreach (array_keys($uniq) as $email) {
                $out[] = ['user_id' => null, 'email' => $email, 'name' => ''];
            }
            return $out;
        }

        return [];
    }

    /* -------------------------------
     * Create + send
     * ------------------------------- */
    public function createNotification(
        ?int $senderUserId,
        string $kind,
        ?string $templateKey,
        string $channel,
        string $audience,
        string $subject,
        string $message,
        array $recipients,
        array $meta = []
    ): int {
        $this->stmt("
            INSERT INTO notifications
              (sender_user_id, kind, template_key, channel, audience, subject, message, status, recipients_total, meta_json)
            VALUES
              (:sender, :kind, :tkey, :channel, :aud, :sub, :msg, 'queued', :total, :meta)
        ", [
            ':sender' => $senderUserId,
            ':kind'   => $kind,
            ':tkey'   => $templateKey,
            ':channel'=> $channel,
            ':aud'    => $audience,
            ':sub'    => $subject,
            ':msg'    => $message,
            ':total'  => count($recipients),
            ':meta'   => !empty($meta) ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
        ]);

        $nid = (int)$this->db->lastInsertId();

        $st = $this->db->prepare("
            INSERT INTO notification_recipients (notification_id, user_id, email, status)
            VALUES (:nid, :uid, :email, 'queued')
        ");
        foreach ($recipients as $r) {
            $st->execute([
                ':nid' => $nid,
                ':uid' => $r['user_id'] ?? null,
                ':email' => (string)$r['email'],
            ]);
        }

        return $nid;
    }

    public function queuedRecipients(int $notificationId): array
    {
        return $this->stmt("
            SELECT notification_recipient_id, user_id, email
            FROM notification_recipients
            WHERE notification_id = :nid AND status='queued'
            ORDER BY notification_recipient_id
        ", [':nid'=>$notificationId])->fetchAll(PDO::FETCH_ASSOC);
    }

    public function markRecipient(int $recipientId, string $status, ?string $error = null): void
    {
        $this->stmt("
            UPDATE notification_recipients
            SET status=:st,
                error=:err,
                sent_at = CASE WHEN :st2='sent' THEN NOW() ELSE sent_at END
            WHERE notification_recipient_id=:rid
        ", [
            ':st'=>$status,
            ':st2'=>$status,
            ':err'=>$error,
            ':rid'=>$recipientId,
        ]);
    }

    public function finalizeNotification(int $notificationId): void
    {
        $row = $this->stmt("
            SELECT
              SUM(status='sent') AS sent_count,
              SUM(status='failed') AS failed_count,
              COUNT(*) AS total_count
            FROM notification_recipients
            WHERE notification_id=:nid
        ", [':nid'=>$notificationId])->fetch(PDO::FETCH_ASSOC) ?: [];

        $sent = (int)($row['sent_count'] ?? 0);
        $failed = (int)($row['failed_count'] ?? 0);
        $total = (int)($row['total_count'] ?? 0);

        $status = ($failed > 0 && $sent === 0) ? 'failed' : 'sent';

        $this->stmt("
            UPDATE notifications
            SET status=:st, sent_at=NOW(),
                recipients_total=:t, recipients_sent=:s, recipients_failed=:f
            WHERE notification_id=:nid
        ", [
            ':st'=>$status,
            ':t'=>$total,
            ':s'=>$sent,
            ':f'=>$failed,
            ':nid'=>$notificationId,
        ]);
    }

    /** Send email batch (optionally attach a PDF file path) */
    public function sendEmailBatch(int $notificationId, array $smtp, ?string $attachPath = null, ?string $attachName = null): array
    {
        $rows = $this->queuedRecipients($notificationId);

        $n = $this->stmt("SELECT subject, message FROM notifications WHERE notification_id=:nid", [':nid'=>$notificationId])
            ->fetch(PDO::FETCH_ASSOC);

        $subject = (string)($n['subject'] ?? '');
        $message = (string)($n['message'] ?? '');

        $sent = 0;
        $failed = 0;

        foreach ($rows as $r) {
            $rid = (int)$r['notification_recipient_id'];
            $to  = (string)$r['email'];

            $mail = new PHPMailer(true);

            try {
                $mail->isSMTP();
                $mail->Host       = (string)$smtp['host'];
                $mail->SMTPAuth   = true;
                $mail->Username   = (string)$smtp['username'];
                $mail->Password   = (string)$smtp['password'];

                $secure = $smtp['secure'] ?? 'tls';
                $mail->SMTPSecure = ($secure === 'ssl')
                    ? PHPMailer::ENCRYPTION_SMTPS
                    : PHPMailer::ENCRYPTION_STARTTLS;

                $mail->Port = (int)$smtp['port'];
                $mail->CharSet = 'UTF-8';

                // If debugging mail delivery:
                // $mail->SMTPDebug = 2;

                $mail->setFrom((string)$smtp['from_email'], (string)$smtp['from_name']);
                $mail->addAddress($to);

                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'));

                if ($attachPath && is_file($attachPath)) {
                    $mail->addAttachment($attachPath, $attachName ?: basename($attachPath));
                }

                $mail->send();

                $this->markRecipient($rid, 'sent', null);
                $sent++;
            } catch (\Throwable $e) {
                $err = $mail->ErrorInfo ?: $e->getMessage();
                $this->markRecipient($rid, 'failed', $err);
                $failed++;
            }
        }

        $this->finalizeNotification($notificationId);
        return ['sent'=>$sent, 'failed'=>$failed, 'total'=>count($rows)];
    }

    /* -------------------------------
     * SYSTEM triggers (reusable)
     * ------------------------------- */

    /** Replace {{var}} placeholders */
    public function renderTemplate(string $text, array $vars): string
    {
        foreach ($vars as $k => $v) {
            $text = str_replace('{{'.$k.'}}', (string)$v, $text);
        }
        return $text;
    }

    /**
     * Send a system notification to specific user_id list
     * (appointment confirmation, invoice generated, etc.)
     */
    public function sendSystemToUsers(
        string $templateKey,
        array $userIds,
        array $vars,
        array $smtp,
        array $meta = [],
        ?string $attachPath = null,
        ?string $attachName = null
    ): array {
        $tpl = $this->templateByKey($templateKey);
        if (!$tpl) return ['sent'=>0,'failed'=>0,'total'=>0,'error'=>'Template not found'];

        $recipients = $this->buildRecipients('selected_users', ['user_ids'=>$userIds]);
        if (!$recipients) return ['sent'=>0,'failed'=>0,'total'=>0,'error'=>'No recipients'];

        $subject = $this->renderTemplate((string)$tpl['default_subject'], $vars);
        $message = $this->renderTemplate((string)$tpl['default_message'], $vars);

        $nid = $this->createNotification(
            null,
            'system',
            $templateKey,
            'email',
            'selected_users',
            $subject,
            $message,
            $recipients,
            $meta
        );

        return $this->sendEmailBatch($nid, $smtp, $attachPath, $attachName);
    }
}
