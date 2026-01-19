<?php $base = rtrim(BASE_URL, '/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vehicle Service Details - <?= htmlspecialchars($details['license_plate']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-history.css">
</head>
<body>

<div class="sidebar">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>

  <h2>AUTONEXUS</h2>
  <a href="/autonexus/supervisor/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="/autonexus/supervisor/workorders"><img src="/autonexus/public/assets/img/jobs.png"/>Work Orders</a>
  <a href="/autonexus/supervisor/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned</a>
  <a href="/autonexus/supervisor/history" class="nav"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
  <a href="/autonexus/supervisor/complaints"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
  <a href="/autonexus/supervisor/feedbacks"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
  <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
</div>

<main class="container">
  <h1>Vehicle Service Details</h1>

  <div class="details-wrapper">

  <!-- Vehicle Info -->
  <div class="card info-card">
    <h3 class="card-title">Vehicle Information</h3>
    <p><b>License Plate:</b> <?= htmlspecialchars($details['license_plate']) ?></p>
    <p><b>Make & Model:</b> <?= htmlspecialchars($details['make'] . ' ' . $details['model']) ?></p>
    <p><b>Year:</b> <?= htmlspecialchars($details['year'] ?? '—') ?></p>
    <p><b>Color:</b> <?= htmlspecialchars($details['color'] ?? '—') ?></p>
    <p><b>Status:</b> <span class="status"><?= htmlspecialchars($details['vehicle_status']) ?></span></p>
  </div>

  <!-- Appointment Info -->
  <div class="card info-card">
    <h3 class="card-title">Appointment Information</h3>
    <p><b>Date:</b> <?= htmlspecialchars($details['appointment_date']) ?> at <?= htmlspecialchars($details['appointment_time']) ?></p>
    <p><b>Status:</b> <span class="status"><?= htmlspecialchars($details['appointment_status']) ?></span></p>
    <p><b>Notes:</b> <?= htmlspecialchars($details['notes'] ?? 'None') ?></p>
  </div>

  <!-- Service Info -->
  <div class="card info-card">
    <h3 class="card-title">Service Information</h3>
    <p><b>Service Name:</b> <?= htmlspecialchars($details['service_name']) ?></p>
    <p><b>Description:</b> <?= htmlspecialchars($details['service_description'] ?? '—') ?></p>
    <p><b>Base Price:</b> <span class="price">Rs. <?= htmlspecialchars($details['default_price']) ?></span></p>
  </div>

  <!-- Work Order Info -->
  <div class="card info-card">
    <h3 class="card-title">Work Order Information</h3>
    <p><b>Mechanic ID:</b> <?= htmlspecialchars($details['mechanic_id'] ?? '—') ?></p>
    <p><b>Summary:</b> <?= htmlspecialchars($details['service_summary'] ?? '—') ?></p>
    <p><b>Total Cost:</b> <span class="price">Rs. <?= htmlspecialchars($details['total_cost'] ?? '0') ?></span></p>
    <p><b>Status:</b> <span class="status"><?= htmlspecialchars($details['work_order_status'] ?? '—') ?></span></p>
    <p><b>Started:</b> <?= htmlspecialchars($details['started_at'] ?? '—') ?></p>
    <p><b>Completed:</b> <?= htmlspecialchars($details['completed_at'] ?? '—') ?></p>
  </div>

</div>

<button onclick="history.back()" class="back-btn">← Back</button>

</main>

</body>
</html>
