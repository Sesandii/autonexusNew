<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Results</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/result.css">
</head>
<body>
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<main class="main">

    <div class="header">
        <h1>Report results</h1>
        <a href="<?= BASE_URL ?>/manager/reports" class="btn-back">← Back to reports</a>
    </div>

    <div class="meta-bar">
        <span class="report-title">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $reportType))) ?>
        </span>
        <?php if (!empty($from) && !empty($to)): ?>
            <span class="date-badge"><?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?></span>
        <?php endif; ?>
    </div>

    <?php if (!empty($rows)): ?>
    <!-- Summary metrics -->
    <div class="summary-row">
        <div class="metric">
            <div class="metric-label">Total records</div>
            <div class="metric-value"><?= count($rows) ?></div>
        </div>

        <?php if ($reportType === 'revenue'): ?>
            <div class="metric">
                <div class="metric-label">Total revenue</div>
                <div class="metric-value">
                    <?= isset($rows[0]['total_revenue'])
                        ? 'LKR ' . number_format(array_sum(array_column($rows, 'total_revenue')), 2)
                        : '—' ?>
                </div>
            </div>

        <?php elseif ($reportType === 'pending_services'): ?>
            <?php
                $statuses = array_column($rows, 'computed_status');
                $overdue  = count(array_filter($statuses, fn($s) => $s === 'Overdue'));
                $pending  = count(array_filter($statuses, fn($s) => $s === 'Pending'));
                $onhold   = count(array_filter($statuses, fn($s) => $s === 'On Hold'));
            ?>
            <div class="metric">
                <div class="metric-label">Overdue</div>
                <div class="metric-value danger"><?= $overdue ?></div>
            </div>
            <div class="metric">
                <div class="metric-label">Pending</div>
                <div class="metric-value warning"><?= $pending ?></div>
            </div>
            <div class="metric">
                <div class="metric-label">On hold</div>
                <div class="metric-value muted"><?= $onhold ?></div>
            </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="card">
        <?php if (empty($rows)): ?>
            <div class="empty">No data found for the selected period.</div>
        <?php else: ?>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <?php foreach (array_keys($rows[0]) as $col): ?>
                                <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col))) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($rows as $row): ?>
                            <tr>
                                <?php foreach ($row as $key => $cell): ?>
                                    <td>
                                        <?php if ($key === 'computed_status'): ?>
                                            <?php
                                                $cls = match($cell) {
                                                    'Overdue'     => 'status-overdue',
                                                    'Pending'     => 'status-pending',
                                                    'In Progress' => 'status-inprogress',
                                                    'On Hold'     => 'status-onhold',
                                                    default       => ''
                                                };
                                            ?>
                                            <span class="status-badge <?= $cls ?>">
                                                <?= htmlspecialchars($cell) ?>
                                            </span>
                                        <?php else: ?>
                                            <?= htmlspecialchars($cell ?? '—') ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</main>
</body>
</html>