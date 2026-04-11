<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Job Completion Report | AutoNexus</title>
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-report.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary: #003366; --accent: #d9534f; --bg: #f4f7f6; }
        .main-content { padding: 20px; background: var(--bg); font-family: 'Inter', sans-serif; }
        
        /* Summary Boxes */
        .analytics-summary-row { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin: 20px 0; }
        .stat-box { background: #fff; padding: 20px; border-radius: 10px; border-bottom: 4px solid var(--primary); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stat-box .label { font-size: 0.8rem; color: #64748b; font-weight: 600; text-transform: uppercase; }
        .stat-box .value { font-size: 1.5rem; font-weight: 800; color: var(--primary); margin-top: 5px; }

        /* Tables & Charts */
        .report-table-container { background: #fff; padding: 20px; border-radius: 10px; margin-bottom: 25px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .charts-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; }
        .chart-container { background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .chart-container h3 { font-size: 0.9rem; margin-bottom: 15px; color: var(--primary); border-left: 3px solid var(--accent); padding-left: 10px; }
        
        .status-good { color: #22c55e; font-weight: 700; }
        .status-bad { color: #ef4444; font-weight: 700; }
        .btn-download { background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 0.85rem; transition: 0.2s; }
        .btn-download:hover { background: #002244; }
    </style>
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">
    <div class="breadcrumb-text">Supervisor <span class="sep">&gt;</span> Reports <span class="sep">&gt;</span> Daily Job Report</div>
    
    <header style="display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
        <h1 style="color: var(--primary);">Daily Job Completion Report</h1>
        <div class="table-controls">
            <button class="btn-download" onclick="exportData('pdf')"><i class="fa-solid fa-file-pdf"></i> Download PDF</button>
            <button class="btn-download" onclick="exportData('csv')" style="background: #15803d;"><i class="fa-solid fa-file-csv"></i> CSV</button>
        </div>
    </header>

    <div style="background: #fff; padding: 15px; border-radius: 10px; margin: 20px 0; display: flex; gap: 20px; align-items: center;">
        <label><b>Date:</b> <input type="date" id="filter-date" value="<?= htmlspecialchars($selectedDate) ?>" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;"></label>
        <label><b>Mechanic:</b> 
            <select id="filter-mechanic" style="padding: 5px; border-radius: 4px; border: 1px solid #ccc;">
                <option value="">All Mechanics</option>
                <?php foreach ($mechanics as $m): ?>
                    <option value="<?= htmlspecialchars($m['mechanic_code']) ?>" <?= ($selectedMechanic === $m['mechanic_code']) ? 'selected' : '' ?>><?= htmlspecialchars($m['mechanic_code']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
    </div>

    <div class="analytics-summary-row">
        <div class="stat-box">
            <span class="label">Avg Completion Time</span>
            <div class="value"><?= (int)($summary['avg_comp'] ?? 0) ?> mins</div>
        </div>
        <div class="stat-box">
            <span class="label">Avg Invoice Value</span>
            <div class="value">LKR <?= number_format($summary['avg_invoice'] ?? 0, 2) ?></div>
        </div>
        <div class="stat-box">
            <span class="label">Avg Approval Time</span>
            <div class="value"><?= number_format($summary['avg_appr'] ?? 0, 1) ?> hrs</div>
        </div>
    </div>

    <div class="report-table-container">
        <table class="report-table" style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                    <th style="padding: 12px;">Date</th>
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
                        <tr style="border-bottom: 1px solid #f1f5f9; text-align: center;">
                            <td style="padding: 12px;"><?= htmlspecialchars($row['report_date']) ?></td>
                            <td><?= htmlspecialchars($row['mechanic_code']) ?></td>
                            <td><?= (int)$row['total_completed'] ?></td>
                            <td class="status-good"><?= (int)$row['on_time'] ?></td>
                            <td class="status-bad"><?= (int)$row['delayed_count'] ?></td>
                            <td><?= number_format($row['avg_completion_time'], 1) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="padding: 20px; text-align: center; color: #64748b;">No completed work orders found for the selected filters.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="charts-grid">
        <div class="chart-container">
            <h3>Appointment Statuses</h3>
            <canvas id="statusDonutChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>Completed Jobs by Hour</h3>
            <canvas id="hourlyBarChart"></canvas>
        </div>
        <div class="chart-container">
            <h3>7-Day Booking Trend</h3>
            <canvas id="bookingTrendChart"></canvas>
        </div>
    </div>

    <button onclick="history.back()" class="btn secondary" style="margin-top: 20px;">Back</button>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Status Donut Chart
    new Chart(document.getElementById('statusDonutChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($statusStats['labels']) ?>,
            datasets: [{
                data: <?= json_encode($statusStats['counts']) ?>,
                backgroundColor: ['#3498db', '#e74c3c', '#f1c40f', '#2ecc71']
            }]
        },
        options: { cutout: '70%', plugins: { legend: { position: 'bottom' } } }
    });

    // 2. Hourly Bar Chart
    new Chart(document.getElementById('hourlyBarChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($hourlyStats['labels']) ?>,
            datasets: [{
                label: 'Jobs Completed',
                data: <?= json_encode($hourlyStats['values']) ?>,
                backgroundColor: '#a2d2ff'
            }]
        },
        options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // 3. Booking Trend Chart
    new Chart(document.getElementById('bookingTrendChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($trendStats['dates']) ?>,
            datasets: [{
                label: 'Total Bookings',
                data: <?= json_encode($trendStats['totals']) ?>,
                borderColor: '#3498db',
                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                tension: 0.3,
                fill: true
            }]
        }
    });
});

// Filter Handlers
const dateInput = document.getElementById('filter-date');
const mechanicSelect = document.getElementById('filter-mechanic');

function applyFilters() {
    const params = new URLSearchParams();
    if (dateInput.value) params.append('report_date', dateInput.value);
    if (mechanicSelect.value) params.append('mechanic_code', mechanicSelect.value);
    window.location.href = '<?= $base ?>/supervisor/reports/daily-jobs?' + params.toString();
}

dateInput.addEventListener('change', applyFilters);
mechanicSelect.addEventListener('change', applyFilters);

function exportData(format) {
    const d = dateInput.value;
    const m = mechanicSelect.value;
    window.location.href = `<?= $base ?>/supervisor/reports/export?format=${format}&date=${d}&mechanic=${m}`;
}
</script>
</body>
</html>