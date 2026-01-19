<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Auto Shop Dashboard</title>
<link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-jobs.css"/>
</head>
<body>

<div class="sidebar">
  <div class="logo-container">
    <img src="<?= $base ?>/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>
  <h2>AUTONEXUS</h2>
  <a href="<?= $base ?>/mechanic/dashboard"><img src="<?= $base ?>/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="<?= $base ?>/mechanic/jobs" class="nav"><img src="<?= $base ?>/public/assets/img/jobs.png"/>Jobs</a>
  <a href="<?= $base ?>/mechanic/assignedjobs"><img src="<?= $base ?>/public/assets/img/assigned.png"/>Assigned Jobs</a>
  <a href="<?= $base ?>/mechanic/history"><img src="<?= $base ?>/public/assets/img/history.png"/>Vehicle History</a>
</div>

<main class="main-content">
<header>
  <input type="text" placeholder="Search..." class="search" />
</header>

<section class="job-section">
  <p>Overview of all ongoing jobs</p>
  <h2>Ongoing Jobs</h2>

  <div class="job-grid">
  <?php foreach ($allJobs as $job): ?>
    <div class="job-card">

      <h3 class="job-title"><?= htmlspecialchars($job['service_summary']) ?></h3>

      <div class="job-info">
        <span>Customer</span>
        <?= htmlspecialchars($job['first_name'] . ' ' . $job['last_name']) ?>
      </div>

      <div class="job-info">
        <span>Vehicle</span>
        <?= htmlspecialchars($job['make'] . ' ' . $job['model']) ?>
      </div>

      <div class="job-info">
        <span>ETA</span>
        <?= htmlspecialchars($job['appointment_date'] . ' ' . $job['appointment_time']) ?>
      </div>

      <div class="job-info">
        <span>Mechanic</span>
        <?= htmlspecialchars($job['mechanic_code'] ?? 'Unassigned') ?>
      </div>

      <div class="job-info status">
        <span>Status</span>
        <?= htmlspecialchars($job['status']) ?>
      </div>

      <div class="job-actions">
        <a href="<?= $base ?>/mechanic/jobs/view/<?= $job['work_order_id'] ?>" class="view-btn">
          View Details →
        </a>
      </div>

    </div>
  <?php endforeach; ?>
</div>


</section>
</main>

</body>
</html>
