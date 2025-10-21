<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Appointments;

class FeedbackController extends Controller
{
    public function index(): void
    {
        // Require login if method exists
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        }

        // Get completed appointments for this user (stub for now)
        $appointmentModel = new Appointments();
        $appointments = $appointmentModel->completedByUser($_SESSION['user_id'] ?? 0);

        $this->view('customer/feedback/index', [
            'title' => 'Rate Your Service',
            'appointments' => $appointments,
        ]);
    }

    public function store(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Method Not Allowed';
            return;
        }

        $data = [
            'appointment_id' => $_POST['appointment_id'] ?? null,
            'rating'         => $_POST['rating'] ?? null,
            'feedback'       => trim($_POST['feedback'] ?? ''),
            'user_id'        => $_SESSION['user_id'] ?? null,
        ];

        // TODO: validate and insert into `feedback` table
        // Example:
        // $pdo = db();
        // $stmt = $pdo->prepare('INSERT INTO feedback (appointment_id, user_id, rating, comments) VALUES (:a, :u, :r, :f)');
        // $stmt->execute(['a'=>$data['appointment_id'],'u'=>$data['user_id'],'r'=>$data['rating'],'f'=>$data['feedback']]);

        $_SESSION['flash'] = 'Thanks for your feedback!';
        header('Location: ' . rtrim(BASE_URL, '/') . '/customer/rate-service');
        exit;
    }
}
