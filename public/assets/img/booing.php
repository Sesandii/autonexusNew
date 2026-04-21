//new field (special note to booking)

//booking controller

<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\public\BranchPublic;
use app\model\public\ServicePublic;
use app\model\customer\Vehicles;
use app\model\customer\Appointments;

/**
 * Handles customer booking flows, including re-booking and slot checks.
 */
class BookingController extends Controller
{
    /**
     * Render the booking form with branch/services/vehicle context.
     */
    public function index(): void
    {
        if (method_exists($this, 'requireLogin')) $this->requireLogin();

        $branchCode  = trim($_GET['branch'] ?? '');
        $serviceIdFromQuery = (int)($_GET['service_id'] ?? 0);
        $itemsParam  = (string)($_GET['items'] ?? '');
        $rebookId    = (int)($_GET['rebook'] ?? ($_GET['reschedule'] ?? 0));
        $bp          = new BranchPublic();
        $branches    = $bp->allActive();
        $branchName  = $branchCode ? ($bp->findNameByCode($branchCode) ?? null) : null;

        $userId   = (int)($this->userId());
        $vehicles = (new Vehicles())->byUserId($userId);

        if ($serviceIdFromQuery <= 0 && $itemsParam !== '') {
            $decoded = json_decode($itemsParam, true);
            if (is_array($decoded)) {
                $first = $decoded[0] ?? null;
                if (is_array($first)) {
                    $serviceIdFromItems = (int)($first['serviceId'] ?? ($first['service_id'] ?? 0));
                    if ($serviceIdFromItems > 0) {
                        $serviceIdFromQuery = $serviceIdFromItems;
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
                    'date'           => $prefillDate,
                    'time'           => $prefillTime,
                    'special_note'   => (string)($appt['special_note'] ?? ''),
                    'service_name'   => $appt['service_name'] ?? null,
                    'license_plate'  => $appt['license_plate'] ?? null,
                ];
            }
        }

        if (!$rebookId && $serviceIdFromQuery > 0) {
            $prefill['service_id'] = $serviceIdFromQuery;
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

    /**
     * Create one or more appointments from booking form input.
     */
    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo 'Method Not Allowed'; return; }
        if (method_exists($this, 'requireLogin')) $this->requireLogin();

        $userId     = (int)$this->userId();
        $branchCode = trim($_POST['branch_code'] ?? '');
        $vehicleId  = (int)($_POST['vehicle_id'] ?? 0);
        $serviceId  = (int)($_POST['service_id'] ?? 0);
        if ($serviceId <= 0) {
            $serviceIdsRaw = $_POST['service_ids'] ?? [];
            if (is_array($serviceIdsRaw) && !empty($serviceIdsRaw[0])) {
                $serviceId = (int)$serviceIdsRaw[0];
            }
        }
        $dateYmd    = trim($_POST['date'] ?? '');
        $time       = trim($_POST['time'] ?? '');
        $specialNote = trim((string)($_POST['special_note'] ?? ''));
        $rebookId   = (int)($_POST['rebook_id'] ?? 0);

        // very light validation
        if (!$branchCode || !$vehicleId || !$serviceId || !$dateYmd || !$time) {
            $_SESSION['flash'] = 'Please complete all fields.';
            $rebookParam = $rebookId > 0 ? '&rebook=' . $rebookId : '';
            $serviceParam = $serviceId > 0 ? '&service_id=' . urlencode((string)$serviceId) : '';
            header('Location: ' . $this->baseUrl() . '/customer/book?branch=' . urlencode($branchCode) . $serviceParam . $rebookParam);
            return;
        }

        if (strlen($specialNote) > 1000) {
            $_SESSION['flash'] = 'Special note cannot exceed 1000 characters.';
            $rebookParam = $rebookId > 0 ? '&rebook=' . $rebookId : '';
            $serviceParam = $serviceId > 0 ? '&service_id=' . urlencode((string)$serviceId) : '';
            header('Location: ' . $this->baseUrl() . '/customer/book?branch=' . urlencode($branchCode) . $serviceParam . $rebookParam);
            return;
        }

        [$ok, $msg] = (new Appointments())->createBooking(
            $userId, $branchCode, $vehicleId, $serviceId, $dateYmd, $time, $specialNote
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
            $serviceParam = $serviceId > 0 ? '&service_id=' . urlencode((string)$serviceId) : '';
            $dest = '/customer/book?branch=' . urlencode($branchCode) . $serviceParam . $rebookParam;
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


//appointment.php model

<?php
// app/model/customer/Appointments.php
declare(strict_types=1);

namespace app\model\customer;

use PDO;
use PDOException;

/**
 * Data access for customer appointments, booking creation, and ownership checks.
 */
class Appointments
{
    private PDO $pdo;
    private ?bool $hasSpecialNoteColumn = null;
    public function __construct() { $this->pdo = db(); }

    /**
     * Checks whether appointments.special_note exists.
     */
    private function appointmentsHasSpecialNoteColumn(): bool
    {
        if ($this->hasSpecialNoteColumn !== null) {
            return $this->hasSpecialNoteColumn;
        }

        $st = $this->pdo->query("SHOW COLUMNS FROM appointments LIKE 'special_note'");
        $this->hasSpecialNoteColumn = (bool)$st->fetch(PDO::FETCH_ASSOC);
        return $this->hasSpecialNoteColumn;
    }

    /**
     * Normalize user-provided time to HH:MM:SS format.
     */
    private function normalizeTime(string $time): string
    {
        if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
            if (strlen($time) === 5) {
                return $time . ':00';
            }
            return $time;
        }
        return '00:00:00';
    }

    /** Map user -> customer_id */
    private function customerIdByUserId(int $userId): ?int
    {
        $sql = "SELECT customer_id FROM customers WHERE user_id = :uid LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['uid' => $userId]);
        $cid = $st->fetchColumn();
        return $cid !== false ? (int)$cid : null;
    }

    /** Map branch_code -> branch_id */
    private function branchIdByCode(string $code): ?int
    {
        $sql = "SELECT branch_id FROM branches WHERE branch_code = :c LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['c' => $code]);
        $bid = $st->fetchColumn();
        return $bid !== false ? (int)$bid : null;
    }

    /** Verify vehicle belongs to customer */
    private function ownsVehicle(int $customerId, int $vehicleId): bool
    {
                $sql = "SELECT 1
                                    FROM vehicles
                                 WHERE vehicle_id = :v
                                     AND customer_id = :c
                                     AND COALESCE(status, 'available') <> 'sold'
                                 LIMIT 1";
        $st  = $this->pdo->prepare($sql);
        $st->execute(['v' => $vehicleId, 'c' => $customerId]);
        return (bool)$st->fetchColumn();
    }

    /**
     * Count active bookings in a branch/date/time slot.
     */
    private function countAtSlot(int $branchId, string $dateYmd, string $time): int
    {
        $sql = "SELECT COUNT(*) 
                  FROM appointments 
                 WHERE branch_id = :b 
                   AND appointment_date = :d 
                   AND appointment_time = :t
                   AND status IN ('requested','confirmed','ongoing','in_service')";
        $st = $this->pdo->prepare($sql);
        $st->execute([
            'b' => $branchId,
            'd' => $dateYmd,
            't' => $time
        ]);
        return (int)$st->fetchColumn();
    }

    /** Get slot availability for all time slots for a branch/date */
    public function getSlotAvailability(string $branchCode, string $dateYmd): array
    {
        $branchId = $this->branchIdByCode($branchCode);
        if (!$branchId) return [];

        $timeSlots = ['09:00:00', '10:00:00', '11:00:00', '13:00:00', '14:00:00', '15:00:00'];
        $cap = 3;
        $result = [];

        foreach ($timeSlots as $time) {
            $count = $this->countAtSlot($branchId, $dateYmd, $time);
            $shortTime = substr($time, 0, 5); // "09:00"
            $result[$shortTime] = [
                'booked'    => $count,
                'available' => max(0, $cap - $count),
                'full'      => $count >= $cap,
            ];
        }

        return $result;
    }

    /** Create a booking (returns [ok, message]) */
    public function createBooking(
        int $userId,
        string $branchCode,
        int $vehicleId,
        int $serviceId,
        string $dateYmd,
        string $time,
        string $specialNote = ''
    ): array {
        [$ok, $msg] = $this->createBookings($userId, $branchCode, $vehicleId, [$serviceId], $dateYmd, $time, $specialNote);
        if (!$ok) {
            return [$ok, $msg];
        }
        return [true, 'Booking created successfully.'];
    }

    /**
     * Create multiple bookings in one submission (one appointment per selected service).
     * Returns [ok, message].
     */
    public function createBookings(
        int $userId,
        string $branchCode,
        int $vehicleId,
        array $serviceIds,
        string $dateYmd,
        string $time,
        string $specialNote = ''
    ): array {
        try {
            $customerId = $this->customerIdByUserId($userId);
            if (!$customerId) return [false, 'Customer profile not found.'];

            $branchId = $this->branchIdByCode($branchCode);
            if (!$branchId) return [false, 'Invalid branch.'];

            if (!$this->ownsVehicle($customerId, $vehicleId)) {
                return [false, 'Selected vehicle does not belong to your account.'];
            }

            $serviceIds = array_values(array_unique(array_filter(array_map('intval', $serviceIds), fn($id) => $id > 0)));
            if (empty($serviceIds)) {
                return [false, 'Please select at least one service.'];
            }

            $placeholders = implode(',', array_fill(0, count($serviceIds), '?'));
            $serviceValidationSql = "
                SELECT COUNT(DISTINCT bs.service_id)
                FROM branch_services bs
                JOIN services s ON s.service_id = bs.service_id
                WHERE bs.branch_id = ?
                  AND COALESCE(s.status, 'active') = 'active'
                  AND bs.service_id IN ({$placeholders})
            ";
            $serviceValidationStmt = $this->pdo->prepare($serviceValidationSql);
            $serviceValidationStmt->execute(array_merge([$branchId], $serviceIds));
            $validServiceCount = (int)$serviceValidationStmt->fetchColumn();
            if ($validServiceCount !== count($serviceIds)) {
                return [false, 'One or more selected services are not available for this branch.'];
            }

            $time = $this->normalizeTime($time);

            // simple slot cap (max 3 per slot, as discussed earlier)
            $cap   = 3;
            $count = $this->countAtSlot($branchId, $dateYmd, $time);
            $requested = count($serviceIds);
            $available = max(0, $cap - $count);
            if ($requested > $available) {
                if ($available === 0) {
                    return [false, 'Selected time slot is full. Please choose another time.'];
                }
                return [false, "Only {$available} slot(s) are available for the selected time."];
            }

            $hasSpecialNote = $this->appointmentsHasSpecialNoteColumn();
            if ($hasSpecialNote) {
                $sql = "INSERT INTO appointments
                            (customer_id, branch_id, vehicle_id, service_id, appointment_date, appointment_time, special_note, status, created_at)
                        VALUES
                            (:cid, :bid, :vid, :sid, :d, :t, :special_note, 'requested', NOW())";
            } else {
                $sql = "INSERT INTO appointments
                            (customer_id, branch_id, vehicle_id, service_id, appointment_date, appointment_time, status, created_at)
                        VALUES
                            (:cid, :bid, :vid, :sid, :d, :t, 'requested', NOW())";
            }
            $st = $this->pdo->prepare($sql);

            $this->pdo->beginTransaction();
            foreach ($serviceIds a
            s $serviceId) {
                $params = [
                    'cid' => $customerId,
                    'bid' => $branchId,
                    'vid' => $vehicleId,
                    'sid' => $serviceId,
                    'd'   => $dateYmd,
                    't'   => $time,
                ];
                if ($hasSpecialNote) {
                    $params['special_note'] = $specialNote;
                }
                $st->execute($params);
            }
            $this->pdo->commit();

            if ($requested === 1) {
                return [true, 'Booking created successfully.'];
            }
            return [true, "{$requested} bookings created successfully."];
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            // optional: log $e->getMessage()
            return [false, 'Failed to create booking.'];
        }
    }

    /** Cancel appointment if it belongs to the current customer */
    public function cancelIfCustomerOwns(int $userId, int $appointmentId): bool
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid || $appointmentId <= 0) return false;

        // Only allow cancel of own appointment, and only if pending/requested/confirmed (not in_progress/completed)
        $sql = "UPDATE appointments
                   SET status = 'cancelled'
                 WHERE appointment_id = :id
                   AND customer_id = :cid
                   AND status IN ('requested','confirmed','pending')";
        $st = $this->pdo->prepare($sql);
        return $st->execute(['id' => $appointmentId, 'cid' => $cid]);
    }

    /** Reader used by your appointments page */
    public function getByCustomer(int $userId): array
    {
        $cid = $this->customerIdByUserId($userId);
        if (!$cid) return [];

        $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time, a.status,
                       b.name AS branch_name,
                       s.name AS service_name,
                       v.license_plate, v.make, v.model
                  FROM appointments a
             LEFT JOIN branches b ON b.branch_id = a.branch_id
             LEFT JOIN services s ON s.service_id = a.service_id
             LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
                 WHERE a.customer_id = :cid
              ORDER BY CASE a.status
                         WHEN 'in_service' THEN 1
                         WHEN 'requested' THEN 2
                         WHEN 'confirmed' THEN 3
                         ELSE 4
                       END,
                       a.appointment_date DESC, a.appointment_time DESC";
        $st = $this->pdo->prepare($sql);
        $st->execute(['cid' => $cid]);
        return $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

/**
 * Return completed appointments for a customer user.
 */
public function completedByUser(int $userId): array
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return [];

    $sql = "SELECT a.appointment_id, a.appointment_date, a.appointment_time,
                   wo.status, b.name AS branch_name, s.name AS service_name,
                   v.license_plate, v.make, v.model
              FROM appointments a
              JOIN work_orders wo ON wo.appointment_id = a.appointment_id AND wo.status = 'completed'
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
             WHERE a.customer_id = :cid
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $st = $this->pdo->prepare($sql);
    $st->execute(['cid' => $cid]);
    return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
}

/**
 * Return completed appointments that are still awaiting feedback.
 */
public function completedWithoutFeedbackByUser(int $userId): array
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return [];

    $sql = "SELECT a.appointment_id, 
                   a.appointment_date AS service_date, 
                   a.appointment_time AS service_time,
                   b.name AS branch_name, 
                   s.name AS service_name,
                   v.license_plate AS vehicle_license_plate,
                   v.make AS vehicle_make,
                   v.model AS vehicle_model,
                   wo.work_order_id,
                   wo.service_summary
              FROM appointments a
              JOIN work_orders wo ON wo.appointment_id = a.appointment_id AND wo.status = 'completed'
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
         LEFT JOIN feedback f ON f.appointment_id = a.appointment_id
             WHERE a.customer_id = :cid
               AND f.appointment_id IS NULL
          ORDER BY a.appointment_date DESC, a.appointment_time DESC";
    $st = $this->pdo->prepare($sql);
    $st->execute(['cid' => $cid]);
    return $st->fetchAll(\PDO::FETCH_ASSOC) ?: [];
}

/**
 * Verify a completed appointment belongs to the given user.
 */
public function appointmentBelongsToUserAndCompleted(int $userId, int $appointmentId): bool
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return false;

