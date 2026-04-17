<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\public\BranchPublic;
use app\model\public\ServicePublic;
use app\model\customer\Vehicles;
use app\model\customer\Appointments;

class BookingController extends Controller
{
    public function index(): void
    {
        if (method_exists($this, 'requireLogin')) $this->requireLogin();

        $branchCode  = trim($_GET['branch'] ?? '');
        $serviceIdFromQuery = (int)($_GET['service_id'] ?? 0);
        $serviceIdsFromQuery = [];
        $itemsParam  = (string)($_GET['items'] ?? '');
        $serviceIdsParam = trim((string)($_GET['service_ids'] ?? ''));
        $rebookId    = (int)($_GET['rebook'] ?? ($_GET['reschedule'] ?? 0));
        $bp          = new BranchPublic();
        $branches    = $bp->allActive();
        $branchName  = $branchCode ? ($bp->findNameByCode($branchCode) ?? null) : null;

        $userId   = (int)($this->userId());
        $vehicles = (new Vehicles())->byUserId($userId);

        $addServiceId = static function (array &$bucket, int $id): void {
            if ($id > 0 && !in_array($id, $bucket, true)) {
                $bucket[] = $id;
            }
        };

        if ($serviceIdFromQuery > 0) {
            $addServiceId($serviceIdsFromQuery, $serviceIdFromQuery);
        }

        if ($serviceIdsParam !== '') {
            foreach (explode(',', $serviceIdsParam) as $rawId) {
                $addServiceId($serviceIdsFromQuery, (int)trim($rawId));
            }
        }

        if ($itemsParam !== '') {
            $decoded = json_decode($itemsParam, true);
            if (is_array($decoded)) {
                foreach ($decoded as $item) {
                    if (is_array($item)) {
                        $serviceIdFromItems = (int)($item['serviceId'] ?? ($item['service_id'] ?? 0));
                        if ($serviceIdFromItems > 0) {
                            $addServiceId($serviceIdsFromQuery, $serviceIdFromItems);
                        }
                    }
                }
            }
        }

        // Prefill data when rebooking
        $prefill = [];
        if ($rebookId) {
            $appt = (new Appointments())->getAppointmentById($userId, $rebookId);
            if ($appt) {
                $branchCode = $branchCode ?: (string)($appt['branch_code'] ?? '');
                $branchName = $branchCode ? ($bp->findNameByCode($branchCode) ?? $branchName) : $branchName;

                $apptDate = substr((string)($appt['appointment_date'] ?? ''), 0, 10);
                $today    = date('Y-m-d');
                $prefillDate = ($apptDate && $apptDate >= $today) ? $apptDate : '';
                $prefillTime = substr((string)($appt['appointment_time'] ?? ''), 0, 5);

                $prefill = [
                    'appointment_id' => $rebookId,
                    'branch_code'    => $branchCode,
                    'vehicle_id'     => (int)($appt['vehicle_id'] ?? 0),
                    'service_id'     => (int)($appt['service_id'] ?? 0),
                    'service_ids'    => [(int)($appt['service_id'] ?? 0)],
                    'date'           => $prefillDate,
                    'time'           => $prefillTime,
                    'service_name'   => $appt['service_name'] ?? null,
                    'license_plate'  => $appt['license_plate'] ?? null,
                ];
            }
        }

        if (!$rebookId && !empty($serviceIdsFromQuery)) {
            $prefill['service_ids'] = $serviceIdsFromQuery;
            $prefill['service_id'] = (int)$serviceIdsFromQuery[0];
        }

        // services for selected branch (server-rendered)
        $services = [];
        if ($branchCode !== '') {
            $services = (new ServicePublic())->byBranchCode($branchCode);
        }

        $this->view('customer/booking/index', [
            'title'       => 'AutoNexus • Book Service',
            'branches'    => $branches,
            'branch_code' => $branchCode,
            'branch_name' => $branchName,
            'vehicles'    => $vehicles,
            'services'    => $services,
            'prefill'     => $prefill,
            'flash'       => $_SESSION['flash'] ?? null,
        ]);
        unset($_SESSION['flash']);
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        if (method_exists($this, 'requireLogin')) $this->requireLogin();

        $userId     = (int)$this->userId();
        $branchCode = trim($_POST['branch_code'] ?? '');
        $vehicleId  = (int)($_POST['vehicle_id'] ?? 0);
        $serviceIdsRaw = $_POST['service_ids'] ?? [];
        if (!is_array($serviceIdsRaw)) {
            $serviceIdsRaw = [];
        }
        $serviceIds = array_values(array_unique(array_filter(array_map('intval', $serviceIdsRaw), fn($id) => $id > 0)));
        if (empty($serviceIds)) {
            $fallbackServiceId = (int)($_POST['service_id'] ?? 0);
            if ($fallbackServiceId > 0) {
                $serviceIds = [$fallbackServiceId];
            }
        }
        $dateYmd    = trim($_POST['date'] ?? '');
        $time       = trim($_POST['time'] ?? '');
        $rebookId   = (int)($_POST['rebook_id'] ?? 0);

        // very light validation
        if (!$branchCode || !$vehicleId || empty($serviceIds) || !$dateYmd || !$time) {
            $_SESSION['flash'] = 'Please complete all fields.';
            $rebookParam = $rebookId > 0 ? '&rebook=' . $rebookId : '';
            $serviceIdsParam = !empty($serviceIds) ? '&service_ids=' . urlencode(implode(',', $serviceIds)) : '';
            header('Location: ' . $this->baseUrl() . '/customer/book?branch=' . urlencode($branchCode) . $serviceIdsParam . $rebookParam);
            return;
        }

        [$ok, $msg] = (new Appointments())->createBookings(
            $userId, $branchCode, $vehicleId, $serviceIds, $dateYmd, $time
        );

        if ($ok && $rebookId > 0) {
            (new Appointments())->cancelIfCustomerOwns($userId, $rebookId);
        }

        $_SESSION['flash'] = $msg;
        // on success go to appointments page
        if ($ok) {
            $dest = '/customer/appointments';
        } else {
            $rebookParam = $rebookId > 0 ? '&rebook=' . $rebookId : '';
            $serviceIdsParam = !empty($serviceIds) ? '&service_ids=' . urlencode(implode(',', $serviceIds)) : '';
            $dest = '/customer/book?branch=' . urlencode($branchCode) . $serviceIdsParam . $rebookParam;
        }
        header('Location: ' . $this->baseUrl() . $dest);
    }

    /** API endpoint to get slot availability for a branch/date */
    public function slots(): void
    {
        header('Content-Type: application/json');
        
        $branchCode = trim($_GET['branch'] ?? '');
        $date       = trim($_GET['date'] ?? '');

        if (!$branchCode || !$date) {
            echo json_encode(['error' => 'Missing branch or date']);
            return;
        }

        $availability = (new Appointments())->getSlotAvailability($branchCode, $date);
        echo json_encode($availability);
    }
}
