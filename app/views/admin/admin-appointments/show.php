<?php /* Admin view: renders admin-appointments/show page. */ ?>
<?php
/** @var array  $appointment */
/** @var string $pageTitle */
/** @var string $current */
$current = $current ?? 'appointments';
$B = rtrim(BASE_URL, '/');
$a = $appointment;

function e($value): string
{
  return htmlspecialchars((string) ($value ?? ''));
}

$appointmentDate = !empty($a['appointment_date']) ? date('Y-m-d', strtotime((string) $a['appointment_date'])) : '—';
$appointmentTime = !empty($a['appointment_time'])
  ? date('h:i:s A', strtotime((string) $a['appointment_time']))
  : '—';

$statusLabel = \app\model\admin\Appointment::statusLabel((string) ($a['status'] ?? ''));
$badgeClass = [
  'Scheduled' => 'status-badge scheduled',
  'Confirmed' => 'status-badge confirmed',
  'In Progress' => 'status-badge progress',
  'Completed' => 'status-badge completed',
  'Cancelled' => 'status-badge cancelled',
][$statusLabel] ?? 'status-badge scheduled';

$workOrderId = $a['work_order_id'] ?? '—';
$serviceName = $a['service_name'] ?? '—';
$serviceCode = $a['service_code'] ?? '—';
$serviceType = $a['service_type'] ?? '—';
$defaultPrice = isset($a['default_price']) && $a['default_price'] !== null
  ? 'Rs. ' . number_format((float) $a['default_price'], 2)
  : '—';

$branchName = $a['branch_name'] ?? '—';
$branchCode = $a['branch_code'] ?? '—';
$branchCity = $a['branch_city'] ?? '—';
$branchAddress = $a['branch_address'] ?? '—';
$branchPhone = $a['branch_phone'] ?? '—';

$customerName = $a['customer_name'] ?? '—';
$customerPhone = $a['customer_phone'] ?? '—';
$customerEmail = $a['customer_email'] ?? '—';

$vehicleCode = $a['vehicle_code'] ?? '—';
$licensePlate = $a['license_plate'] ?? '—';
$makeModel = trim((string) ($a['make'] ?? '') . ' ' . (string) ($a['model'] ?? ''));
$makeModel = $makeModel !== '' ? $makeModel : '—';
$vehicleYear = $a['year'] ?? '—';
$vehicleColor = $a['color'] ?? '—';

$supervisorName = $a['supervisor_name'] ?? 'Not assigned';
$mechanicName = $a['mechanic_name'] ?? 'Not assigned';
$startedAt = $a['started_at'] ?? '—';
$completedAt = $a['completed_at'] ?? '—';
$totalCost = isset($a['total_cost']) && $a['total_cost'] !== null
  ? 'Rs. ' . number_format((float) $a['total_cost'], 2)
  : '—';
