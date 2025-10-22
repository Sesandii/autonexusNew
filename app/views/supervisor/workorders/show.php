<?php $base = rtrim(BASE_URL,'/'); $wo = $wo ?? []; ?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Work Order #<?= htmlspecialchars($wo['work_order_id']) ?></title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
</head>
<body>
<!-- your sidebar include here -->
 <div class="sidebar">
<div class="logo-container">
       <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
<h2>AUTONEXUS</h2>
<a href="/autonexus/supervisor/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
    was /supervisor/workloads
<a href="/autonexus/supervisor/workorders" class="nav active">
  <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
</a>

     <a href="/autonexus/supervisor/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs</a>
     <a href="/autonexus/supervisor/history"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
     <a href="/autonexus/supervisor/complaints"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
     <a href="/autonexus/supervisor/feedbacks"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
     <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
</div> 

<main class="container">
  <div class="page-header">
    <div>
      <h1>Work Order #<?= htmlspecialchars($wo['work_order_id']) ?></h1>
      <p class="subtitle"><?= htmlspecialchars(($wo['appointment_date'] ?? '') . ' ' . ($wo['appointment_time'] ?? '')) ?></p>
    </div>
    <div>
      <a class="btn" href="<?= $base ?>/supervisor/workorders">Back</a>
      <a class="btn primary" href="<?= $base ?>/supervisor/workorders/<?= (int)$wo['work_order_id'] ?>/edit">Edit</a>
    </div>
  </div>

  <div class="form-card">
    <dl class="details">
      <dt>Appointment</dt>
      <dd>#<?= htmlspecialchars($wo['appointment_id']) ?> — <?= htmlspecialchars(($wo['appointment_date'] ?? '') . ' ' . ($wo['appointment_time'] ?? '')) ?></dd>

      <dt>Service</dt>
      <dd><?= htmlspecialchars($wo['service_name'] ?? '—') ?></dd>

      <dt>Mechanic</dt>
      <dd><?= htmlspecialchars($wo['mechanic_code'] ?? 'Unassigned') ?></dd>

      <dt>Status</dt>
      <dd><span class="status <?= htmlspecialchars($wo['status']) ?>"><?= htmlspecialchars($wo['status']) ?></span></dd>

      <dt>Total Cost</dt>
      <dd>LKR <?= number_format((float)($wo['total_cost'] ?? 0), 2) ?></dd>

      <dt>Summary</dt>
      <dd><pre style="white-space:pre-wrap"><?= htmlspecialchars($wo['service_summary'] ?? '') ?></pre></dd>
    </dl>
  </div>
</main>
</body>
</html>
