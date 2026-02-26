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
<header>
    <h1>Mechanic Report</h1>
</header>

<div class="report-table-container">
  <table class="report-table">
    <thead>
      <tr>
        <th>Mechanic</th>
        <th>Mechanic Code</th>
        <th>Total Assigned</th>
        <th>Completed</th>
        <th>In Progress</th>
        <th>Open</th>
        <th>Average Duration (mins)</th>
      </tr>
    </thead>
    <tbody>
      <?php if (!empty($activity)): ?>
        <?php foreach ($activity as $row): ?>
          <tr>
            <td><?= htmlspecialchars($row['mechanic_name'] ?? '-') ?></td>
            <td><?= htmlspecialchars($row['mechanic_code'] ?? '-') ?></td>
            <td><?= (int)($row['total_assigned'] ?? 0) ?></td>
            <td class="status-good"><?= (int)($row['completed'] ?? 0) ?></td>
            <td class="status-bad"><?= (int)($row['in_progress'] ?? 0) ?></td>
            <td><?= (int)($row['open'] ?? 0) ?></td>
            <td><?= isset($row['avg_duration_mins']) ? (float)$row['avg_duration_mins'] : '-' ?></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr><td colspan="6">No jobs found for selected filters.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<button onclick="history.back()" class="btn secondary">Back</button>
<script>
  const dateInput = document.getElementById('filter-date');
  const mechanicSelect = document.getElementById('filter-mechanic');

  function applyFilters() {
    const params = new URLSearchParams();
    if (dateInput.value) params.append('date', dateInput.value);
    if (mechanicSelect.value) params.append('mechanic_code', mechanicSelect.value);
    window.location.href = '<?= $base ?>/supervisor/reports/technician-activity?' + params.toString();
  }

  dateInput.addEventListener('change', applyFilters);
  mechanicSelect.addEventListener('change', applyFilters);
</script>

</main>
</body>
</html>
