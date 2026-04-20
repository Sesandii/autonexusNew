<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 11px;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .stat-grid {
            width: 100%;
            margin-bottom: 20px;
        }

        .stat-card {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        th {
            background: #f4f4f4;
        }

        .chart-box {
            width: 31%;
            display: inline-block;
            text-align: center;
            margin-top: 20px;
            vertical-align: top;
        }

        .chart-img {
            width: 100%;
            height: auto;
            border: 1px solid #eee;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>AutoNexus: Daily Job Completion Report</h1>
        <p>Branch ID: <?= $branchId ?> | Date: <?= $date ?></p>
    </div>

    <table class="stat-grid">
        <tr>
            <td class="stat-card"><b>Avg Completion</b><br><?= (int) $summary['avg_comp'] ?> mins</td>
            <td class="stat-card"><b>Avg Invoice</b><br>LKR <?= number_format($summary['avg_invoice'], 2) ?></td>
            <td class="stat-card"><b>Avg Approval</b><br><?= $summary['avg_appr'] ?> hrs</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th>Mechanic</th>
                <th>Completed</th>
                <th>On-Time</th>
                <th>Delayed</th>
                <th>Avg Time</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($dailyReport as $row): ?>
                <tr>
                    <td><?= $row['mechanic_code'] ?></td>
                    <td><?= $row['total_completed'] ?></td>
                    <td><?= $row['on_time'] ?></td>
                    <td><?= $row['delayed_count'] ?></td>
                    <td><?= $row['avg_completion_time'] ?> mins</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php if (!empty($chartStatus) && !empty($chartHourly) && !empty($chartTrend)): ?>
        <div style="width: 100%;">
            <div class="chart-box">
                <strong>Status</strong><br>
                <img src="<?= $chartStatus ?>" class="chart-img" alt="Status Chart">
            </div>
            <div class="chart-box">
                <strong>Hourly Load</strong><br>
                <img src="<?= $chartHourly ?>" class="chart-img" alt="Hourly Load Chart">
            </div>
            <div class="chart-box">
                <strong>7-Day Trend</strong><br>
                <img src="<?= $chartTrend ?>" class="chart-img" alt="Trend Chart">
            </div>
        </div>
    <?php else: ?>
        <p style="margin-top: 20px; color: #666; text-align: center;">
            Chart visuals were omitted because the server is missing PHP GD support.
        </p>
    <?php endif; ?>
</body>

</html>