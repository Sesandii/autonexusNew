<?php
/** @var array  $appointment */
/** @var string $pageTitle */
/** @var string $current */
$current = $current ?? 'appointments';
$B = rtrim(BASE_URL, '/');
$a = $appointment;

$dt = new DateTime($a['appointment_date'] . ' ' . $a['appointment_time']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Appointment Details') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content { margin-left:260px;padding:30px;background:#fff;min-height:100vh; }
    .grid-two { display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px; }
    .card { border:1px solid #eee;border-radius:10px;padding:16px;background:#fafafa; }
    .card h3 { margin-top:0;margin-bottom:10px; }
    .field { margin-bottom:8px;font-size:14px; }
    .label { font-weight:600;color:#555;margin-right:4px; }
    .pill { display:inline-block;padding:2px 9px;border-radius:999px;font-size:12px; }
    .pill.scheduled   { background:#e0f2fe;color:#0369a1; }
    .pill.in-progress { background:#fef9c3;color:#854d0e; }
    .pill.completed   { background:#dcfce7;color:#166534; }
    .pill.cancelled   { background:#fee2e2;color:#991b1b; }
    .actions { margin-top:20px;display:flex;gap:10px; }
    .btn { padding:8px 14px;border-radius:6px;border:none;cursor:pointer;font-size:14px; }
    .btn-primary { background:#2563eb;color:#fff; }
    .btn-secondary { background:#e5e7eb;color:#111827; }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/appointments">Appointments</a>
      <span>›</span>
      <span>Appointment #<?= htmlspecialchars((string)$a['appointment_id']) ?></span>
    </div>
  </header>

  <?php
  $statusLabel = \app\model\admin\Appointment::statusLabel($a['status']);
  $badgeClass = [
      'Scheduled'   => 'scheduled',
      'In Progress' => 'in-progress',
      'Completed'   => 'completed',
      'Cancelled'   => 'cancelled',
  ][$statusLabel] ?? 'scheduled';
  ?>

  <section class="grid-two" style="margin-top:20px;">
    <div class="card">
      <h3>Appointment Info</h3>
      <div class="field">
        <span class="label">ID:</span>
        <span>#<?= htmlspecialchars((string)$a['appointment_id']) ?></span>
      </div>
      <div class="field">
        <span class="label">Date / Time:</span>
        <span><?= $dt->format('M j, Y g:i A') ?></span>
      </div>
      <div class="field">
        <span class="label">Branch:</span>
        <span><?= htmlspecialchars($a['branch_name'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Service:</span>
        <span><?= htmlspecialchars($a['service_name'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Status:</span>
        <span class="pill <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
      </div>
      <div class="field">
        <span class="label">Booked At:</span>
        <span><?= htmlspecialchars($a['created_at'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Last Updated:</span>
        <span><?= htmlspecialchars($a['updated_at'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Notes:</span>
        <span><?= nl2br(htmlspecialchars($a['notes'] ?? '—')) ?></span>
      </div>
    </div>

    <div class="card">
      <h3>Customer & Vehicle</h3>
      <div class="field">
        <span class="label">Customer:</span>
        <span><?= htmlspecialchars($a['customer_name'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Phone:</span>
        <span><?= htmlspecialchars($a['customer_phone'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Email:</span>
        <span><?= htmlspecialchars($a['customer_email'] ?? '—') ?></span>
      </div>

      <hr>

      <div class="field">
        <span class="label">Vehicle Code:</span>
        <span><?= htmlspecialchars($a['vehicle_code'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">License Plate:</span>
        <span><?= htmlspecialchars($a['license_plate'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Make / Model:</span>
        <span><?= htmlspecialchars(trim(($a['make'] ?? '') . ' ' . ($a['model'] ?? '')) ?: '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Year:</span>
        <span><?= htmlspecialchars($a['year'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Color:</span>
        <span><?= htmlspecialchars($a['color'] ?? '—') ?></span>
      </div>
    </div>
  </section>

  <section class="grid-two" style="margin-top:20px;">
    <div class="card">
      <h3>Assignment</h3>
      <div class="field">
        <span class="label">Supervisor:</span>
        <span><?= htmlspecialchars($a['supervisor_name'] ?? 'Not set') ?></span>
      </div>
      <div class="field">
        <span class="label">Mechanic:</span>
        <span><?= htmlspecialchars($a['mechanic_name'] ?? 'Not assigned') ?></span>
      </div>
    </div>

    <div class="card">
      <h3>Work Order</h3>
      <div class="field">
        <span class="label">Work Order ID:</span>
        <span><?= htmlspecialchars($a['work_order_id'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Job Status:</span>
        <span><?= htmlspecialchars($a['work_status'] ?? 'Not created') ?></span>
      </div>
      <div class="field">
        <span class="label">Started At:</span>
        <span><?= htmlspecialchars($a['started_at'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Completed At:</span>
        <span><?= htmlspecialchars($a['completed_at'] ?? '—') ?></span>
      </div>
      <div class="field">
        <span class="label">Total Cost:</span>
        <span><?= $a['total_cost'] !== null ? number_format((float)$a['total_cost'], 2) : '—' ?></span>
      </div>
      <div class="field">
        <span class="label">Service Summary:</span>
        <span><?= nl2br(htmlspecialchars($a['service_summary'] ?? '—')) ?></span>
      </div>
    </div>
  </section>

  <div class="actions">
    <a href="<?= $B ?>/admin/admin-appointments/edit?id=<?= (int)$a['appointment_id'] ?>" class="btn btn-primary">
      <i class="fa-regular fa-pen-to-square"></i> Edit
    </a>
    <a href="<?= $B ?>/admin/appointments" class="btn btn-secondary">Back to list</a>
  </div>
</main>
</body>
</html>

