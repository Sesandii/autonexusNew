<?php
/** @var array  $record */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'history';
$B = rtrim(BASE_URL, '/');
$r = $record;

$completed = $r['completed_at'] ? new DateTime($r['completed_at']) : null;
$started   = $r['started_at']   ? new DateTime($r['started_at'])   : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Service Details') ?></title>

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
    .actions{margin-top:20px;display:flex;gap:10px;}
    .btn{padding:8px 14px;border-radius:8px;border:none;cursor:pointer;font-size:14px;display:inline-flex;align-items:center;gap:6px;text-decoration:none;}
    .btn-secondary{background:#e5e7eb;color:#111827;}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/admin-servicehistory">Service History</a>
      <span>›</span>
      <span>Work Order #<?= htmlspecialchars((string)$r['work_order_id']) ?></span>
    </div>
  </header>

  <section class="grid-two">
    <div class="card">
      <h3>Service & Work Order</h3>
      <div class="field"><span class="label">Work Order ID:</span>#<?= htmlspecialchars((string)$r['work_order_id']) ?></div>
      <div class="field"><span class="label">Service:</span><?= htmlspecialchars($r['service_name']) ?></div>
      <div class="field"><span class="label">Service Code:</span><?= htmlspecialchars($r['service_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Type:</span><?= htmlspecialchars($r['service_type'] ?? 'Service') ?></div>
      <div class="field"><span class="label">Branch:</span><?= htmlspecialchars($r['branch_name']) ?> (<?= htmlspecialchars($r['branch_code'] ?? '') ?>)</div>
      <div class="field"><span class="label">Appointment Date:</span><?= htmlspecialchars($r['appointment_date']) ?></div>
      <div class="field"><span class="label">Appointment Time:</span><?= htmlspecialchars($r['appointment_time']) ?></div>
      <div class="field"><span class="label">Started At:</span><?= $started ? $started->format('M j, Y g:i A') : '—' ?></div>
      <div class="field"><span class="label">Completed At:</span><?= $completed ? $completed->format('M j, Y g:i A') : '—' ?></div>
      <div class="field"><span class="label">Default Price:</span><?= number_format((float)$r['default_price'], 2) ?></div>
      <div class="field"><span class="label">Total Cost:</span><?= number_format((float)$r['total_cost'], 2) ?></div>
      <div class="field">
        <span class="label">Service Summary:</span>
        <span><?= nl2br(htmlspecialchars($r['service_summary'] ?? '—')) ?></span>
      </div>
    </div>

    <div class="card">
      <h3>Customer & Vehicle</h3>
      <div class="field"><span class="label">Customer:</span><?= htmlspecialchars($r['customer_name']) ?></div>
      <div class="field"><span class="label">Customer Code:</span><?= htmlspecialchars($r['customer_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Phone:</span><?= htmlspecialchars($r['customer_phone'] ?? '—') ?></div>
      <div class="field"><span class="label">Email:</span><?= htmlspecialchars($r['customer_email'] ?? '—') ?></div>
      <hr>
      <div class="field"><span class="label">Vehicle Code:</span><?= htmlspecialchars($r['vehicle_code'] ?? '—') ?></div>
      <div class="field"><span class="label">License Plate:</span><?= htmlspecialchars($r['license_plate'] ?? '—') ?></div>
      <div class="field"><span class="label">Make / Model:</span><?= htmlspecialchars(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? '')) ?: '—') ?></div>
      <div class="field"><span class="label">Year:</span><?= htmlspecialchars($r['year'] ?? '—') ?></div>
      <div class="field"><span class="label">Color:</span><?= htmlspecialchars($r['color'] ?? '—') ?></div>
    </div>
  </section>

  <section class="grid-two">
    <div class="card">
      <h3>Branch & Assignment</h3>
      <div class="field"><span class="label">Branch City:</span><?= htmlspecialchars($r['branch_city'] ?? '—') ?></div>
      <div class="field"><span class="label">Branch Address:</span><?= htmlspecialchars($r['branch_address'] ?? '—') ?></div>
      <div class="field"><span class="label">Branch Phone:</span><?= htmlspecialchars($r['branch_phone'] ?? '—') ?></div>
      <hr>
      <div class="field"><span class="label">Mechanic:</span><?= htmlspecialchars($r['mechanic_name'] ?? '—') ?></div>
      <div class="field"><span class="label">Mechanic Code:</span><?= htmlspecialchars($r['mechanic_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Mechanic Phone:</span><?= htmlspecialchars($r['mechanic_phone'] ?? '—') ?></div>
      <div class="field"><span class="label">Specialization:</span><?= htmlspecialchars($r['specialization'] ?? '—') ?></div>
      <div class="field"><span class="label">Experience:</span><?= $r['experience_years'] !== null ? htmlspecialchars($r['experience_years']) . ' years' : '—' ?></div>
    </div>

    <div class="card">
      <h3>Supervisor & Appointment Notes</h3>
      <div class="field"><span class="label">Supervisor:</span><?= htmlspecialchars($r['supervisor_name'] ?? '—') ?></div>
      <div class="field"><span class="label">Supervisor Code:</span><?= htmlspecialchars($r['supervisor_code'] ?? '—') ?></div>
      <div class="field"><span class="label">Supervisor Phone:</span><?= htmlspecialchars($r['supervisor_phone'] ?? '—') ?></div>
      <hr>
      <div class="field"><span class="label">Appointment Status:</span><?= htmlspecialchars($r['appointment_status']) ?></div>
      <div class="field"><span class="label">Booked At:</span><?= htmlspecialchars($r['appointment_created_at'] ?? '—') ?></div>
      <div class="field"><span class="label">Last Updated:</span><?= htmlspecialchars($r['appointment_updated_at'] ?? '—') ?></div>
      <div class="field">
        <span class="label">Appointment Notes:</span>
        <span><?= nl2br(htmlspecialchars($r['appointment_notes'] ?? '—')) ?></span>
      </div>
    </div>
  </section>

  <div class="actions">
    <a href="<?= $B ?>/admin/admin-servicehistory" class="btn btn-secondary">
      ← Back to Service History
    </a>
  </div>
</main>
</body>
</html>
