<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Report Generation</title>

  <!-- Remembered sidebar CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">
  <!-- Page CSS (changed from sm_css to css/manager) -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/reports.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <main class="main">
    <header>
      <h1>Report Generation</h1>
    </header>

    <!-- Report Type Selection -->
    <section class="card report-types">
      <div class="card-header">
        <h3>Select Report Type</h3>
        <div class="filter">
          <span>🔽</span> All Categories
        </div>
      </div>

      <div class="report-options">
        <div class="report-box">
          <span class="icon">📊</span>
          <h4>Service Frequency</h4>
          <p>Most common services performed over time</p>
        </div>
        <div class="report-box">
          <span class="icon">📝</span>
          <h4>Customer Feedback Summary</h4>
          <p>Overview of customer satisfaction ratings</p>
        </div>
        <div class="report-box">
          <span class="icon">⏱️</span>
          <h4>Service Completion Times</h4>
          <p>Average time to complete different service types</p>
        </div>
        <div class="report-box">
          <span class="icon">📉</span>
          <h4>Mechanic Performance</h4>
          <p>Productivity and efficiency metrics by mechanic</p>
        </div>
        <div class="report-box active">
          <span class="icon">👥</span>
          <h4>Customer Retention</h4>
          <p>Analysis of repeat customers and retention rates</p>
        </div>
      </div>
    </section>

    <!-- Report Parameters -->
    <section class="card report-params">
      <h3>Report Parameters</h3>
      <div class="date-range">
        <label>Date Range</label>
        <input type="date" value="2025-06-29"> 
        <span class="to">to</span>
        <input type="date" value="2025-07-29">
      </div>
      <div class="actions">
        <button class="cancel">Cancel</button>
        <button class="generate">📑 Generate Report</button>
      </div>
    </section>
  </main>
</body>
</html>
