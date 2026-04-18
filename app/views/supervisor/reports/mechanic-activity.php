<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Mechanic Performance | AutoNexus</title>
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-report.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --primary: #000000; --accent: #d9534f; --bg: #f8fafc; }
        body { background-color: var(--bg); font-family: 'Inter', sans-serif; }
        .main-content { padding: 25px; }
        
        .analytics-summary-row { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 25px; }
        .stat-box { background: #fff; padding: 20px; border-radius: 10px; border-bottom: 4px solid var(--primary); box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
        .stat-box .label { font-size: 0.75rem; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-box .value { font-size: 1.4rem; font-weight: 800; color: var(--primary); margin-top: 5px; }

        .btn-download { background: var(--primary); color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: 0.2s; }
        .btn-download:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-csv { background: #15803d; }

        .charts-container { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-top: 25px; }
        .chart-card { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .chart-card h3 { font-size: 0.95rem; margin-bottom: 20px; color: var(--primary); border-left: 4px solid var(--accent); padding-left: 12px; }

        .report-table-container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 25px; }
        .report-table { width: 100%; border-collapse: collapse; }
        .report-table th { background: #f8fafc; color: var(--primary); text-align: left; padding: 15px; font-size: 0.85rem; border-bottom: 2px solid #edf2f7; }
        .report-table td { padding: 14px 15px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; }
        .status-good { color: #10b981; font-weight: 700; }
        .status-bad { color: #f43f5e; font-weight: 700; }
    </style>
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<main class="main-content">
    <div class="breadcrumb-text">Supervisor <span class="sep">&gt;</span> Reports <span class="sep">&gt;</span> <b>Mechanic Performance</b></div>

    <header style="display: flex; justify-content: space-between; align-items: center; margin: 20px 0;">
        <h1 style="color: var(--primary); margin: 0;">Mechanic Activity Report</h1>
        <div class="export-controls" style="display: flex; gap: 10px;">
            <button class="btn-download" onclick="exportReport('pdf')"><i class="fa-solid fa-file-pdf"></i>PDF</button>
            <button class="btn-download btn-csv" onclick="exportReport('csv')"><i class="fa-solid fa-file-csv"></i>CSV</button>
        </div>
    </header>

    <div style="background: white; padding: 15px 25px; border-radius: 12px; margin-bottom: 25px; display: flex; gap: 25px; align-items: center; box-shadow: 0 2px 5px rgba(0,0,0,0.03);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <label style="font-weight: 600; font-size: 0.9rem;">Date:</label>
            <input type="date" id="filter-date" value="<?= htmlspecialchars($selectedDate ?? '') ?>" style="padding: 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
        </div>
        <div style="display: flex; align-items: center; gap: 10px;">
            <label style="font-weight: 600; font-size: 0.9rem;">Mechanic:</label>
            <select id="filter-mechanic" style="padding: 8px; border-radius: 6px; border: 1px solid #e2e8f0;">
                <option value="">All Mechanics</option>
                <?php foreach ($mechanics as $m): ?>
                    <option value="<?= $m['mechanic_code'] ?>" <?= ($selectedMechanic == $m['mechanic_code']) ? 'selected' : '' ?>><?= $m['mechanic_code'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="analytics-summary-row">
        <div class="stat-box">
            <span class="label">Total Mechanics</span>
            <div class="value"><?= count($activity) ?></div>
        </div>
        <div class="stat-box">
            <span class="label">Total Completed</span>
            <div class="value"><?= array_sum(array_column($activity, 'completed')) ?></div>
        </div>
        <div class="stat-box">
            <span class="label">Avg Efficiency</span>
            <div class="value">
                <?php 
                    $avg = array_filter(array_column($activity, 'avg_duration_mins'));
                    echo count($avg) > 0 ? round(array_sum($avg) / count($avg), 0) : 0;
                ?> mins
            </div>
        </div>
        <div class="stat-box">
            <span class="label">Active Load</span>
            <div class="value"><?= array_sum(array_column($activity, 'in_progress')) ?> Jobs</div>
        </div>
    </div>

    <div class="report-table-container">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Mechanic Name</th>
                    <th>Code</th>
                    <th>Assigned</th>
                    <th>Completed</th>
                    <th>In Progress</th>
                    <th>Open/Pending</th>
                    <th>Avg Duration</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($activity)): ?>
                    <?php foreach ($activity as $row): ?>
                        <tr>
                            <td><b><?= htmlspecialchars($row['mechanic_name'] ?? '-') ?></b></td>
                            <td><span style="background: #eef2ff; color: #4338ca; padding: 4px 8px; border-radius: 4px; font-weight: 600; font-size: 0.8rem;"><?= htmlspecialchars($row['mechanic_code'] ?? '-') ?></span></td>
                            <td><?= (int)($row['total_assigned'] ?? 0) ?></td>
                            <td class="status-good"><?= (int)($row['completed'] ?? 0) ?></td>
                            <td class="status-bad"><?= (int)($row['in_progress'] ?? 0) ?></td>
                            <td style="color: #64748b; font-weight: 600;"><?= (int)($row['open'] ?? 0) ?></td>
                            <td><?= isset($row['avg_duration_mins']) ? (float)$row['avg_duration_mins'] . ' min' : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" style="text-align: center; padding: 40px; color: #64748b;">No mechanic activity found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="charts-container">
        <div class="chart-card">
            <h3>Completion Comparison</h3>
            <canvas id="jobCountChart" height="200"></canvas>
        </div>
        <div class="chart-card">
            <h3>Technician Speed (Mins)</h3>
            <canvas id="efficiencyChart" height="200"></canvas>
        </div>
    </div>

    <button onclick="history.back()" class="btn secondary" style="margin-top: 25px;">Back</button>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = <?= json_encode(array_column($comparison, 'mechanic_code')) ?>;
    
    new Chart(document.getElementById('jobCountChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Completed',
                data: <?= json_encode(array_column($comparison, 'completed_count')) ?>,
                backgroundColor: '#3498db',
                borderRadius: 4
            }]
        },
        options: { plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('efficiencyChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($efficiency, 'mechanic_code')) ?>,
            datasets: [{
                label: 'Avg Mins',
                data: <?= json_encode(array_column($efficiency, 'avg_mins')) ?>,
                backgroundColor: '#f1c40f',
                borderRadius: 4
            }]
        },
        options: { indexAxis: 'y', plugins: { legend: { display: false } } }
    });

    const dateInput = document.getElementById('filter-date');
    const mechanicSelect = document.getElementById('filter-mechanic');

    function applyFilters() {
        const params = new URLSearchParams();
        if (dateInput.value) params.append('date', dateInput.value);
        if (mechanicSelect.value) params.append('mechanic_code', mechanicSelect.value);
        window.location.href = '<?= $base ?>/supervisor/reports/mechanic-activity?' + params.toString();
    }

    dateInput.addEventListener('change', applyFilters);
    mechanicSelect.addEventListener('change', applyFilters);
});

function exportReport(type) {
    const d = document.getElementById('filter-date').value;
    const m = document.getElementById('filter-mechanic').value;

    if (type === 'csv') {
        // Construct the URL and redirect the browser to trigger the download
        const url = `<?= $base ?>/supervisor/reports/export-mechanic?format=csv&date=${d}&mechanic=${m}`;
        window.location.href = url;
    } else if (type === 'pdf') {
        // Get the canvas elements
        const chart1Canvas = document.getElementById('jobCountChart');
        const chart2Canvas = document.getElementById('efficiencyChart');

        // Convert canvases to Base64 Image strings
        const chart1Image = chart1Canvas.toDataURL('image/png');
        const chart2Image = chart2Canvas.toDataURL('image/png');

        const form = document.createElement('form');
        form.method = 'POST';
        // Ensure this URL is registered in your Router
        form.action = `<?= $base ?>/supervisor/reports/export-mechanic?format=pdf`;

        const inputs = {
            'date': document.getElementById('filter-date').value,
            'mechanic': document.getElementById('filter-mechanic').value,
            'chart1_data': chart1Image,
            'chart2_data': chart2Image
        };

        for (const [key, value] of Object.entries(inputs)) {
            const field = document.createElement('input');
            field.type = 'hidden';
            field.name = key;
            field.value = value;
            form.appendChild(field);
        }

        document.body.appendChild(form);
        form.submit();
    } else {
        window.location.href = `<?= $base ?>/supervisor/reports/export-mechanic?format=csv&date=${d}&mechanic=${m}`;
    }
}
</script>
</body>
</html>