    $sql = "SELECT 1 FROM appointments a
              JOIN work_orders wo ON wo.appointment_id = a.appointment_id AND wo.status = 'completed'
             WHERE a.appointment_id = :aid
               AND a.customer_id = :cid
             LIMIT 1";
    $st = $this->pdo->prepare($sql);
    $st->execute(['aid' => $appointmentId, 'cid' => $cid]);
    return (bool)$st->fetchColumn();
}

/**
 * Fetch one appointment by id without customer ownership checks.
 */
public function getById(int $appointmentId): ?array
{
    $sql = "SELECT a.*, 
                   b.name AS branch_name,
                   s.name AS service_name,
                   v.license_plate, v.make, v.model,
                   c.customer_id
              FROM appointments a
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
         LEFT JOIN customers c ON c.customer_id = a.customer_id
             WHERE a.appointment_id = :aid
             LIMIT 1";
    $st = $this->pdo->prepare($sql);
    $st->execute(['aid' => $appointmentId]);
    $result = $st->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

/**
 * Fetch one appointment only if it belongs to the given customer user.
 */
    public function getAppointmentById(int $userId, int $appointmentId): ?array
{
    $cid = $this->customerIdByUserId($userId);
    if (!$cid) return null;

    $sql = "SELECT a.*, 
                   b.name AS branch_name, b.city AS branch_city, b.branch_code,
                   s.name AS service_name, s.description AS service_description,
                   v.license_plate, v.make, v.model, v.year AS vehicle_year,
                   wo.status AS work_order_status, wo.work_order_id,
                   wo.started_at AS service_start, wo.completed_at AS service_end
              FROM appointments a
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
         LEFT JOIN work_orders wo ON wo.appointment_id = a.appointment_id
             WHERE a.appointment_id = :aid
               AND a.customer_id = :cid
             LIMIT 1";
    $st = $this->pdo->prepare($sql);
    $st->execute(['aid' => $appointmentId, 'cid' => $cid]);
    $result = $st->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}

}


