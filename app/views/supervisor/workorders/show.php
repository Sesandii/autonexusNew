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
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Work Orders <span class="sep">&gt;</span> 
    #<?= htmlspecialchars($wo['work_order_id']) ?> <span class="sep">&gt;</span> 
    <span class="active-page">Show</span>
  </div>
  <div class="page-header">
    <div class="header">
      <h1>Work Order Info</h1>
      <p class="subtitle">System records for Work Order are shown below</p>
    </div>
    <div class="header-actions">
      <a class="btn secondary" href="<?= $base ?>/supervisor/workorders">Back</a>
      <a class="btn primary" href="<?= $base ?>/supervisor/workorders/<?= (int)$wo['work_order_id'] ?>/edit">Edit Details</a>
    </div>
  </div>

  <div class="tiles-container2">
    <div class="info-tile2">
      <h3>Work Order Details</h3>
      <div class="tile-content2">
        <p><strong>Service Type</strong> <span><?= htmlspecialchars($wo['service_name'] ?? '—') ?></span></p>
        <p><strong>Date Created</strong> <span><?= htmlspecialchars($wo['started_at'] ?? '—') ?></span></p>
        <p><strong>Current Status</strong> <span class="status2 <?= htmlspecialchars($wo['status']) ?>"><?= htmlspecialchars($wo['status']) ?></span></p>
        <hr style="border:0; border-top:1px solid #eee; margin:15px 0;">
        <p><strong>Work Summary</strong></p>
        <p class="summary"><?= !empty($wo['service_summary']) ? htmlspecialchars($wo['service_summary']) : 'No notes provided for this job.' ?></p>
      </div>
    </div>

    <div class="info-tile2">
      <h3>Customer & Vehicle</h3>
      <div class="tile-content2">
        <p><strong>Customer</strong> <span><?= htmlspecialchars(($wo['customer_first_name'] ?? '') . ' ' . ($wo['customer_last_name'] ?? '')) ?></span></p>
        <p><strong>License Plate</strong> <span style="font-family: monospace; background: #eee; padding: 2px 6px; border-radius: 4px;"><?= htmlspecialchars($wo['license_plate'] ?? '') ?></span></p>
        <p><strong>Model</strong> <span><?= htmlspecialchars($wo['model'] ?? '') ?></span></p>
        <p><strong>Make</strong> <span><?= htmlspecialchars($wo['make'] ?? '') ?></span></p>
        <p><strong>Color</strong> <span><?= htmlspecialchars($wo['color'] ?? '') ?></span></p>
      </div>
    </div>

    <div class="info-tile2">
      <h3>Mechanic Assignment</h3>
      <div class="tile-content2">
        <p><strong>Assigned To</strong> <span><?= (!empty($wo['mechanic_first_name'])) ? htmlspecialchars($wo['mechanic_first_name'] . ' ' . $wo['mechanic_last_name']) : '<em>Unassigned</em>' ?></span></p>
        <p><strong>Mechanic Code</strong> <span><?= htmlspecialchars($wo['mechanic_code'] ?? 'N/A') ?></span></p>
        <p><strong>Work Started</strong> <span><?= htmlspecialchars($wo['job_start_time'] ?? 'Pending') ?></span></p>
        <p><strong>Work Finished</strong> <span><?= htmlspecialchars($wo['completed_at'] ?? '-') ?></span></p>
      </div>
    </div>
  </div>
</main>
</body>
</html>
