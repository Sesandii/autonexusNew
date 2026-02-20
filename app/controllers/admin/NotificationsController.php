<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Notifications;

class NotificationsController extends Controller
{
    private Notifications $notifications;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->notifications = new Notifications();
    }

    private function smtp(): array
    {
        return [
            'host'       => 'smtp.gmail.com',
            'username'   => 'aautonexus@gmail.com',
            'password'   => 'mtfqejasdzjwajbe', // Gmail App Password
            'secure'     => 'tls',
            'port'       => 587,
            'from_email' => 'aautonexus@gmail.com',
            'from_name'  => 'AutoNexus',
        ];
    }

    /** GET /admin/admin-notifications */
    public function index(): void
    {
        $recent = $this->notifications->recent(20);
        $templates = $this->notifications->templates('manual');

        $flash = $_SESSION['flash_notifications'] ?? null;
        unset($_SESSION['flash_notifications']);

        $this->view('admin/admin-notifications/index', [
            'pageTitle'  => 'Notifications - AutoNexus',
            'current'    => 'notifications',
            'recent'     => $recent,
            'templates'  => $templates,
            'flash'      => $flash,
        ]);
    }

    /** GET /admin/admin-notifications/users?q= */
    public function users(): void
    {
        $this->requireAdmin();
        header('Content-Type: application/json; charset=utf-8');

        $q = (string)($_GET['q'] ?? '');
        $rows = $this->notifications->searchCustomerUsers($q, 25);
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /** POST /admin/admin-notifications/send (manual campaigns) */
    public function send(): void
    {
        $this->requireAdmin();

        $sender = (int)($_SESSION['user']['user_id'] ?? 0);
        if ($sender <= 0) {
            header('Location: ' . rtrim(BASE_URL,'/') . '/login');
            exit;
        }

        $templateKey = trim((string)($_POST['template_key'] ?? ''));
        $details     = trim((string)($_POST['details'] ?? ''));

        $audience = (string)($_POST['audience'] ?? 'all_customers');
        $subject  = trim((string)($_POST['subject'] ?? ''));
        $message  = trim((string)($_POST['message'] ?? ''));

        $customEmails = (string)($_POST['custom_emails'] ?? '');
        $daysUpcoming = (int)($_POST['upcoming_days'] ?? 1);
        $daysRecent   = (int)($_POST['recent_days'] ?? 30);
        $selectedUsers = $_POST['selected_users'] ?? [];

        // If a template chosen, auto-fill subject/message (but admin can edit)
        if ($templateKey !== '') {
            $tpl = $this->notifications->templateByKey($templateKey);
            if ($tpl) {
                if ($subject === '') $subject = (string)$tpl['default_subject'];
                if ($message === '') $message = (string)$tpl['default_message'];

                // Replace {{details}} for promo/holiday/closure/new-service
                $subject = $this->notifications->renderTemplate($subject, ['details'=>$details, 'name'=>'Customer']);
                $message = $this->notifications->renderTemplate($message, ['details'=>$details, 'name'=>'Customer']);
            }
        }

        if ($subject === '' || $message === '') {
            $_SESSION['flash_notifications'] = ['type'=>'error','text'=>'Subject and message are required.'];
            header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-notifications');
            exit;
        }

        $recipients = $this->notifications->buildRecipients($audience, [
            'custom_emails' => $customEmails,
            'days'          => ($audience === 'upcoming_appointments') ? $daysUpcoming : $daysRecent,
            'user_ids'      => $selectedUsers,
        ]);

        if (!$recipients) {
            $_SESSION['flash_notifications'] = ['type'=>'error','text'=>'No recipients matched that selection.'];
            header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-notifications');
            exit;
        }

        $nid = $this->notifications->createNotification(
            $sender,
            'manual',
            $templateKey ?: null,
            'email',
            $audience,
            $subject,
            $message,
            $recipients,
            ['details'=>$details]
        );

        $result = $this->notifications->sendEmailBatch($nid, $this->smtp());

        $_SESSION['flash_notifications'] = [
            'type' => ($result['failed'] > 0 ? 'warn' : 'success'),
            'text' => "Notification sent. Total: {$result['total']}, Sent: {$result['sent']}, Failed: {$result['failed']}.",
        ];

        header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-notifications');
        exit;
    }

    /**
     * POST /admin/admin-notifications/run-daily
     * Optional: run “appointment reminders + feedback requests” like a cron.
     */
    public function runDaily(): void
    {
        $this->requireAdmin();
        // This is just a hook endpoint. Real cron is better (Windows Task Scheduler / Linux cron).
        $_SESSION['flash_notifications'] = ['type'=>'success','text'=>'Daily notification runner endpoint hit. (Implement cron script for production)'];
        header('Location: ' . rtrim(BASE_URL,'/') . '/admin/admin-notifications');
        exit;
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL,'/') . '/login');
            exit;
        }
    }
}
