<?php /* Admin view: renders admin-servicehistory/show page. */ ?>
<?php
/** @var array  $record */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'history';
$B = rtrim(BASE_URL, '/');
$r = $record;

$completed = $r['completed_at'] ? new DateTime($r['completed_at']) : null;
$started = $r['started_at'] ? new DateTime($r['started_at']) : null;

function e($value): string
{
  return htmlspecialchars((string) $value);
}

function fieldRow($icon, $label, $value): string
{
  return "<div class=\"detail-row\">
    <div class=\"detail-row-icon\"><i class=\"fa-solid {$icon}\"></i></div>
    <div class=\"detail-row-content\">
      <div class=\"detail-row-label\">$label</div>
      <div class=\"detail-row-value\">$value</div>
    </div>
  </div>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Service Details') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/servicehistory/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content service-history-page">
    <header class="topbar">
      <div>
        <h1 class="page-title">Service Details</h1>
        <p class="subtitle">Work Order #<?= e($r['work_order_id']) ?> • <?= e($r['service_name']) ?></p>
      </div>
    </header>

    <div class="grid-three">
      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-hourglass-end"></i></div>
        <div class="kpi-label">Duration</div>
        <div class="kpi-value">
          <?= $completed && $started ? (int) ($completed->diff($started)->format('%h')) . 'h ' . (int) ($completed->diff($started)->format('%i')) . 'm' : '—' ?>
        </div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
        <div class="kpi-label">Total Cost</div>
        <div class="kpi-value">Rs. <?= number_format((float) ($r['total_cost'] ?? 0), 2) ?></div>
      </div>

      <div class="kpi-card">
        <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
        <div class="kpi-label">Status</div>
        <div class="status-wrap"><span class="status-badge">Completed</span></div>
      </div>
    </div>

    <div class="detail-grid">
      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-wrench"></i>
          <h3>Service Details</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-key', 'Work Order ID', '#' . e($r['work_order_id'])) ?>
          <?= fieldRow('fa-tools', 'Service', e($r['service_name'])) ?>
          <?= fieldRow('fa-barcode', 'Service Code', e($r['service_code'] ?? '—')) ?>
          <?= fieldRow('fa-tag', 'Service Type', e($r['service_type'] ?? 'Service')) ?>
          <?= fieldRow('fa-dollar-sign', 'Default Price', 'Rs. ' . number_format((float) ($r['default_price'] ?? 0), 2)) ?>
        </div>
      </div>

      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-calendar-check"></i>
          <h3>Schedule</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-calendar', 'Appointment Date', e($r['appointment_date'])) ?>
          <?= fieldRow('fa-clock', 'Appointment Time', e($r['appointment_time'])) ?>
          <?= fieldRow('fa-play', 'Started At', $started ? $started->format('M j, Y g:i A') : '—') ?>
          <?= fieldRow('fa-stop', 'Completed At', $completed ? $completed->format('M j, Y g:i A') : '—') ?>
          <?= fieldRow('fa-circle-check', 'Appointment Status', e($r['appointment_status'])) ?>
        </div>
      </div>

      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-building"></i>
          <h3>Branch</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-building', 'Branch', e($r['branch_name'])) ?>
          <?= fieldRow('fa-barcode', 'Branch Code', e($r['branch_code'] ?? '—')) ?>
          <?= fieldRow('fa-location-dot', 'City', e($r['branch_city'] ?? '—')) ?>
          <?= fieldRow('fa-map', 'Address', e($r['branch_address'] ?? '—')) ?>
          <?= fieldRow('fa-phone', 'Phone', e($r['branch_phone'] ?? '—')) ?>
        </div>
      </div>
    </div>

    <div class="grid-two">
      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-user"></i>
          <h3>Customer Information</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-user', 'Customer Name', e($r['customer_name'])) ?>
          <?= fieldRow('fa-barcode', 'Customer Code', e($r['customer_code'] ?? '—')) ?>
          <?= fieldRow('fa-phone', 'Phone', e($r['customer_phone'] ?? '—')) ?>
          <?= fieldRow('fa-envelope', 'Email', e($r['customer_email'] ?? '—')) ?>
        </div>
      </div>

      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-car"></i>
          <h3>Vehicle Information</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-car', 'Vehicle Code', e($r['vehicle_code'] ?? '—')) ?>
          <?= fieldRow('fa-id-card', 'License Plate', e($r['license_plate'] ?? '—')) ?>
          <?= fieldRow('fa-car-side', 'Make/Model', e(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: '—')) ?>
          <?= fieldRow('fa-calendar', 'Year', e($r['year'] ?? '—')) ?>
          <?= fieldRow('fa-palette', 'Color', e($r['color'] ?? '—')) ?>
        </div>
      </div>
    </div>

    <div class="grid-two">
      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-user-wrench"></i>
          <h3>Assigned Mechanic</h3>
        </div>
        <div class="card-body">
          <?= fieldRow('fa-user', 'Mechanic Name', e($r['mechanic_name'] ?? '—')) ?>
          <?= fieldRow('fa-barcode', 'Mechanic Code', e($r['mechanic_code'] ?? '—')) ?>
          <?= fieldRow('fa-phone', 'Phone', e($r['mechanic_phone'] ?? '—')) ?>
          <?= fieldRow('fa-graduation-cap', 'Specialization', e($r['specialization'] ?? '—')) ?>
          <?= fieldRow('fa-briefcase', 'Experience', $r['experience_years'] !== null ? e($r['experience_years']) . ' years' : '—') ?>
        </div>
      </div>

      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-user-tie"></i>
          <h3>Assigned Supervisor</h3>
        </div>
        <div class="card-body">
          <?php
          $supName = trim($r['supervisor_name'] ?? '');
          $supCode = trim($r['supervisor_code'] ?? '');
          $supPhone = trim($r['supervisor_phone'] ?? '');
          ?>
          <?= fieldRow('fa-user-tie', 'Supervisor Name', $supName ?: '—') ?>
          <?= fieldRow('fa-barcode', 'Supervisor Code', $supCode ?: '—') ?>
          <?= fieldRow('fa-phone', 'Supervisor Phone', $supPhone ?: '—') ?>
        </div>
      </div>
    </div>

    <?php if (!empty($r['service_summary']) || !empty($r['appointment_notes'])): ?>
      <div class="detail-card">
        <div class="card-header">
          <i class="fa-solid fa-note-sticky"></i>
          <h3>Notes & Summary</h3>
        </div>
        <div class="card-body">
          <?php if (!empty($r['service_summary'])): ?>
            <div class="notes-block">
              <div class="field-label"><i class="fa-solid fa-clipboard"></i> Service Summary</div>
              <div class="summary-box">
                <?= nl2br(e($r['service_summary'])) ?>
              </div>
            </div>
          <?php endif; ?>

          <?php if (!empty($r['appointment_notes'])): ?>
            <div>
              <div class="field-label"><i class="fa-solid fa-sticky-note"></i> Appointment Notes</div>
              <div class="summary-box">
                <?= nl2br(e($r['appointment_notes'])) ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="back-wrap">
      <a href="<?= $B ?>/admin/admin-servicehistory" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Back to Service History
      </a>
    </div>
  </main>

</body>

</html>