<?php $base = rtrim(BASE_URL,'/'); $wo = $wo ?? []; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Work Order <?= htmlspecialchars($wo['work_order_id']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-workorders.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="container">
  <div class="page-header">
    <div class = "header">
      <h1>Work Order Info</h1>
      <p class="subtitle">Details showed related to the workorder.</p>
    </div>
    <div>
      <a class="btn" href="<?= $base ?>/supervisor/workorders">Back</a>
      <a class="btn primary" href="<?= $base ?>/supervisor/workorders/<?= (int)$wo['work_order_id'] ?>/edit">Edit</a>
    </div>
  </div>

<<<<<<< HEAD
  <div class="form-card">
    <dl class="details">

    <dt>Workorder ID</dt>
    <dd><?= htmlspecialchars($wo['work_order_id']) ?></dd>

      <dt>Appointment ID</dt>
      <dd><?= htmlspecialchars($wo['appointment_id']) ?></dd>
      
      <dt>Appointment</dt>
      <dd><?= htmlspecialchars(($wo['appointment_date'] ?? '') . ' ' . ($wo['appointment_time'] ?? '')) ?></dd>

      <dt>Customer Code</dt>
      <dd><?= htmlspecialchars($wo['customer_code'] ?? 'CUST000') ?></dd>

      <dt>Customer</dt>
      <dd><?= htmlspecialchars($wo['customer_first_name'] ?? '') . " " . htmlspecialchars($wo['customer_last_name'] ?? '') ?></dd>

      <dt>License Plate</dt>
      <dd><?= htmlspecialchars($wo['license_plate'] ?? '') ?></dd>

      <dt>Vehicle</dt>
      <dd><?= htmlspecialchars($wo['make'] ?? '') . " " . htmlspecialchars($wo['model'] ?? '')?></dd>

      <dt>Service</dt>
      <dd><?= htmlspecialchars($wo['service_name'] ?? '—') ?></dd>

      <dt>Created at</dt>
      <dd><?= htmlspecialchars($wo['started_at'] ?? '—') ?></dd>

      <dt>Started at</dt>
      <dd><?= htmlspecialchars($wo['job_start_time'] ?? '') ?></dd>

      <dt>Mechanic Code</dt>
      <dd><?= htmlspecialchars($wo['mechanic_code'] ?? '')?></dd>

      <dt>Mechanic</dt>
<dd>
  <?php 
    if (!empty($wo['mechanic_first_name']) || !empty($wo['mechanic_last_name'])) {
        echo htmlspecialchars($wo['mechanic_first_name'] . ' ' . $wo['mechanic_last_name']);
    } else {
        echo 'Unassigned';
    }
  ?>
</dd>


      <dt>Status</dt>
      <dd><span class="status <?= htmlspecialchars($wo['status']) ?>"><?= htmlspecialchars($wo['status']) ?></span></dd>

      <dt>Actual Completion</dt>
      <dd><?= htmlspecialchars($wo['completed_at'] ?? '—') ?></dd>

      <dt>Summary</dt>
      <dd><pre style="white-space:pre-wrap"><?= htmlspecialchars($wo['service_summary'] ?? '') ?></pre></dd>

    </dl>
  </div>
=======
  <div class="tiles-container">

  <!-- WORK ORDER TILE -->
  <div class="info-tile">
    <h3>Work Order</h3>
    <div class="tile-content">
      <p><strong>ID:</strong> <?= htmlspecialchars($wo['work_order_id']) ?></p>
      <p><strong>Service:</strong> <?= htmlspecialchars($wo['service_name'] ?? '—') ?></p>
      <p><strong>Created At:</strong> <?= htmlspecialchars($wo['started_at'] ?? '—') ?></p>
      <p><strong>Status:</strong><span class="status <?= htmlspecialchars($wo['status']) ?>"><?= htmlspecialchars($wo['status']) ?></span></p>
      <p><strong>Summary:</strong></p>
      <p class="summary"><?= htmlspecialchars($wo['service_summary'] ?? '') ?></p>
    </div>
  </div>

  <!-- APPOINTMENT TILE -->
  <div class="info-tile">
    <h3>Appointment</h3>
    <div class="tile-content">
      <p><strong>ID:</strong> <?= htmlspecialchars($wo['appointment_id']) ?></p>
      <p><strong>Date & Time:</strong>
        <?= htmlspecialchars(($wo['appointment_date'] ?? '') . ' ' . ($wo['appointment_time'] ?? '')) ?>
      </p>
    </div>
  </div>

  <!-- CUSTOMER TILE -->
  <div class="info-tile">
    <h3>Customer</h3>
    <div class="tile-content">
      <p><strong>Code:</strong> <?= htmlspecialchars($wo['customer_code'] ?? 'CUST000') ?></p>
      <p><strong>Name:</strong>
        <?= htmlspecialchars(($wo['customer_first_name'] ?? '') . ' ' . ($wo['customer_last_name'] ?? '')) ?>
      </p>
    </div>
  </div>
  </div>
  <div class="tiles-container second-row">
  <!-- VEHICLE TILE -->
  <div class="info-tile">
    <h3>Vehicle</h3>
    <div class="tile-content">
      <p><strong>License Plate:</strong> <?= htmlspecialchars($wo['license_plate'] ?? '') ?></p>
      <p><strong>Model:</strong>
        <?= htmlspecialchars(($wo['make'] ?? '') . ' ' . ($wo['model'] ?? '')) ?>
      </p>
    </div>
  </div>

  <!-- MECHANIC TILE -->
  <div class="info-tile">
    <h3>Mechanic</h3>
    <div class="tile-content">
      <p><strong>Code:</strong> <?= htmlspecialchars($wo['mechanic_code'] ?? '') ?></p>
      <p><strong>Name:</strong>
        <?php 
          if (!empty($wo['mechanic_first_name']) || !empty($wo['mechanic_last_name'])) {
              echo htmlspecialchars($wo['mechanic_first_name'] . ' ' . $wo['mechanic_last_name']);
          } else {
              echo 'Unassigned';
          }
        ?>
      </p>
      <p><strong>Started At:</strong> <?= htmlspecialchars($wo['job_start_time'] ?? '—') ?></p>
      <p><strong>Completed At:</strong> <?= htmlspecialchars($wo['completed_at'] ?? '—') ?></p>
    </div>
  </div>

</div>

>>>>>>> bc21bfd776db2147cd644a47aeb727bb8ca3d276
</main>
</body>
</html>