//views/customer/appointments/index.php


<?php
$base    = rtrim(BASE_URL,'/');
$selCode = $branch_code ?? '';
$prefill = $prefill ?? [];
$prefillVehicleId = (int)($prefill['vehicle_id'] ?? 0);
$prefillServiceId = (int)($prefill['service_id'] ?? 0);
$prefillDate      = $prefill['date'] ?? '';
$prefillTime      = $prefill['time'] ?? '';
$prefillSpecialNote = (string)($prefill['special_note'] ?? '');
$prefillRebookId  = (int)($prefill['appointment_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title><?= htmlspecialchars($title ?? 'Book Service') ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/page-header.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/booking.css" />
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/home.css" />
  <style>
    /* Slot availability styles */
    .session {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 4px;
      padding: 12px 16px;
      min-width: 80px;
      border: 2px solid #e5e7eb;
      border-radius: 8px;
      background: #fff;
      cursor: pointer;
      transition: all 0.2s;
    }
    .session:hover:not(.is-full) {
      border-color: #e74c3c;
      background: #fef2f2;
    }
    .session.is-selected {
      border-color: #e74c3c;
      background: #e74c3c;
      color: #fff;
    }
    .session.is-selected .slot-count {
      color: rgba(255,255,255,0.9);
    }
    .session.is-full {
      background: #fee2e2;
      border-color: #dc2626;
      cursor: not-allowed;
      opacity: 0.9;
    }
    .session.is-full .slot-time {
      color: #991b1b;
      text-decoration: line-through;
    }
    .session.is-full .slot-count {
      color: #dc2626;
      font-weight: 600;
    }
    .slot-time {
      font-weight: 600;
      font-size: 1rem;
    }
    .slot-count {
      font-size: 0.75rem;
      color: #6b7280;
    }
    /* Legend styles */
    .legend {
      display: flex;
      gap: 20px;
      margin-top: 16px;
      font-size: 0.85rem;
    }
    .lg {
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .lg-dot {
      width: 12px;
      height: 12px;
      border-radius: 50%;
    }
    .available-dot {
      background: #10b981;
      border: 2px solid #059669;
    }
    .full-dot {
      background: #dc2626;
      border: 2px solid #991b1b;
    }
  </style>
</head>
<body>

  <div class="container">
    <?php
      $branchSubtitle = !empty($branch_name) ? 'Selected branch: ' . htmlspecialchars($branch_name) : 'Choose your preferred branch and time slot.';
      $headerIcon = 'fa-solid fa-calendar-plus';
      $headerTitle = 'Book a Service';
      $headerSubtitle = $branchSubtitle;
      $headerActionBtn = '<a href="' . htmlspecialchars($base) . '/customer/appointments" class="back-to-appointments-btn"><i class="fa-solid fa-arrow-left"></i> Back to Appointments</a>';
      include APP_ROOT . '/views/partials/customer-page-header.php';
    ?>
    <?php if (!empty($flash)): ?>
      <div class="toast show" style="position:static;margin-bottom:16px;opacity:1"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <!-- Wrap everything in a POST form -->
    <form action="<?= $base ?>/customer/book" method="post" id="bookingForm">
      <?php if ($prefillRebookId > 0): ?>
        <input type="hidden" name="rebook_id" value="<?= $prefillRebookId ?>">
      <?php endif; ?>

      <!-- STEP 1: Branch -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">1</span>
          <h3>Select Branch</h3>
        </div>

        <div class="branch-grid">
          <?php foreach ($branches as $b):
            $checked = ($selCode && $selCode === $b['branch_code']) ? 'checked' : ''; ?>
            <label class="branch-card">
              <input type="radio" name="branch_pick" value="<?= htmlspecialchars($b['branch_code']) ?>" <?= $checked ?>>
              <div class="branch-card__content">
                <strong><?= htmlspecialchars($b['branch_name']) ?></strong><br>
                <small><?= htmlspecialchars($b['branch_code']) ?></small>
              </div>
            </label>
          <?php endforeach; ?>
        </div>

        <!-- will be submitted -->
        <input type="hidden" name="branch_code" id="branch_code" value="<?= htmlspecialchars($selCode) ?>">
        <div class="notes">Tip: changing branch reloads services for that branch.</div>
      </section>

      <!-- STEP 2: Vehicle -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">2</span>
          <h3>Select Vehicle</h3>
        </div>

        <?php if (!empty($vehicles)): ?>
          <div class="date-row" style="gap:12px;">
            <div class="date-field" style="min-width:260px;">
              <label for="vehicle_id">Your vehicles</label><br>
              <select id="vehicle_id" name="vehicle_id" required>
                <option value="" disabled <?= $prefillVehicleId ? '' : 'selected' ?>>-- Choose a vehicle --</option>
                <?php foreach ($vehicles as $v):
                  $label = trim(($v['license_plate'] ?? '') . ' — ' . ($v['make'] ?? '') . ' ' . ($v['model'] ?? ''));
                  $selected = ($prefillVehicleId && (int)$v['vehicle_id'] === $prefillVehicleId) ? 'selected' : '';
                ?>
                  <option value="<?= (int)$v['vehicle_id'] ?>" <?= $selected ?>>
                    <?= htmlspecialchars($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
              <div class="notes">We’ll attach this booking to the selected vehicle.</div>
            </div>
          </div>
        <?php else: ?>
          <p class="notes">
            You don’t have any vehicles saved yet.
            <a href="<?= $base ?>/customer/profile">Add a vehicle in your Profile</a> to continue.
          </p>
        <?php endif; ?>
      </section>

      <!-- STEP 3: Services (loaded from DB for chosen branch) -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">3</span>
          <h3>Select Service</h3>
        </div>

        <?php if (!empty($services)): ?>
          <div class="date-row">
            <div class="date-field" style="min-width:360px;">
              <label for="service_id">Available services at this branch</label><br>
              <select id="service_id" name="service_id" required aria-label="Available services">
                <option value="" disabled <?= $prefillServiceId ? '' : 'selected' ?>>-- Choose a service --</option>
                <?php
                  // Group by type_name as <optgroup>
                  $byType = [];
                  foreach ($services as $s) {
                    $byType[$s['type_name']][] = $s;
                  }
                  foreach ($byType as $type => $rows):
                ?>
                  <optgroup label="<?= htmlspecialchars($type) ?>">
                    <?php foreach ($rows as $r): ?>
                      <?php $sel = ($prefillServiceId && (int)$r['service_id'] === $prefillServiceId) ? 'selected' : ''; ?>
                      <option value="<?= (int)$r['service_id'] ?>" <?= $sel ?>>
                        <?= htmlspecialchars($r['service_name']) ?> — Rs. <?= number_format((float)$r['default_price'], 2) ?>
                      </option>
                    <?php endforeach; ?>
                  </optgroup>
                <?php endforeach; ?>
              </select>
              <div class="notes">Select one service. This list updates when you change the branch.</div>
            </div>
          </div>
        <?php else: ?>
          <p class="notes">Pick a branch first to see available services.</p>
        <?php endif; ?>
      </section>

      <!-- STEP 4: Date & Slot -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">4</span>
          <h3>Date & Time</h3>
        </div>

        <div class="date-row">
          <div class="date-field">
            <label for="date">Preferred date</label><br>
            <input type="date" id="date" name="date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($prefillDate) ?>" required>
            <div class="notes">We’ll show available time slots for your chosen date.</div>
          </div>
        </div>

        <div class="slot-grid" id="slotGrid" style="margin-top:14px;">
          <!-- Morning -->
          <div class="slot">
            <div class="slot__header">Morning</div>
            <div class="slot__sessions" data-period="am">
              <?php foreach (['09:00','10:00','11:00'] as $t): ?>
                <button type="button" class="session" data-time="<?= $t ?>">
                  <span class="slot-time"><?= $t ?></span>
                  <span class="slot-count">(0/3)</span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
          <!-- Afternoon -->
          <div class="slot">
            <div class="slot__header">Afternoon</div>
            <div class="slot__sessions" data-period="pm">
              <?php foreach (['13:00','14:00','15:00'] as $t): ?>
                <button type="button" class="session" data-time="<?= $t ?>">
                  <span class="slot-time"><?= $t ?></span>
                  <span class="slot-count">(0/3)</span>
                </button>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="legend">
          <!-- <span class="lg available"><span class="lg-dot available-dot"></span> Available</span> -->
          <span class="lg full"><span class="lg-dot full-dot"></span> Full (3/3)</span>
        </div>

        <!-- holds time for POST -->
        <input type="hidden" name="time" id="time" value="<?= htmlspecialchars($prefillTime) ?>">
      </section>

      <!-- STEP 5: Confirm -->
      <section class="card">
        <div class="step-title">
          <span class="step-badge">5</span>
          <h3>Special Note (Optional)</h3>
        </div>

        <div class="date-row">
          <div class="date-field" style="min-width:360px; width:100%;">
            <label for="special_note">Special note for service team</label><br>
            <textarea
              id="special_note"
              name="special_note"
              maxlength="1000"
              rows="4"
              style="width:100%;"
              placeholder="Add any special instructions or issues to check..."
            ><?= htmlspecialchars($prefillSpecialNote) ?></textarea>
            <div class="notes">Optional. Max 1000 characters.</div>
          </div>
        </div>

        <div class="footer-actions">
          <button class="btn-primary" id="bookNow" type="submit">Book Now</button>
        </div>
      </section>

    </form>
  </div>

  <div class="toast" id="toast">Booking created!</div>

  <script src="<?= $base ?>/public/assets/js/customer/booking.js" defer></script>
  <script>
    const base = "<?= $base ?>";
    const prefillDate  = "<?= htmlspecialchars($prefillDate, ENT_QUOTES) ?>";
    const prefillTime  = "<?= htmlspecialchars($prefillTime, ENT_QUOTES) ?>";
    const branchCodeInput = document.getElementById('branch_code');
    const dateInput = document.getElementById('date');
    const timeInput = document.getElementById('time');
    const rebookId = <?= $prefillRebookId ?>;

    // 1) when a branch is picked, reload page with ?branch=CODE (to load services server-side)
    document.querySelectorAll('input[name="branch_pick"]').forEach(r => {
      r.addEventListener('change', e => {
        const code = e.target.value;
        branchCodeInput.value = code;
        const selectedService = document.getElementById('service_id')?.value || '';
        const serviceParam = selectedService
          ? '&service_id=' + encodeURIComponent(selectedService)
          : '';
        const rebookParam = rebookId ? '&rebook=' + encodeURIComponent(String(rebookId)) : '';
        window.location.href = base + '/customer/book?branch=' + encodeURIComponent(code) + serviceParam + rebookParam;
      });
    });

    // 2) Fetch and update slot availability
    async function fetchSlotAvailability() {
      const branchCode = branchCodeInput.value.trim();
      const date = dateInput.value;
      
      if (!branchCode || !date) return;
      
      // Check if selected date is today
      const today = new Date();
      const selectedDate = new Date(date + 'T00:00:00');
      const isToday = selectedDate.toDateString() === today.toDateString();
      const currentHour = today.getHours();
      const currentMinute = today.getMinutes();
      
      try {
        const response = await fetch(`${base}/customer/book/slots?branch=${encodeURIComponent(branchCode)}&date=${encodeURIComponent(date)}`);
        const slots = await response.json();
        
        if (slots.error) {
          console.error('Slots error:', slots.error);
          return;
        }
        
        // Update each slot button
        document.querySelectorAll('.session').forEach(btn => {
          const time = btn.dataset.time;
          const slotData = slots[time];
          
          // Parse slot time (e.g., "09:00" -> hour=9, minute=0)
          const [slotHour, slotMinute] = time.split(':').map(Number);
          
          // Check if this time slot has already passed (for today only)
          const isPastSlot = isToday && (slotHour < currentHour || (slotHour === currentHour && slotMinute <= currentMinute));
          
          if (isPastSlot) {
            // Hide the slot if it has already passed
            btn.style.display = 'none';
            // Clear selection if this slot was selected
            if (timeInput.value === time) {
              timeInput.value = '';
            }
          } else {
            // Show the slot
            btn.style.display = '';
            
            if (slotData) {
              const countEl = btn.querySelector('.slot-count');
              countEl.textContent = `(${slotData.booked}/3)`;
              
              if (slotData.full) {
                btn.classList.add('is-full');
                btn.classList.remove('is-selected');
                btn.disabled = true;
                // Clear selection if this slot was selected
                if (timeInput.value === time) {
                  timeInput.value = '';
                }
              } else {
                btn.classList.remove('is-full');
                btn.disabled = false;
              }
            }
          }
        });
      } catch (err) {
        console.error('Failed to fetch slot availability:', err);
      }
    }

    // Fetch slots when date changes
    dateInput.addEventListener('change', fetchSlotAvailability);
    
    // Initial fetch if branch is already selected
    if (branchCodeInput.value && (dateInput.value || prefillDate)) {
      if (!dateInput.value && prefillDate) {
        dateInput.value = prefillDate;
      }
      fetchSlotAvailability().finally(selectPrefillTime);
    } else {
      selectPrefillTime();
    }

    // 3) time slot selection
    document.querySelectorAll('.session').forEach(btn => {
      btn.addEventListener('click', () => {
        if (btn.classList.contains('is-full')) return; // Don't select full slots
        document.querySelectorAll('.session').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
        timeInput.value = btn.dataset.time;
      });
    });

    function selectPrefillTime() {
      if (!prefillTime) return;
      const btn = document.querySelector(`.session[data-time="${prefillTime}"]`);
      if (btn && !btn.classList.contains('is-full')) {
        document.querySelectorAll('.session').forEach(b => b.classList.remove('is-selected'));
        btn.classList.add('is-selected');
      }
      timeInput.value = prefillTime;
    }

    // 4) sanity check before submit
    document.getElementById('bookingForm').addEventListener('submit', (e) => {
      const branch = branchCodeInput.value.trim();
      const veh    = document.getElementById('vehicle_id')?.value || '';
      const selectedService = document.getElementById('service_id')?.value || '';
      const date   = dateInput.value;
      const time   = timeInput.value;
      if (!branch || !veh || !selectedService || !date || !time) {
        e.preventDefault();
        alert('Please complete all fields (branch, vehicle, service, date & time).');
      }
    });
  </script>
</body>
</html>



//db 
ALTER TABLE appointments
ADD COLUMN special_note TEXT NULL AFTER appointment_time;

