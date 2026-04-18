<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/individual.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<title><?= $mechanic['first_name'] ?> Performance</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

  <header class="top-header">
    <h1><?= $mechanic['first_name'] . ' ' . $mechanic['last_name'] ?> Performance</h1>
  </header>

  <section class="profile-section">
    <div class="profile-card">
      <div class="profile-left">
        <div class="avatar"></div>
        <div class="info">
          <h3><?= $mechanic['first_name'] ?> <?= $mechanic['last_name'] ?></h3>
          <p class="title"><?= $mechanic['specialization'] ?></p>
        </div>
      </div>

      <div class="date-range">
        Month
        <input type="month" id="month-picker" value="<?= $month ?>">
      </div>
    </div>
  </section>

  <div class="stats">
    <div class="stat-card">Completed Jobs<br><strong><?= $stats['completed_jobs'] ?></strong></div>
    <div class="stat-card">Customer Satisfaction<br><strong><?= $stats['customer_satisfaction'] ?? 'N/A' ?></strong></div>
    <div class="stat-card">Avg. Service Time<br><strong><?= $stats['avg_service_time'] ?> min</strong></div>
    <div class="stat-card">Return Rate<br><strong><?= $stats['return_rate'] ?>%</strong></div>
  </div>

  <section class="charts">
    <canvas id="jobsByDayChart"></canvas>
  </section>

</div>

<!-- ✅ PASS PHP DATA TO JS -->
<script>
  window.mechanicChartData = {
    jobsByDay: <?= json_encode($jobsByDay) ?>,
    month: "<?= $month ?>"
  };
</script>

<script>
document.getElementById('month-picker').addEventListener('change', function () {
  const month = this.value;
  const mechanicId = <?= (int)$mechanic['mechanic_id'] ?>;
  window.location.href =
    `<?= BASE_URL ?>/manager/performance/viewMechanic?id=${mechanicId}&month=${month}`;
});
</script>


<script src="<?= BASE_URL ?>/public/assets/js/manager/mechanicPerformanceChart.js"></script>

</body>
</html>
