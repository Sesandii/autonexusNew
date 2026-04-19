<?php /* Admin view: renders admin-ongoingservices/show page. */ ?>
<?php
/** @var array  $workOrder */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'progress';
$B = rtrim(BASE_URL, '/');
$w = $workOrder;

$dt = new DateTime($w['appointment_date'] . ' ' . $w['appointment_time']);
$uiStatus = \app\model\admin\OngoingService::uiStatus($w['work_status']);

// Timing logic for KPI
$completed = $w['completed_at'] ? new DateTime($w['completed_at']) : null;
$started = $w['started_at'] ? new DateTime($w['started_at']) : null;

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
    <title><?= e($pageTitle ?? 'Work Order Details') ?></title>

    <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-admin-ongoingservicesshow.css?v=1">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
    <header class="topbar">
        <div>
            <h1 class="page-title">Work Order Details</h1>
            <p class="subtitle">#<?= e($w['work_order_id']) ?> • <?= e($w['service_name']) ?></p>
        </div>
    </header>

    <div class="grid-three">
        <div class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="kpi-label">Duration</div>
            <div class="kpi-value">
                <?php 
                if ($started && $completed) {
                    echo (int) ($completed->diff($started)->format('%h')) . 'h ' . (int) ($completed->diff($started)->format('%i')) . 'm';
                } else {
                    echo (int)$w['base_duration_minutes'] . 'm';
                }
                ?>
            </div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
            <div class="kpi-label">Total Cost</div>
            <div class="kpi-value">Rs. <?= number_format((float)($w['total_cost'] ?? 0), 2) ?></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-signal"></i></div>
            <div class="kpi-label">Work Status</div>
            <div style="margin-top: 8px;">
                <?php
                $statusSlug = $w['work_status'] === 'open' ? 'received' : ($w['work_status'] === 'in_progress' ? 'in-service' : 'completed');
                ?>
                <span class="status-badge <?= $statusSlug ?>"><?= e($uiStatus) ?></span>
            </div>
        </div>
    </div>

    <div class="detail-grid">
        <div class="detail-card">
            <div class="card-header">
                <i class="fa-solid fa-circle-info"></i>
                <h3>Work Order Info</h3>
            </div>
            <div class="card-body">
                <?= fieldRow('fa-key', 'Work Order ID', '#' . e($w['work_order_id'])) ?>
                <?= fieldRow('fa-building', 'Branch', e($w['branch_name'])) ?>
                <?= fieldRow('fa-tools', 'Service', e($w['service_name'])) ?>
                <?= fieldRow('fa-dollar-sign', 'Default Price', 'Rs. ' . number_format((float)$w['default_price'], 2)) ?>
                <?= fieldRow('fa-play', 'Started At', e($w['started_at'] ?? '—')) ?>
                <?= fieldRow('fa-stop', 'Completed At', e($w['completed_at'] ?? '—')) ?>
            </div>
        </div>

        <div class="detail-card">
            <div class="card-header">
                <i class="fa-solid fa-calendar-check"></i>
                <h3>Appointment & Schedule</h3>
            </div>
            <div class="card-body">
                <?= fieldRow('fa-hashtag', 'Appointment ID', '#' . e($w['appointment_id'])) ?>
                <?= fieldRow('fa-calendar', 'Date', e($w['appointment_date'])) ?>
                <?= fieldRow('fa-clock', 'Time', $dt->format('g:i A')) ?>
                <?= fieldRow('fa-check-double', 'Status', e($w['appointment_status'])) ?>
                <?= fieldRow('fa-history', 'Booked At', e($w['appointment_created_at'] ?? '—')) ?>
            </div>
        </div>

        <div class="detail-card">
            <div class="card-header">
                <i class="fa-solid fa-car-side"></i>
                <h3>Customer & Vehicle</h3>
            </div>
            <div class="card-body">
                <?= fieldRow('fa-user', 'Customer', e($w['customer_name'])) ?>
                <?= fieldRow('fa-phone', 'Phone', e($w['customer_phone'] ?? '—')) ?>
                <hr style="border: 0; border-top: 1px solid #f3f4f6; margin: 10px 0;">
                <?= fieldRow('fa-barcode', 'Vehicle Code', e($w['vehicle_code'] ?? '—')) ?>
                <?= fieldRow('fa-id-card', 'License Plate', e($w['license_plate'] ?? '—')) ?>
                <?= fieldRow('fa-car', 'Make / Model', e(trim(($w['make'] ?? '') . ' ' . ($w['model'] ?? '')) ?: '—')) ?>
            </div>
        </div>

        <div class="detail-card">
            <div class="card-header">
                <i class="fa-solid fa-user-wrench"></i>
                <h3>Assignment</h3>
            </div>
            <div class="card-body">
                <?= fieldRow('fa-user-gear', 'Mechanic', e($w['mechanic_name'] ?? 'Unassigned')) ?>
                <?= fieldRow('fa-graduation-cap', 'Specialization', e($w['specialization'] ?? '—')) ?>
                <hr style="border: 0; border-top: 1px solid #f3f4f6; margin: 10px 0;">
                <?= fieldRow('fa-user-tie', 'Supervisor', e($w['supervisor_name'] ?? '—')) ?>
                <?= fieldRow('fa-phone-volume', 'Supervisor Phone', e($w['supervisor_phone'] ?? '—')) ?>
            </div>
        </div>
    </div>

    <?php if (!empty($w['service_summary']) || !empty($w['appointment_notes'])): ?>
        <div class="detail-card" style="margin-top: 20px;">
            <div class="card-header">
                <i class="fa-solid fa-note-sticky"></i>
                <h3>Notes & Summaries</h3>
            </div>
            <div class="card-body">
                <?php if (!empty($w['service_summary'])): ?>
                    <div class="field-label">Service Summary</div>
                    <div class="summary-box" style="margin-bottom: 15px;"><?= nl2br(e($w['service_summary'])) ?></div>
                <?php endif; ?>
                <?php if (!empty($w['appointment_notes'])): ?>
                    <div class="field-label">Appointment Notes</div>
                    <div class="summary-box"><?= nl2br(e($w['appointment_notes'])) ?></div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <div style="margin-top: 24px;">
        <a href="<?= $B ?>/admin/admin-ongoingservices" class="back-btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Ongoing Services
        </a>
    </div>
</main>
</body>
</html>