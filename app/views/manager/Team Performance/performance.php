<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $pageTitle ?? 'Team Performance' ?></title>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/performance.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
  <header>
    <h1>Team Performance</h1>
  </header>

  <!-- =====================
       STATS TILES
       ===================== -->
  <div class="stats-grid">
    <div class="card">
      <h3>Completed Jobs</h3>
      <p class="value" id="completed-jobs">0</p>
    </div>

    <div class="card">
      <h3>Customer Satisfaction</h3>
      <p class="value" id="customer-satisfaction">N/A</p>
    </div>

    <div class="card">
      <h3>Avg. Service Time</h3>
      <p class="value" id="avg-service-time">0 min</p>
    </div>

    <div class="card">
      <h3>Return Rate</h3>
      <p class="value" id="return-rate">0%</p>
    </div>

    <div class="card revenue">
      <h3>Revenue</h3>
      <p class="value" id="revenue">$0.00</p>
    </div>
  </div>

<div class="chart-table-container">
  <div class="chart">
    <h3>Jobs Completed by Day</h3>
    <canvas id="jobsByDayChart" width="600" height="400"></canvas>
  </div>

<div class="team-table">
    <h3>Team Member Performance</h3>
    <table >
        <thead>
            <tr>
                <th>Team Member</th>
                <th>Specialization</th>
                <th>Completed Jobs</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($mechanics)): ?>
                <?php foreach ($mechanics as $m): ?>
 <tr class="clickable-row" data-url="<?= BASE_URL ?>/manager/performance/viewMechanic?id=<?= (int)$m['mechanic_id'] ?>">
    <td><?= htmlspecialchars($m['first_name'] . ' ' . $m['last_name'] ?? '-') ?></td>
    <td><?= htmlspecialchars($m['specialization'] ?? '-') ?></td>
    <td><?= htmlspecialchars($m['completed_jobs'] ?? 0) ?></td>
</tr>

                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5">No mechanics found</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>




</div>



<script>
  const BASE_URL = '<?= $this->baseUrl() ?>';
</script>
<script src="<?= BASE_URL ?>/public/assets/js/manager/performance.js"></script>
<script src="<?= BASE_URL ?>/public/assets/js/manager/performanceChart.js"></script>

</body>
</html>
