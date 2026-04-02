<!-- app/views/Manager/Reports/result.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Report Results</title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/report.css">
</head>
<body>
    <?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

    <main class="main">
        <header>
            <h1>Report Results</h1>
            <a href="<?= BASE_URL ?>/manager/reports" class="btn-back">← Back to Reports</a>
        </header>

        <section class="card report-results">
            <div class="report-result">
                <h3>
                    <?= htmlspecialchars(ucwords(str_replace('_', ' ', $reportType))) ?>
                    <?php if(!empty($from) && !empty($to)): ?>
                        (<?= htmlspecialchars($from) ?> → <?= htmlspecialchars($to) ?>)
                    <?php endif; ?>
                </h3>

                <?php if(empty($rows)): ?>
                    <p>No data found for this report.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <?php foreach(array_keys($rows[0]) as $col): ?>
                                        <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $col))) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $row): ?>
                                    <tr>
                                        <?php foreach($row as $cell): ?>
                                            <td><?= htmlspecialchars($cell) ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </main>
</body>
</html>
