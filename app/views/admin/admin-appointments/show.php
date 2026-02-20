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
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/appointments/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>
<main class="main-content appointments-main">
  <header class="page-header">
    <div class="page-breadcrumb">
      <a href="<?= $B ?>/admin/admin-appointments">Appointments</a>
      <span>›</span>
      <span>Appointment #<?= htmlspecialchars((string)$a['appointment_id']) ?></span>
    </div>
    <?php
    $statusLabel = \app\model\admin\Appointment::statusLabel($a['status']);
    $badgeClass = [
        'Scheduled'   => 'status-pill--scheduled',
        'In Progress' => 'status-pill--progress',
        'Completed'   => 'status-pill--completed',
        'Cancelled'   => 'status-pill--cancelled',
    ][$statusLabel] ?? 'status-pill--scheduled';
    ?>
  </header>
  <section class="appt-details-grid">
    <article class="appt-details-card">
      <h3>Appointment Info</h3>
      <p class="field">
        <span class="label">ID:</span>
        <span>#<?= htmlspecialchars((string)$a['appointment_id']) ?></span>
      </p>
      <p class="field">
        <span class="label">Date / Time:</span>
        <span><?= $dt->format('M j, Y g:i A') ?></span>
      </p>
      <p class="field">
        <span class="label">Branch:</span>
        <span><?= htmlspecialchars($a['branch_name'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Service:</span>
        <span><?= htmlspecialchars($a['service_name'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Status:</span>
        <span class="status-pill <?= $badgeClass ?>"><?= htmlspecialchars($statusLabel) ?></span>
      </p>
      <p class="field">
        <span class="label">Booked At:</span>
        <span><?= htmlspecialchars($a['created_at'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Last Updated:</span>
        <span><?= htmlspecialchars($a['updated_at'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Notes:</span>
        <span><?= nl2br(htmlspecialchars($a['notes'] ?? '—')) ?></span>
      </p>
    </article>
    <article class="appt-details-card">
      <h3>Customer & Vehicle</h3>
      <p class="field">
        <span class="label">Customer:</span>
        <span><?= htmlspecialchars($a['customer_name'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Phone:</span>
        <span><?= htmlspecialchars($a['customer_phone'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Email:</span>
        <span><?= htmlspecialchars($a['customer_email'] ?? '—') ?></span>
      </p>
      <hr>
      <p class="field">
        <span class="label">Vehicle Code:</span>
        <span><?= htmlspecialchars($a['vehicle_code'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">License Plate:</span>
        <span><?= htmlspecialchars($a['license_plate'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Make / Model:</span>
        <span><?= htmlspecialchars(trim(($a['make'] ?? '') . ' ' . ($a['model'] ?? '')) ?: '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Year:</span>
        <span><?= htmlspecialchars($a['year'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Color:</span>
        <span><?= htmlspecialchars($a['color'] ?? '—') ?></span>
      </p>
    </article>
  </section>
  <section class="appt-details-grid" style="margin-top:18px;">
    <article class="appt-details-card">
      <h3>Assignment</h3>
      <p class="field">
        <span class="label">Supervisor:</span>
        <span><?= htmlspecialchars($a['supervisor_name'] ?? 'Not set') ?></span>
      </p>
      <p class="field">
        <span class="label">Mechanic:</span>
        <span><?= htmlspecialchars($a['mechanic_name'] ?? 'Not assigned') ?></span>
      </p>
    </article>
    <article class="appt-details-card">
      <h3>Work Order</h3>
      <p class="field">
        <span class="label">Work Order ID:</span>
        <span><?= htmlspecialchars($a['work_order_id'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Job Status:</span>
        <span><?= htmlspecialchars($a['work_status'] ?? 'Not created') ?></span>
      </p>
      <p class="field">
        <span class="label">Started At:</span>
        <span><?= htmlspecialchars($a['started_at'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Completed At:</span>
        <span><?= htmlspecialchars($a['completed_at'] ?? '—') ?></span>
      </p>
      <p class="field">
        <span class="label">Total Cost:</span>
        <span><?= $a['total_cost'] !== null ? number_format((float)$a['total_cost'], 2) : '—' ?></span>
      </p>
      <p class="field">
        <span class="label">Service Summary:</span>
        <span><?= nl2br(htmlspecialchars($a['service_summary'] ?? '—')) ?></span>
      </p>
    </article>
  </section>
  <div class="appt-detail-actions">
    <a href="<?= $B ?>/admin/admin-appointments/edit?id=<?= (int)$a['appointment_id'] ?>" class="btn-primary">
      <i class="fa-regular fa-pen-to-square"></i>
      <span>Edit</span>
    </a>
    <a href="<?= $B ?>/admin/admin-appointments" class="btn-secondary">
      Back to list
    </a>
  </div>
</main>
</body>
</html>
