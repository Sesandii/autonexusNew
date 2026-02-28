<?php
declare(strict_types=1);

namespace app\controllers\admin;

use app\core\Controller;
use app\model\admin\Feedback as FeedbackModel;

class FeedbackController extends Controller
{
    private FeedbackModel $feedback;

    public function __construct(array $config = [])
    {
        parent::__construct($config);
        $this->requireAdmin();
        $this->feedback = new FeedbackModel();
    }

    /** GET /admin/admin-viewfeedback */
    public function index(): void
    {
        $filters = [
            'q'       => trim($_GET['q']       ?? ''),
            'rating'  => $_GET['rating']      ?? '',
            'replied' => $_GET['replied']     ?? '',
            'date'    => $_GET['date']        ?? '',
        ];

        $rows = $this->feedback->list($filters);

        // simple average rating
        $avgRating = null;
        if ($rows) {
            $sum = 0;
            foreach ($rows as $r) {
                $sum += (int)$r['rating'];
            }
            $avgRating = round($sum / count($rows), 1);
        }

        $this->view('admin/admin-viewfeedback/index', [
            'pageTitle' => 'Customer Feedback - AutoNexus',
            'current'   => 'feedback',
            'feedbacks' => $rows,
            'filters'   => $filters,
            'avgRating' => $avgRating,
        ]);
    }

    /** POST /admin/admin-viewfeedback/reply */
    public function reply(): void
    {
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewfeedback');
            exit;
        }

        $id        = (int)($_POST['feedback_id'] ?? 0);
        $replyText = trim((string)($_POST['reply_text'] ?? ''));

        if ($id <= 0 || $replyText === '') {
            header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewfeedback');
            exit;
        }

        $adminUserId = (int)($_SESSION['user']['user_id'] ?? 0);

        $this->feedback->reply($id, $replyText, $adminUserId);

        // Redirect back to list (PRG)
        header('Location: ' . rtrim(BASE_URL, '/') . '/admin/admin-viewfeedback');
        exit;
    }

    private function requireAdmin(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $u = $_SESSION['user'] ?? null;
        if (!$u || (($u['role'] ?? '') !== 'admin')) {
            header('Location: ' . rtrim(BASE_URL, '/') . '/login');
            exit;
        }
    }
}
