<?php
/** @var array  $workOrder */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'progress';
$B = rtrim(BASE_URL, '/');
$w = $workOrder;

$dt = new DateTime($w['appointment_date'] . ' ' . $w['appointment_time']);
$uiStatus = \app\model\admin\OngoingService::uiStatus($w['work_status']);

$badgeClass = $w['work_status'] === 'open'        ? 'received'
            : ($w['work_status'] === 'in_progress' ? 'in-service'
            : 'completed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Work Order Details') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content{margin-left:260px;padding:30px;background:#f4f5f7;min-height:100vh;}
    .grid-two{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:20px;}
    .card{background:#fff;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(15,23,42,.08);}
    .card h3{margin-top:0;margin-bottom:10px;}
    .field{font-size:14px;margin-bottom:6px;}
    .label{font-weight:600;color:#4b5563;margin-right:4px;}
    .badge{padding:4px 10px;border-radius:999px;font-size:12px;}
    .badge.received{background:#fee2e2;color:#b91c1c;}
    .badge.in-service{background:#dbeafe;color:#1d4ed8;}
    .badge.completed{background:#dcfce7;color:#15803d;}
    .actions{margin-top:20px;display:flex;gap:10px;}
    .btn{padding:8px 14px;border-radius:8px;border:none;cursor:pointer;font-size:14px;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
    .btn-primary{background:#111827;color:#fff;}
    .btn-secondary{background:#e5e7eb;color:#111827;}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/admin-ongoingservices">Ongoing Services</a>
      <span>›</span>
      <span>Work Order #<?= htmlspecialchars((string)$w['work_order_id']) ?></span>
    </div>
  </header>

  <section class="grid-two">
    <div class="card">
      <h3>Work Order Info</h3>
      <div class="field"><span class="label">Work Order ID:</span>#<?= htmlspecialchars((string)$w['work_order_id']) ?></div>
      <div class="field"><span class="label">Status:</span>
        <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($uiStatus) ?></span>
      </div>
      <div class="field"><span class="label">Branch:</span><?= htmlspecialchars($w['branch_name']) ?></div>
      <div class="field"><span class="label">Service:</span><?= htmlspecialchars($w['service_name']) ?></div>
      <div class="field"><span class="label">Duration:</span><?= (int)$w['base_duration_minutes'] ?> min</div>
      <div class="field"><span class="label">Default Price:</span><?= number_format((float)$w['default_price'], 2) ?></div>
      <div class="field"><span class="label">Started At:</span><?= htmlspecialchars($w['started_at'] ?? '—') ?></div>
      <div class="field"><span class="label">Completed At:</span><?= htmlspecialchars($w['completed_at'] ?? '—') ?></div>
      <div class="field"><span class="label">Total Cost:</span><?= number_format((float)$w['total_cost'], 2) ?></div>
      <div class="field">
        <span class="label">Service Summary:</span>
        <span><?= nl2br(htmlspecialchars($w['service_summary'] ?? '—')) ?></span>
      </div>
    </div>

    <div class="card">
      <h3>Appointment & Schedule</h3>
      <div class="field"><span class="label">Appointment ID:</span>#<?= htmlspecialchars((string)$w['appointment_id']) ?></div>
      <div class="field"><span class="label">Appointment Date:</span><?= htmlspecialchars($w['appointment_date']) ?></div>
      <div class="field"><span class="label">Appointment Time:</span><?= $dt->format('g:i A') ?></div>
      <div class="field"><span class="label">Appointment Status:</span><?= htmlspecialchars($w['appointment_status']) ?></div>
      <div class="field"><span class="label">Booked At:</span><?= htmlspecialchars($w['appointment_created_at'] ?? '—') ?></div>
      <div class="field"><span class="label">Last Updated:</span><?= htmlspecialchars($w['appointment_updated_at'] ?? '—') ?></div>
      <div class="field">
        <span class="label">Appointment Notes:</span>
        <span><?= nl2br(htmlspecialchars($w['appointment_notes'] ?? '—')) ?></span>
      </div>
    </div>
  </section>

  <section class="grid-two">
    <div class="card">
      <h3>Customer & Vehicle</h3>
      <div class="field"><span class="label">Customer:</span><?= htmlspecialchars($w['customer_name']) ?></div>
      <div class="field"><span class="label">Customer Code:</span><?= htmlspecialchars($w['customer_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Phone:</span><?= htmlspecialchars($w['customer_phone'] ?? '—') ?></div>
      <div class="field"><span class="label">Email:</span><?= htmlspecialchars($w['customer_email'] ?? '—') ?></div>

      <hr>

      <div class="field"><span class="label">Vehicle Code:</span><?= htmlspecialchars($w['vehicle_code'] ?? '—') ?></div>
      <div class="field"><span class="label">License Plate:</span><?= htmlspecialchars($w['license_plate'] ?? '—') ?></div>
      <div class="field"><span class="label">Make / Model:</span>
        <?= htmlspecialchars(trim(($w['make'] ?? '') . ' ' . ($w['model'] ?? '')) ?: '—') ?>
      </div>
      <div class="field"><span class="label">Year:</span><?= htmlspecialchars($w['year'] ?? '—') ?></div>
      <div class="field"><span class="label">Color:</span><?= htmlspecialchars($w['color'] ?? '—') ?></div>
    </div>

    <div class="card">
      <h3>Assignment</h3>
      <div class="field"><span class="label">Mechanic:</span><?= htmlspecialchars($w['mechanic_name'] ?? 'Unassigned') ?></div>
      <div class="field"><span class="label">Mechanic Code:</span><?= htmlspecialchars($w['mechanic_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Mechanic Phone:</span><?= htmlspecialchars($w['mechanic_phone'] ?? '—') ?></div>
      <div class="field"><span class="label">Specialization:</span><?= htmlspecialchars($w['specialization'] ?? '—') ?></div>
      <div class="field"><span class="label">Experience:</span><?= htmlspecialchars($w['experience_years'] !== null ? $w['experience_years'] . ' years' : '—') ?></div>

      <hr>

      <div class="field"><span class="label">Supervisor:</span><?= htmlspecialchars($w['supervisor_name'] ?? '—') ?></div>
      <div class="field"><span class="label">Supervisor Code:</span><?= htmlspecialchars($w['supervisor_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Supervisor Phone:</span><?= htmlspecialchars($w['supervisor_phone'] ?? '—') ?></div>
    </div>
  </section>

  <div class="actions">
    <a href="<?= $B ?>/admin/admin-ongoingservices" class="btn btn-secondary">
      ← Back to Ongoing Services
    </a>
  </div>
</main>
</body>
</html>
