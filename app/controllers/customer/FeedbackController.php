<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Appointments;

class FeedbackController extends Controller
{
   public function index(): void
{
    $this->requireCustomer();

    $uid = $this->userId();
    $appointmentModel = new Appointments();

    $appointments = $appointmentModel->completedWithoutFeedbackByUser($uid);

    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);

    $this->view('customer/feedback/index', [
        'title'        => 'Rate Your Service',
        'appointments' => $appointments,
        'flash'        => $flash,
    ]);
}

public function store(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo 'Method Not Allowed';
        return;
    }

    $this->requireCustomer();

    $uid = $this->userId();
    $appointmentId = (int)($_POST['appointment_id'] ?? 0);
    $rating = (int)($_POST['rating'] ?? 0);
    $feedback = trim((string)($_POST['feedback'] ?? ''));

    // Basic validation
    if ($appointmentId <= 0 || $rating < 1 || $rating > 5) {
        $_SESSION['flash'] = 'Invalid form submission.';
        header('Location: ' . rtrim(BASE_URL, '/') . '/customer/rate-service');
        exit;
    }

    $model = new Appointments();
    if (!$model->appointmentBelongsToUserAndCompleted($uid, $appointmentId)) {
        $_SESSION['flash'] = 'You can only rate your own completed appointments.';
        header('Location: ' . rtrim(BASE_URL, '/') . '/customer/rate-service');
        exit;
    }

    // Insert feedback (prevents duplicates via UNIQUE constraint)
    try {
        $pdo = db();
        $stmt = $pdo->prepare(
            "INSERT INTO feedback (appointment_id, user_id, rating, comments, created_at)
             VALUES (:a, :u, :r, :c, NOW())"
        );
        $stmt->execute([
            'a' => $appointmentId,
            'u' => $uid,
            'r' => $rating,
            'c' => $feedback,
        ]);

        $_SESSION['flash'] = 'Thanks for your feedback!';
    } catch (\PDOException $e) {
        // Duplicate? (already rated)
        $_SESSION['flash'] = 'You have already submitted feedback for this appointment.';
    }

    header('Location: ' . rtrim(BASE_URL, '/') . '/customer/rate-service');
    exit;
}

}
