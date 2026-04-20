// update reviews
//controller

<?php
declare(strict_types=1);

namespace app\controllers\customer;

use app\core\Controller;
use app\model\customer\Appointments;

/**
 * Handles customer feedback submission for completed services.
 */
class FeedbackController extends Controller
{
/**
 * Show completed appointments that still need customer feedback.
 */
public function index(): void
{
    if (method_exists($this, 'requireCustomer')) {
        $this->requireCustomer();
    }

    $uid = (int)($_SESSION['user']['user_id'] ?? 0);
    $appointmentModel = new Appointments();

    // Show all completed appointments, including those with existing feedback,
    // so the customer can submit a new review or edit an existing one.
    $appointments = $appointmentModel->completedForFeedbackByUser($uid);

    $this->view('customer/feedback/index', [
        'title' => 'Rate Your Service',
        'appointments' => $appointments,
    ]);
}

/**
 * Persist customer rating and comment for a completed appointment.
 */
public function store(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo 'Method Not Allowed';
        return;
    }

    if (method_exists($this, 'requireCustomer')) {
        $this->requireCustomer();
    }

    $uid = (int)($_SESSION['user']['user_id'] ?? 0);
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

    // Insert new feedback or update existing feedback (edit option).
    try {
        $pdo = db();
        $existsStmt = $pdo->prepare(
            "SELECT feedback_id
               FROM feedback
              WHERE appointment_id = :a
              LIMIT 1"
        );
        $existsStmt->execute(['a' => $appointmentId]);
        $existingId = $existsStmt->fetchColumn();

        if ($existingId !== false) {
            $updateStmt = $pdo->prepare(
                "UPDATE feedback
                    SET rating = :r,
                        comment = :c
                  WHERE appointment_id = :a"
            );
            $updateStmt->execute([
                'a' => $appointmentId,
                'r' => $rating,
                'c' => $feedback,
            ]);

            $_SESSION['flash'] = 'Your feedback was updated.';
        } else {
            $insertStmt = $pdo->prepare(
                "INSERT INTO feedback (appointment_id, rating, comment, created_at)
                 VALUES (:a, :r, :c, NOW())"
            );
            $insertStmt->execute([
                'a' => $appointmentId,
                'r' => $rating,
                'c' => $feedback,
            ]);

            $_SESSION['flash'] = 'Thanks for your feedback!';
        }
    } catch (\PDOException $e) {
        $_SESSION['flash'] = 'Unable to save feedback right now. Please try again.';
    }

    header('Location: ' . rtrim(BASE_URL, '/') . '/customer/rate-service');
    exit;
}

}

