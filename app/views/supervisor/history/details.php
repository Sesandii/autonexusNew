<?php $base = rtrim(BASE_URL, '/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vehicle Service Details - <?= htmlspecialchars($details['license_plate']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-history.css">
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="main-content">
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Vehicle History <span class="sep">&gt;</span> 
    <?= htmlspecialchars($details['license_plate']) ?> <span class="sep">&gt;</span> 
    <span class="active-page">Details</span>
  </div>
<header class="page-header">
  <h1>Vehicle Service Details</h1>
</header>
  <div class="details-wrapper">
    
  <div class="card info-card">
    <h3 class="card-title">Vehicle Information</h3>
    <p><b>License Plate:</b> <?= htmlspecialchars($details['license_plate']) ?></p>
    <p><b>Make & Model:</b> <?= htmlspecialchars($details['make'] . ' ' . $details['model']) ?></p>
    <p><b>Year:</b> <?= htmlspecialchars($details['year'] ?? '—') ?></p>
    <p><b>Color:</b> <?= htmlspecialchars($details['color'] ?? '—') ?></p>
    <p><b>Status:</b> <span class="status"><?= htmlspecialchars($details['vehicle_status']) ?></span></p>
  </div>

  <div class="card info-card">
    <h3 class="card-title">Appointment Information</h3>
    <p><b>Date:</b> <?= htmlspecialchars($details['appointment_date']) ?></p>
    <p><b>Time:</b> <?= htmlspecialchars($details['appointment_time']) ?></p>
    <p><b>Status:</b> <span class="status"><?= htmlspecialchars($details['appointment_status']) ?></span></p>
    <p><b>Notes:</b> <?= htmlspecialchars($details['notes'] ?? 'None') ?></p>
  </div>

  <div class="card info-card">
    <h3 class="card-title">Service Information</h3>
    <p><b>Service Name:</b> <?= htmlspecialchars($details['service_name']) ?></p>
    <p><b>Description:</b> <?= htmlspecialchars($details['service_description'] ?? '—') ?></p>
    <p><b>Base Price:</b> <span class="price">Rs. <?= htmlspecialchars($details['default_price']) ?></span></p>
  </div>

  <div class="card info-card">
    <h3 class="card-title">Work Order Information</h3>
    <p><b>Mechanic ID:</b> <?= htmlspecialchars($details['mechanic_code'] ?? '—') ?></p>
    <p><b>Summary:</b> <?= htmlspecialchars($details['service_summary'] ?? '—') ?></p>
    <p><b>Total Cost:</b> <span class="price">Rs. <?= htmlspecialchars($details['total_cost'] ?? '0') ?></span></p>
    <p><b>Status:</b> <span class="status"><?= htmlspecialchars($details['work_order_status'] ?? '—') ?></span></p>
    <p><b>Created:</b> <?= htmlspecialchars($details['started_at'] ?? '—') ?></p>
    <p><b>Started:</b> <?= htmlspecialchars($details['job_start_time'] ?? '—') ?></p>
    <p><b>Completed:</b> <?= htmlspecialchars($details['completed_at'] ?? '—') ?></p>
  </div>

</div>

<a onclick="history.back()" class="btn">Back</a>

</div>

</body>
</html>
