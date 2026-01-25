<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Service Dashboard</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-view.css">
</head>

<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
  <div class="main">
    <header>
      <input type="text" placeholder="Search..." class="search">
    </header>

    <div class="section-title job-header">
      Job Details - JOB-<?= $job['work_order_id'] ?>
    </div>

    <!-- Job Information -->
    <div class="card">
      <div class="section-title">Job Information</div>

      <div class="info-row">
        <span class="label">Job Title:</span>
        <span><?= htmlspecialchars($job['service_summary']) ?></span>
      </div>

      <div class="info-row">
        <span class="label">Status:</span>
        <span class="status"><?= htmlspecialchars($job['status']) ?></span>
      </div>

      <!-- 🔥 SHOW STATUS FORM ONLY IF MECHANIC OWNS THIS JOB -->
      <?php if (!empty($can_edit) && $can_edit === true): ?>
        <form method="POST" action="<?= $base ?>/mechanic/jobs/update-status" style="margin-top: 10px;">
          <input type="hidden" name="work_order_id" value="<?= $job['work_order_id'] ?>">

          <div class="info-row">
            <span class="label">Change Status:</span>
            <select name="status" class="status-dropdown">
              <option value="open" <?= $job['status'] === 'open' ? 'selected' : '' ?>>Open</option>
              <option value="in_progress" <?= $job['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
              <option value="completed" <?= $job['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
            </select>
            <button style="margin-top: 8px;" class="btn btn-primary">Update Status</button>
          </div>

          
        </form>
      <?php else: ?>
        <!-- VIEW ONLY MODE (OTHER MECHANIC'S JOB) -->
        <div class="info-row">
          <span class="label">Change Status:</span>
          <span style="opacity: 0.6;">You cannot modify this job</span>
        </div>
      <?php endif; ?>
      <!-- END STATUS FORM -->

      <div class="info-row">
        <span class="label">Date:</span>
        <span><?= htmlspecialchars($job['appointment_date']) ?></span>
      </div>

      <div class="info-row">
        <span class="label">Time:</span>
        <span><?= htmlspecialchars($job['appointment_time']) ?></span>
      </div>
    </div>

    <div class="card">
      <div class="section-title">Customer Information</div>
      <span class="label">Customer: </span><span><?= htmlspecialchars($job['first_name'] . " " . $job['last_name']) ?></span>
      
    </div>

    <!-- Vehicle Information -->
    <div class="card">
      <div class="section-title">Vehicle Information</div>
      

      <div class="info-row"><span class="label">Make:</span> <span><?= $job['make'] ?></span></div>
      <div class="info-row"><span class="label">Model:</span> <span><?= $job['model'] ?></span></div>
      <div class="info-row"><span class="label">Year:</span> <span><?= $job['year'] ?></span></div>
      <div class="info-row"><span class="label">License:</span> <span><?= $job['license_plate'] ?></span></div>
      <div class="info-row"><span class="label">Mileage:</span> <span><?= $job['mileage'] ?></span></div>
      <div class="info-row"><span class="label">Color:</span> <span><?= $job['color'] ?></span></div>
      <div class="info-row"><span class="label">VIN:</span> <span><?= $job['vin'] ?></span></div>
    </div>

  </div>
</body>
</html>
