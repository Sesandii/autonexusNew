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
<div class="breadcrumb-text">
    Supervisor <span class="sep">&gt;</span> 
    Reports <span class="sep">&gt;</span> 
    Daily Job Report <span class="sep"></span> 
  </div>
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
    <div class="table-controls">
        <button class="btn-download" onclick="download('pdf')"><i class="fa-solid fa-file-pdf"></i> Download PDF</button>
        <button class="btn-download" onclick="download('csv')"><i class="fa-solid fa-file-csv"></i> Download CSV</button>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Mechanic</th>
                <th>Total Completed</th>
                <th>On-Time</th>
                <th>Delayed</th>
                <th>Avg Time (mins)</th>
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
                <tr><td colspan="6">No completed work orders found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="analytics-summary-row">
    <div class="stat-box">
        <span class="label">Avg Completion Time</span>
        <div class="value">0 mins</div>
    </div>
    <div class="stat-box">
        <span class="label">Avg Invoice Value</span>
        <div class="value">0.00</div>
    </div>
    <div class="stat-box">
        <span class="label">Avg Approval Time</span>
        <div class="value">0.0 hrs</div>
    </div>
</div>

<div class="charts-grid">
    <div class="chart-container">
        <h3>Appointment Statuses</h3>
        <canvas id="statusDonutChart"></canvas>
        <div class="mini-actions">
            <button><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button><i class="fa-solid fa-file-pdf"></i> PDF</button>
        </div>
    </div>
    <div class="chart-container">
        <h3>Appointments by Hour</h3>
        <canvas id="hourlyBarChart"></canvas>
        <div class="mini-actions">
            <button><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button><i class="fa-solid fa-file-pdf"></i> PDF</button>
        </div>
    </div>
    <div class="chart-container">
        <h3>Booking Trend</h3>
        <canvas id="bookingTrendChart"></canvas>
        <div class="mini-actions">
            <button><i class="fa-solid fa-file-csv"></i> CSV</button>
            <button><i class="fa-solid fa-file-pdf"></i> PDF</button>
        </div>
    </div>
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

  document.addEventListener('DOMContentLoaded', function() {
    // 1. Status Donut Chart
    new Chart(document.getElementById('statusDonutChart'), {
        type: 'doughnut',
        data: {
            labels: ['Confirmed', 'Requested', 'In Service'],
            datasets: [{
                data: [70, 20, 10], // Replace with PHP data
                backgroundColor: ['#3498db', '#ff6b81', '#f39c12']
            }]
        },
        options: { cutout: '70%', plugins: { legend: { position: 'top' } } }
    });

    // 2. Hourly Bar Chart
    new Chart(document.getElementById('hourlyBarChart'), {
        type: 'bar',
        data: {
            labels: ['06', '08', '09', '10', '11', '12', '14', '19'],
            datasets: [{
                label: 'Value',
                data: [2, 1, 3, 3, 2, 1, 3, 7], // Replace with PHP data
                backgroundColor: '#a2d2ff'
            }]
        }
    });

    // 3. Booking Trend Chart
    new Chart(document.getElementById('bookingTrendChart'), {
        type: 'line',
        data: {
            labels: ['2025-10', '2025-11', '2025-12', '2026-02', '2026-03', '2026-04'],
            datasets: [{
                label: 'Value',
                data: [4, 11, 1, 3, 3, 4], // Replace with PHP data
                borderColor: '#3498db',
                tension: 0.1,
                fill: false
            }]
        }
    });
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>
</html>