$notes = trim((string) ($a['notes'] ?? ''));
$notes = $notes !== '' ? $notes : '—';
$bookedAt = $a['created_at'] ?? '—';
$updatedAt = $a['updated_at'] ?? '—';
$workStatus = $a['work_status'] ?? 'Not created';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle ?? 'Appointment Details') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-dashboard.css?v=4">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-appointments-show.css?v=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content details-main">
    <header class="details-header">
      <h1 class="details-title">Appointment Details</h1>
      <p class="details-subtitle">
        Appointment #<?= e($a['appointment_id'] ?? '—') ?> • <?= e($serviceName) ?>
      </p>
    </header>

    <section class="details-grid">
      <article class="details-card">
        <div class="details-card-header">
          <i class="fa-solid fa-calendar-check"></i>
          <h3>Appointment Details</h3>
        </div>
        <div class="details-card-body">
          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-hashtag"></i></div>
            <div class="detail-content">
              <span class="detail-label">Appointment ID</span>
              <div class="detail-value">#<?= e($a['appointment_id'] ?? '—') ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-calendar"></i></div>
            <div class="detail-content">
              <span class="detail-label">Appointment Date</span>
              <div class="detail-value"><?= e($appointmentDate) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="detail-content">
              <span class="detail-label">Appointment Time</span>
              <div class="detail-value"><?= e($appointmentTime) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="detail-content">
              <span class="detail-label">Appointment Status</span>
              <div class="detail-value">
                <span class="<?= e($badgeClass) ?>"><?= e($statusLabel) ?></span>
              </div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-user-tie"></i></div>
            <div class="detail-content">
              <span class="detail-label">Supervisor</span>
              <div class="detail-value"><?= e($supervisorName) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-user-gear"></i></div>
            <div class="detail-content">
              <span class="detail-label">Mechanic</span>
              <div class="detail-value"><?= e($mechanicName) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-pen"></i></div>
            <div class="detail-content">
              <span class="detail-label">Notes</span>
              <div class="detail-value"><?= nl2br(e($notes)) ?></div>
            </div>
          </div>
        </div>
      </article>

      <article class="details-card">
        <div class="details-card-header">
          <i class="fa-solid fa-screwdriver-wrench"></i>
          <h3>Service Details</h3>
        </div>
        <div class="details-card-body">
          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-wrench"></i></div>
            <div class="detail-content">
              <span class="detail-label">Service</span>
              <div class="detail-value"><?= e($serviceName) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-barcode"></i></div>
            <div class="detail-content">
              <span class="detail-label">Service Code</span>
              <div class="detail-value"><?= e($serviceCode) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-tag"></i></div>
            <div class="detail-content">
              <span class="detail-label">Service Type</span>
              <div class="detail-value"><?= e($serviceType) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-dollar-sign"></i></div>
            <div class="detail-content">
              <span class="detail-label">Default Price</span>
              <div class="detail-value"><?= e($defaultPrice) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
            <div class="detail-content">
              <span class="detail-label">Work Order ID</span>
              <div class="detail-value"><?= e($workOrderId) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="detail-content">
              <span class="detail-label">Total Cost</span>
              <div class="detail-value"><?= e($totalCost) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-list-check"></i></div>
            <div class="detail-content">
              <span class="detail-label">Work Order Status</span>
              <div class="detail-value"><?= e($workStatus) ?></div>
            </div>
          </div>
        </div>
      </article>

      <article class="details-card">
        <div class="details-card-header">
          <i class="fa-solid fa-building"></i>
          <h3>Branch Details</h3>
        </div>
        <div class="details-card-body">
          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-building"></i></div>
            <div class="detail-content">
              <span class="detail-label">Branch</span>
              <div class="detail-value"><?= e($branchName) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-barcode"></i></div>
            <div class="detail-content">
              <span class="detail-label">Branch Code</span>
              <div class="detail-value"><?= e($branchCode) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-location-dot"></i></div>
            <div class="detail-content">
              <span class="detail-label">City</span>
              <div class="detail-value"><?= e($branchCity) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-map"></i></div>
            <div class="detail-content">
              <span class="detail-label">Address</span>
              <div class="detail-value"><?= e($branchAddress) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-content">
              <span class="detail-label">Phone</span>
              <div class="detail-value"><?= e($branchPhone) ?></div>
            </div>
          </div>
        </div>
      </article>
    </section>

    <section class="details-grid">
      <article class="details-card">
        <div class="details-card-header">
          <i class="fa-solid fa-user"></i>
          <h3>Customer Details</h3>
        </div>
        <div class="details-card-body">
          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-user"></i></div>
            <div class="detail-content">
              <span class="detail-label">Customer</span>
              <div class="detail-value"><?= e($customerName) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-phone"></i></div>
            <div class="detail-content">
              <span class="detail-label">Phone</span>
              <div class="detail-value"><?= e($customerPhone) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-envelope"></i></div>
            <div class="detail-content">
              <span class="detail-label">Email</span>
              <div class="detail-value"><?= e($customerEmail) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
            <div class="detail-content">
              <span class="detail-label">Booked At</span>
              <div class="detail-value"><?= e($bookedAt) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-arrows-rotate"></i></div>
            <div class="detail-content">
              <span class="detail-label">Last Updated</span>
              <div class="detail-value"><?= e($updatedAt) ?></div>
            </div>
          </div>
        </div>
      </article>

      <article class="details-card">
        <div class="details-card-header">
          <i class="fa-solid fa-car"></i>
          <h3>Vehicle Details</h3>
        </div>
        <div class="details-card-body">
          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-car-side"></i></div>
            <div class="detail-content">
              <span class="detail-label">Vehicle Code</span>
              <div class="detail-value"><?= e($vehicleCode) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-id-card"></i></div>
            <div class="detail-content">
              <span class="detail-label">License Plate</span>
              <div class="detail-value"><?= e($licensePlate) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-car-rear"></i></div>
            <div class="detail-content">
              <span class="detail-label">Make / Model</span>
              <div class="detail-value"><?= e($makeModel) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-calendar-days"></i></div>
            <div class="detail-content">
              <span class="detail-label">Year</span>
              <div class="detail-value"><?= e($vehicleYear) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-palette"></i></div>
            <div class="detail-content">
              <span class="detail-label">Color</span>
              <div class="detail-value"><?= e($vehicleColor) ?></div>
            </div>
          </div>
        </div>
      </article>

      <article class="details-card">
        <div class="details-card-header">
          <i class="fa-solid fa-clipboard-check"></i>
          <h3>Work Progress</h3>
        </div>
        <div class="details-card-body">
          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-play"></i></div>
            <div class="detail-content">
              <span class="detail-label">Started At</span>
              <div class="detail-value"><?= e($startedAt) ?></div>
            </div>
          </div>

          <div class="detail-item">
            <div class="detail-icon"><i class="fa-solid fa-square-check"></i></div>
            <div class="detail-content">
              <span class="detail-label">Completed At</span>
              <div class="detail-value"><?= e($completedAt) ?></div>
            </div>
          </div>
        </div>
      </article>
    </section>

    <div class="details-actions">
      <a href="<?= $B ?>/admin/admin-appointments" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i>
        <span>Back to list</span>
      </a>
    </div>
  </main>
</body>

</html>