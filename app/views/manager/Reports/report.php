<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/report.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<main class="main">
  <header>
    <h1>Report Generation</h1>
  </header>

  <!-- Report Type Selection -->
  <section class="card report-types">
    <div class="report-options">

      <!-- IMPORTANT: data-report attribute -->
      <div class="report-box" data-report="Revenue & Sales Report">
        <span class="icon">📝</span>
        <h4>Revenue & Sales Report</h4>
        <p>Summary of service revenue, invoice totals, and payment trends within the selected period</p>
      </div>

      <div class="report-box" data-report="Service Completion Times">
        <span class="icon">⏱️</span>
        <h4>Service Completion Times</h4>
        <p>Average time to complete different service types</p>
      </div>

      <div class="report-box" data-report="Pending & Overdue Services">
        <span class="icon">📊</span>
        <h4>Pending & Overdue Services</h4>
        <p>List of ongoing, delayed, and overdue service jobs requiring management attention</p>
      </div>

    <!--  <div class="report-box" data-report="Customer Feedback Summary">
        <span class="icon">👥</span>
        <h4>Customer Feedback Summary</h4>
        <p>Overview of customer satisfaction ratings</p>
      </div>-->

      <!--
      <div class="report-box" data-report="Service Frequency">
        <span class="icon">📉</span>
        <h4>Service Frequency</h4>
        <p>Most common services performed over time</p>
      </div>

      <div class="report-box" data-report="Vehicle Service History">
        <span class="icon">🚗</span>
        <h4>Vehicle Service History</h4>
        <p>Complete service history of vehicles, including previous services, costs, and dates</p>
      </div>

      <div class="report-box" data-report="Technician Performance">
        <span class="icon">📉</span>
        <h4>Technician Performance</h4>
        <p>Productivity and efficiency metrics by technician</p>
      </div>

      <div class="report-box" data-report="Appointment & Workload Report">
        <span class="icon">📝</span>
        <h4>Appointment & Workload Report</h4>
        <p>Overview of scheduled appointments, walk-ins, and daily workshop workload</p>
      </div> -->

    </div>
  </section>

 <!-- Report Parameters -->
<section class="card report-params">
  <h3>Report Parameters</h3>

  <!-- Form for generating report -->
  <form id="report-form" method="POST" action="<?= BASE_URL ?>/manager/reports/result">
    <p class="form-hint">
      Select a report to configure generation options
    </p>

    <!-- Dynamic filters + date range inputs will still be injected here via JS if needed -->

    <div class="actions">
      <button type="reset" class="cancel">Cancel</button>
      <button type="submit" class="generate">
        📑 Generate Report
      </button>
    </div>
  </form>
</section>



</main>

<script>
  window.BASE_URL = '<?= BASE_URL ?>';
</script>
<script src="<?= BASE_URL ?>/public/assets/js/manager/report.js"></script>


</body>
</html>
