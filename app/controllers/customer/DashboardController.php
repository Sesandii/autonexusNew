<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Appointments;
use app\model\customer\Profile;
use app\model\customer\ServiceHistory;

class DashboardController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireCustomer')) {
            $this->requireCustomer();
        } elseif (method_exists($this, 'requireLogin')) {
            $this->requireLogin();
        }

        $userId = $this->userId();

        $appointmentsModel = new Appointments();
        $serviceHistoryModel = new ServiceHistory();
        $profileModel = new Profile();

        $appointments = $appointmentsModel->getByCustomer($userId);
        $history = $serviceHistoryModel->getByCustomer($userId);
        $profile = $profileModel->getProfile($userId);

        $nextAppointment = [
            'date' => '-',
            'service' => 'No appointment scheduled',
        ];

        $upcomingStatuses = ['requested', 'confirmed', 'pending', 'ongoing', 'in_service', 'in progress', 'in_progress'];
        $nowTs = time();
        $upcoming = [];

        foreach ($appointments as $row) {
            $status = strtolower(trim((string)($row['status'] ?? '')));
            if (!in_array($status, $upcomingStatuses, true)) {
                continue;
            }

            $date = (string)($row['appointment_date'] ?? '');
            $time = (string)($row['appointment_time'] ?? '00:00:00');
            $stamp = strtotime(trim($date . ' ' . $time));

            if ($stamp === false || $stamp < $nowTs) {
                continue;
            }

            $upcoming[] = [
                'stamp' => $stamp,
                'date' => $date,
                'time' => substr($time, 0, 5),
                'service' => (string)($row['service_name'] ?? 'Service'),
            ];
        }

        if (!empty($upcoming)) {
            usort($upcoming, static fn(array $a, array $b): int => $a['stamp'] <=> $b['stamp']);
            $next = $upcoming[0];

            $nextAppointment = [
                'date' => $next['date'],
                'service' => trim($next['service'] . ($next['time'] !== '' ? ' at ' . $next['time'] : '')),
            ];
        }

        $trackSummary = [
            'active' => 0,
            'note' => 'No active services right now',
        ];

        if (!empty($appointments)) {
            $activeStatuses = ['requested', 'confirmed', 'pending', 'ongoing', 'in_service', 'in progress', 'in_progress'];
            $inProgressStatuses = ['ongoing', 'in_service', 'in progress', 'in_progress'];
            $activeCount = 0;
            $inProgressCount = 0;

            foreach ($appointments as $row) {
                $status = strtolower(trim((string)($row['status'] ?? '')));
                if (!in_array($status, $activeStatuses, true)) {
                    continue;
                }

                $activeCount++;
                if (in_array($status, $inProgressStatuses, true)) {
                    $inProgressCount++;
                }
            }

            if ($activeCount > 0) {
                $trackSummary['active'] = $activeCount;
                $trackSummary['note'] = $inProgressCount > 0
                    ? ($inProgressCount . ' currently in progress')
                    : 'Awaiting service start';
            }
        }

        $recentServices = [];
        foreach (array_slice($history, 0, 5) as $item) {
            $recentServices[] = [
                'title' => (string)($item['service_type'] ?? 'Service'),
                'date' => (string)($item['date'] ?? ''),
            ];
        }

        $feedbackPending = count($appointmentsModel->completedWithoutFeedbackByUser($userId));

        $firstName = trim((string)($profile['first_name'] ?? $_SESSION['user']['first_name'] ?? $_SESSION['first_name'] ?? ''));
        $lastName = trim((string)($profile['last_name'] ?? $_SESSION['user']['last_name'] ?? $_SESSION['last_name'] ?? ''));
        $displayName = trim($firstName . ' ' . $lastName);
        if ($displayName === '') {
            $displayName = $firstName !== '' ? $firstName : 'Customer';
        }

        $data = [
            'user_first_name'   => $firstName !== '' ? $firstName : 'Customer',
            'user_display_name' => $displayName,
            'next_appointment'  => $nextAppointment,
            'track_summary'     => $trackSummary,
            'feedback_pending'  => $feedbackPending,
            'recent_services'   => $recentServices,
        ];

        $this->view('customer/dashboard/index', $data);
    }
}
