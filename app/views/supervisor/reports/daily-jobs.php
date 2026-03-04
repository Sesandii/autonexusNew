<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Daily Job Completion Report</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-report.css">
  <style>
    .report-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    .report-table th, .report-table td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    .report-table th {
      background: #f0f0f0;
    }
    .status-good { color: green; font-weight: bold; }
    .status-bad { color: red; font-weight: bold; }
  </style>
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">
  <header>
    <h1>Daily Job Completion Report</h1>
  </header>
  <div style="margin-bottom:20px;">
  <label>
    Date:
    <input type="date" id="filter-date"
           value="<?= htmlspecialchars($_GET['report_date'] ?? '') ?>">
  </label>

  <label style="margin-left:15px;">
    Mechanic:
    <select id="filter-mechanic">
      <option value="">All</option>
      <?php foreach ($mechanics as $m): ?>
        <option value="<?= htmlspecialchars($m['mechanic_code']) ?>"
          <?= ($_GET['mechanic_code'] ?? '') === $m['mechanic_code'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($m['mechanic_code']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </label>
</div>
<div class="report-table-container">
<div style="margin-bottom: 20px;">
<button onclick="window.location.href='<?= $base ?>/public/downloads/download_daily_jobs.php?report_date=<?= htmlspecialchars($_GET['report_date'] ?? '') ?>&mechanic_code=<?= htmlspecialchars($_GET['mechanic_code'] ?? '') ?>&format=pdf'">
    Download PDF
</button>

<button onclick="window.location.href='<?= $base ?>/public/downloads/download_daily_jobs.php?report_date=<?= htmlspecialchars($_GET['report_date'] ?? '') ?>&mechanic_code=<?= htmlspecialchars($_GET['mechanic_code'] ?? '') ?>&format=csv'">
    Download CSV
</button>

</div>

  <table class="report-table">
    <thead>
      <tr>
        <th>Date</th>
        <th>Mechanic</th>
        <th>Total Completed</th>
        <th>On-Time</th>
        <th>Delayed</th>
        <th>Average Completion Time (mins)</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($dailyReport)): ?>
        <?php foreach ($dailyReport as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['report_date'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['mechanic_code'] ?? '-') ?></td>
            <td><?= (int)($row['total_completed'] ?? 0) ?></td>
            <td class="status-good"><?= (int)($row['on_time'] ?? 0) ?></td>
            <td class="status-bad"><?= (int)($row['delayed_count'] ?? 0) ?></td>
            <td><?= isset($row['avg_completion_time']) ? (float)$row['avg_completion_time'] : '-' ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="5">No completed work orders found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
  </div>

  <button onclick="history.back()" class="btn secondary">Back</button>
</main>
<script>
  const dateInput = document.getElementById('filter-date');
  const mechanicSelect = document.getElementById('filter-mechanic');

  function applyFilters() {
    const params = new URLSearchParams();

    if (dateInput.value) {
      params.append('report_date', dateInput.value);
    }

    if (mechanicSelect.value) {
      params.append('mechanic_code', mechanicSelect.value);
    }

    window.location.href = '<?= $base ?>/supervisor/reports/daily-jobs?' + params.toString();
  }

  dateInput.addEventListener('change', applyFilters);
  mechanicSelect.addEventListener('change', applyFilters);
</script>

</body>
</html>