//appointments.php (model)

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
    public function __construct() { $this->pdo = db(); }

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
        int $userId, string $branchCode, int $vehicleId, int $serviceId, string $dateYmd, string $time
    ): array {
        [$ok, $msg] = $this->createBookings($userId, $branchCode, $vehicleId, [$serviceId], $dateYmd, $time);
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
        string $time
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

            $sql = "INSERT INTO appointments
                        (customer_id, branch_id, vehicle_id, service_id, appointment_date, appointment_time, status, created_at)
                    VALUES
                        (:cid, :bid, :vid, :sid, :d, :t, 'requested', NOW())";
            $st = $this->pdo->prepare($sql);

            $this->pdo->beginTransaction();
            foreach ($serviceIds as $serviceId) {
                $st->execute([
                    'cid' => $customerId,
                    'bid' => $branchId,
                    'vid' => $vehicleId,
                    'sid' => $serviceId,
                    'd'   => $dateYmd,
                    't'   => $time,
                ]);
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
 * Return completed appointments for feedback form, including existing feedback values.
 */
public function completedForFeedbackByUser(int $userId): array
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
                   wo.service_summary,
                   f.feedback_id,
                   f.rating AS existing_rating,
                   f.comment AS existing_comment
              FROM appointments a
              JOIN work_orders wo ON wo.appointment_id = a.appointment_id AND wo.status = 'completed'
         LEFT JOIN branches b ON b.branch_id = a.branch_id
         LEFT JOIN services s ON s.service_id = a.service_id
         LEFT JOIN vehicles v ON v.vehicle_id = a.vehicle_id
         LEFT JOIN feedback f ON f.appointment_id = a.appointment_id
             WHERE a.customer_id = :cid
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

//index.php (view)-feedback

<?php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title ?? 'Rate Your Service') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/page-header.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/rate-service.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="<?= $base ?>/public/assets/js/customer/rate-service.js?v=<?= time() ?>" defer></script>
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <!-- Keep customer-layout-main so shared sidebar spacing stays consistent across customer pages. -->
  <div class="container customer-layout-main">
    <main class="main-content">

      <?php
        $headerIcon = 'fa-solid fa-star';
        $headerTitle = 'Rate Your Service';
        $headerSubtitle = 'Tell us how we did so we can keep improving every visit.';
        include APP_ROOT . '/views/partials/customer-page-header.php';

        $flashMessage = isset($_SESSION['flash']) ? trim((string)$_SESSION['flash']) : null;
        if ($flashMessage !== null) {
          unset($_SESSION['flash']);
        }
        $isBookingSuccess = $flashMessage !== null && (
          strcasecmp($flashMessage, 'Booking created successfully.') === 0 ||
          strcasecmp($flashMessage, 'Booking created successfully') === 0
        );
      ?>

      <!-- Flash Messages -->
      <?php if ($flashMessage !== null && !$isBookingSuccess): ?>
        <div class="flash-message">
          <i class="fa-solid fa-circle-check"></i>
          <?= htmlspecialchars($flashMessage) ?>
        </div>
      <?php endif; ?>
      <?php if (empty($appointments)): ?>
        <div class="empty-appointments">
          <i class="fa-regular fa-calendar-xmark"></i>
          <h3>No Services to Rate</h3>
          <p>You don't have any completed services yet.</p>
        </div>
      <?php else: ?>
      <form class="form-container" method="POST" action="<?= $base ?>/customer/rate-service" id="ratingForm">
        
        <!-- Step 1: Select Service -->
        <div class="form-section full-width">
          <h2 class="section-title">
            <span class="step-badge">1</span>
            Select Completed Service
          </h2>
          <div class="form-group">
            <label for="appointment">Choose a service to rate</label>
            <select id="appointment" name="appointment_id" required>
              <option value="">-- Select an appointment --</option>
              <?php foreach ($appointments as $a): ?>
                <?php $hasFeedback = !empty($a['feedback_id']); ?>
                <option 
                  value="<?= htmlspecialchars($a['appointment_id']) ?>"
                  data-vehicle="<?= htmlspecialchars($a['vehicle_license_plate'] ?? 'N/A') ?>"
                  data-model="<?= htmlspecialchars(($a['vehicle_make'] ?? '') . ' ' . ($a['vehicle_model'] ?? '')) ?>"
                  data-service="<?= htmlspecialchars($a['service_name'] ?? 'N/A') ?>"
                  data-date="<?= htmlspecialchars($a['service_date'] ?? '') ?>"
                  data-time="<?= htmlspecialchars($a['service_time'] ?? '') ?>"
                  data-existing-rating="<?= htmlspecialchars((string)($a['existing_rating'] ?? '0')) ?>"
                  data-existing-comment="<?= htmlspecialchars((string)($a['existing_comment'] ?? '')) ?>"
                  data-has-feedback="<?= $hasFeedback ? '1' : '0' ?>"
                >
                
                
                  <?= htmlspecialchars(
                    date('M d, Y', strtotime($a['service_date'])) . ' - ' . 
                    $a['service_name'] . ' - ' . 
                    $a['vehicle_license_plate'] .
                    ($hasFeedback ? ' (Edit submitted review)' : ' (New review)')
                  ) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Service Details (auto-filled) -->
        <div class="form-section full-width" id="serviceDetails" style="display: none;">
          <h2 class="section-title">
            <span class="step-badge">2</span>
            Service Details
          </h2>
          
          <div class="details-grid">
            <div class="detail-box">
              <i class="fa-solid fa-car"></i>
              <div class="detail-content">
                <span class="detail-label">Vehicle</span>
                <span class="detail-value" id="vehicleDisplay">-</span>
              </div>
            </div>
            
            <div class="detail-box">
              <i class="fa-solid fa-tag"></i>
              <div class="detail-content">
                <span class="detail-label">License Plate</span>
                <span class="detail-value" id="licensePlateDisplay">-</span>
              </div>
            </div>
            
            <div class="detail-box">
              <i class="fa-solid fa-wrench"></i>
              <div class="detail-content">
                <span class="detail-label">Service Type</span>
                <span class="detail-value" id="serviceDisplay">-</span>
              </div>
            </div>
            
            <div class="detail-box">
              <i class="fa-regular fa-calendar"></i>
              <div class="detail-content">
                <span class="detail-label">Service Date</span>
                <span class="detail-value" id="dateDisplay">-</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Rating Section -->
        <div class="form-section full-width" id="ratingSection" style="display: none;">
          <h2 class="section-title">
            <span class="step-badge">3</span>
            Your Rating
          </h2>
          
          <div class="form-group">
            <label>How would you rate this service? <span class="required">*</span></label>
            <div class="stars-wrapper">
              <div class="stars" id="ratingStars"></div>
              <span class="rating-text" id="ratingText">Select a rating</span>
            </div>
            <input type="hidden" name="rating" id="ratingInput" value="0" required>
            <span class="validation-msg" id="ratingError"></span>
          </div>
        </div>

        <!-- Feedback Section -->
         
        <div class="form-section full-width" id="feedbackSection" style="display: none;">
          <h2 class="section-title">
            <span class="step-badge">4</span>
            Your Feedback
          </h2>
          
          <div class="form-group">
            <label for="feedback">Tell us more about your experience (optional)</label>
            <textarea 
              id="feedback" 
              name="feedback" 
              placeholder="What did you like? What could we improve? Any specific comments about the service, technician, or branch?"
              rows="6"
            ></textarea>
            <div class="char-counter">
              <span id="charCount">0</span> / 500 characters
            </div>
          </div>
        </div>

        <!-- Submit Button -->

        <div class="form-group full-width actions-row" id="submitSection" style="display: none;">
          <button type="button" class="btn-secondary" onclick="window.location.href='<?= $base ?>/customer/dashboard'">
            <i class="fa-solid fa-xmark"></i>
            Cancel
          </button>
          <button type="submit" class="submit-btn" id="submitBtn">
            <i class="fa-regular fa-paper-plane"></i>
            Submit Review
          </button>
        </div>
      </form>

      <?php endif; ?>
    </main>
  </div>

  
</body>
</html>


//rateservices.js

// AutoNexus - Rate Service Interaction
document.addEventListener("DOMContentLoaded", () => {
  // ========================================
  // APPOINTMENT SELECTION & AUTO-FILL
  // ========================================
  const appointmentSelect = document.getElementById("appointment");
  const serviceDetails = document.getElementById("serviceDetails");
  const ratingSection = document.getElementById("ratingSection");
  const feedbackSection = document.getElementById("feedbackSection");
  const submitSection = document.getElementById("submitSection");

  const vehicleDisplay = document.getElementById("vehicleDisplay");
  const licensePlateDisplay = document.getElementById("licensePlateDisplay");
  const serviceDisplay = document.getElementById("serviceDisplay");
  const dateDisplay = document.getElementById("dateDisplay");
  const submitBtn = document.getElementById("submitBtn");

  // ========================================
  // STAR RATING SYSTEM
  // ========================================
  const starsContainer = document.getElementById("ratingStars");
  const ratingInput = document.getElementById("ratingInput");
  const ratingText = document.getElementById("ratingText");
  const ratingError = document.getElementById("ratingError");

  // ========================================
  // CHARACTER COUNTER
  // ========================================
  const feedbackTextarea = document.getElementById("feedback");
  const charCount = document.getElementById("charCount");
  const charCounter = document.querySelector(".char-counter");

  const ratingLabels = {
    0: "Select a rating",
    1: "⭐ Poor - Very dissatisfied",
    2: "⭐⭐ Fair - Could be better",
    3: "⭐⭐⭐ Good - Satisfied",
    4: "⭐⭐⭐⭐ Very Good - Highly satisfied",
    5: "⭐⭐⭐⭐⭐ Excellent - Extremely satisfied"
  };

  let currentRating = Number(ratingInput?.value || 0);

  function showSection(element) {
    if (element) {
      element.style.display = "block";
      element.style.opacity = "0";
      element.style.transform = "translateY(10px)";

      setTimeout(() => {
        element.style.transition = "all 0.4s ease";
        element.style.opacity = "1";
        element.style.transform = "translateY(0)";
      }, 10);
    }
  }

  function hideSection(element) {
    if (element) {
      element.style.display = "none";
    }
  }

  function updateSubmitButton(isEditMode) {
    if (!submitBtn) return;
    submitBtn.innerHTML = isEditMode
      ? '<i class="fa-regular fa-pen-to-square"></i> Update Review'
      : '<i class="fa-regular fa-paper-plane"></i> Submit Review';
  }

  function paintStars(rating) {
    if (!starsContainer) return;
    const stars = Array.from(starsContainer.querySelectorAll("i"));
    stars.forEach((star) => {
      const value = Number(star.dataset.value);
      star.classList.toggle("active", value <= rating);
    });
    if (ratingText) {
      ratingText.textContent = ratingLabels[rating] || "Select a rating";
      ratingText.classList.toggle("selected", rating > 0);
    }
  }

  function setExistingFeedback(selectedOption) {
    const existingRating = Number(selectedOption?.dataset.existingRating || 0);
    const existingComment = selectedOption?.dataset.existingComment || "";
    const hasFeedback = selectedOption?.dataset.hasFeedback === "1";

    currentRating = existingRating;

    if (ratingInput) {
      ratingInput.value = String(existingRating > 0 ? existingRating : 0);
    }
    paintStars(currentRating);

    if (feedbackTextarea) {
      feedbackTextarea.value = existingComment;
      if (charCount) {
        charCount.textContent = String(existingComment.length);
      }
      if (charCounter) {
        charCounter.classList.toggle("warning", existingComment.length > 450);
      }
    }

    updateSubmitButton(hasFeedback);
  }

  function handleSelection(selectedOption) {
    if (!selectedOption || !selectedOption.value) {
      hideSection(serviceDetails);
      hideSection(ratingSection);
      hideSection(feedbackSection);
      hideSection(submitSection);
      updateSubmitButton(false);
      return;
    }

    const vehicle = selectedOption.dataset.model || "N/A";
    const licensePlate = selectedOption.dataset.vehicle || "N/A";
    const service = selectedOption.dataset.service || "N/A";
    const date = selectedOption.dataset.date || "";
    const time = selectedOption.dataset.time || "";

    let formattedDate = date;
    if (date) {
      const dateObj = new Date(date);
      formattedDate = dateObj.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric"
      });
      if (time) {
        formattedDate += ` at ${time}`;
      }
    }

    vehicleDisplay.textContent = vehicle;
    licensePlateDisplay.textContent = licensePlate;
    serviceDisplay.textContent = service;
    dateDisplay.textContent = formattedDate;

    showSection(serviceDetails);
    setTimeout(() => showSection(ratingSection), 200);
    setTimeout(() => showSection(feedbackSection), 400);
    setTimeout(() => showSection(submitSection), 600);

    setExistingFeedback(selectedOption);
  }

  if (starsContainer && ratingInput) {
    // Create 5 stars
    for (let i = 1; i <= 5; i++) {
      const star = document.createElement("i");
      star.classList.add("fa-solid", "fa-star");
      star.dataset.value = String(i);
      starsContainer.appendChild(star);
    }

    const stars = Array.from(starsContainer.querySelectorAll("i"));

    // Initial paint
    paintStars(currentRating);

    stars.forEach((star) => {
      star.addEventListener("mouseenter", () => {
        const hoverValue = Number(star.dataset.value);
        paintStars(hoverValue);
      });

      star.addEventListener("click", () => {
        currentRating = Number(star.dataset.value);
        ratingInput.value = String(currentRating);
        paintStars(currentRating);
        if (ratingError) ratingError.classList.remove("show");
      });
    });

    starsContainer.addEventListener("mouseleave", () => {
      paintStars(currentRating);
    });
  }

  if (feedbackTextarea && charCount) {
    feedbackTextarea.addEventListener("input", () => {
      const length = feedbackTextarea.value.length;
      charCount.textContent = String(length);
      if (length > 450 && charCounter) {
        charCounter.classList.add("warning");
      } else if (charCounter) {
        charCounter.classList.remove("warning");
      }
      if (length > 500) {
        feedbackTextarea.value = feedbackTextarea.value.substring(0, 500);
        charCount.textContent = "500";
      }
    });
  }

  if (appointmentSelect) {
    appointmentSelect.addEventListener("change", (e) => {
      const selectedOption = e.target.options[e.target.selectedIndex];
      handleSelection(selectedOption);
    });

    // If there is a preselected option on load, show sections immediately
    const preselected = appointmentSelect.options[appointmentSelect.selectedIndex];
    if (preselected && preselected.value) {
      handleSelection(preselected);
    } else {
      updateSubmitButton(false);
    }
  }

  // ========================================
  // FORM VALIDATION
  // ========================================
  const form = document.getElementById("ratingForm");

  if (form) {
    form.addEventListener("submit", (e) => {
      let isValid = true;

      // Check if appointment is selected
      if (!appointmentSelect || !appointmentSelect.value) {
        alert("Please select a service to rate.");
        e.preventDefault();
        return false;
      }

      // Check if rating is selected
      const rating = Number(ratingInput?.value || 0);
      if (rating < 1 || rating > 5) {
        if (ratingError) {
          ratingError.textContent = "Please select a rating between 1 and 5 stars.";
          ratingError.classList.add("show");
        }
        isValid = false;
      }

      if (!isValid) {
        e.preventDefault();

        // Scroll to rating section
        if (ratingSection) {
          ratingSection.scrollIntoView({ behavior: "smooth", block: "center" });
        }
        return false;
      }

      // Disable submit button to prevent double submission
      if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
      }
    });
  }

  // ========================================
  // SMOOTH SCROLL FOR VALIDATION ERRORS
  // ========================================
  if (ratingError && ratingError.textContent) {
    ratingError.classList.add("show");
    if (ratingSection) {
      setTimeout(() => {
        ratingSection.scrollIntoView({ behavior: "smooth", block: "center" });
      }, 100);
    }
  }
});
