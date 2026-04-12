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
  return "<div style=\"display:flex; gap:12px; padding:10px 0; border-bottom:1px solid #f3f4f6;\">
        <div style=\"color:#6b7280; width:24px; text-align:center;\"><i class=\"fa-solid {$icon}\"></i></div>
        <div style=\"flex:1;\">
            <div style=\"font-size:11px; font-weight:700; color:#9ca3af; text-transform:uppercase; letter-spacing:0.5px;\">$label</div>
            <div style=\"font-size:14px; color:#111827; margin-top:4px;\">$value</div>
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

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content {
      min-height: 100vh;
    }

    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 24px;
    }

    .page-title {
      font-size: 28px;
      font-weight: 700;
      margin: 10px 0 2px;
      color: #111827;
    }

    .subtitle {
      color: #6b7280;
      margin: 0 0 8px;
      font-size: 14px;
    }

    /* KPI Grid - Stays 3 columns always */
    .grid-three {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 20px;
    }

    .detail-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 20px;
      margin-bottom: 20px;
    }

    .detail-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
      overflow: hidden;
    }

    .card-header {
      background: #f9fafb;
      padding: 16px;
      border-bottom: 1px solid #e5e7eb;
      display: flex;
      align-items: center;
      gap: 10px;
    }

    .card-header h3 {
      margin: 0;
      font-size: 15px;
      font-weight: 700;
      color: #111827;
    }

    .card-header i {
      font-size: 18px;
      color: #6b7280;
    }

    .card-body {
      padding: 16px;
    }

    .field-label {
      font-size: 11px;
      font-weight: 700;
      color: #9ca3af;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin-bottom: 4px;
    }

    .summary-box {
      background: #f8fafc;
      border-left: 4px solid #2563eb;
      padding: 12px 14px;
      border-radius: 8px;
      font-size: 13px;
      line-height: 1.6;
      color: #374151;
    }

    .grid-two {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
      margin-bottom: 20px;
    }

    .status-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      background: #ecfdf5;
      color: #047857;
    }

    .back-btn {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      padding: 10px 16px;
      background: #f3f4f6;
      color: #111827;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      text-decoration: none;
      font-size: 14px;
      font-weight: 600;
      transition: all 0.15s ease;
    }

    .back-btn:hover {
      background: #e5e7eb;
    }

    .kpi-card {
      background: #fff;
      border-radius: 16px;
      padding: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
      text-align: center;
    }

    .kpi-icon {
      height: 48px;
      width: 48px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: #f3f4f6;
      margin: 0 auto 10px;
    }

    .kpi-icon i {
      font-size: 22px;
      color: #2563eb;
    }

    .kpi-label {
      font-size: 12px;
      color: #6b7280;
      font-weight: 600;
      margin-bottom: 6px;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .kpi-value {
      font-size: 24px;
      font-weight: 700;
      color: #111827;
    }

    @media (max-width: 120px) {
      .detail-grid {
        grid-template-columns: 1fr 1fr;
      }
    }

    @media (max-width: 76px) {
      /* Detail sections stack vertically */
      .detail-grid,
      .grid-two {
        grid-template-columns: 1fr;
      }

      /* Keep KPIs in 3 columns but reduce spacing and text size for fit */
      .grid-three {
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
      }

      .kpi-card {
        padding: 10px 5px;
      }

      .kpi-value {
        font-size: 14px;
      }

      .kpi-label {
        font-size: 9px;
      }

      .kpi-icon {
        height: 36px;
        width: 36px;
      }

      .kpi-icon i {
        font-size: 16px;
      }

      .main-content {
        padding: 16px 16px 24px;
      }
    }
  </style>
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
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
        <div style="margin-top: 8px;"><span class="status-badge">Completed</span></div>
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
            <div style="margin-bottom: 16px;">
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

    <div style="margin-top: 24px;">
      <a href="<?= $B ?>/admin/admin-servicehistory" class="back-btn">
        <i class="fa-solid fa-arrow-left"></i> Back to Service History
      </a>
    </div>
  </main>

</body>

</html>