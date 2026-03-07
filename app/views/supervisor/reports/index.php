<?php $base = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Daily Report | AutoNexus</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-report.css"/>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">

<h1>Reports</h1>

<div class="report-grid">

  <a href="<?= BASE_URL ?>/supervisor/reports/indexp" class="report-tile">
    <h3>Vehicle Service Report</h3>
    <p>Completed vehicle services summary</p>
  </a>

  <a href="<?= BASE_URL ?>/supervisor/reports/daily-jobs" class="report-tile">
  <h3>Daily Job Completion</h3>
  <p>Shows date, completed count, and timing performance</p>
</a>

  <a href="<?= BASE_URL ?>/supervisor/reports/mechanic-activity" class="report-tile">
    <h3>Mechanic Activity</h3>
    <p>Jobs handled by each mechanic</p>
  </a>

  <a href="<?= BASE_URL ?>/supervisor/reports/pending-jobs" class="report-tile">
    <h3>Pending Jobs</h3>
    <p>Open and in-progress jobs</p>
  </a>

</div>
</main>
</body>
</html>
