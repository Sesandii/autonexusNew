<?php $B = rtrim(BASE_URL, '/'); $current = $current ?? 'qc-reports'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Inspection Reports') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content{margin-left:260px;padding:30px;background:#f8fafc;min-height:100vh}
    .card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px}
    .table-wrap{overflow:auto}
    table{width:100%;border-collapse:collapse}
    th,td{padding:12px 10px;border-bottom:1px solid #e2e8f0;text-align:left;vertical-align:top;font-size:14px}
    th{background:#f8fafc}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
    .badge.draft{background:#fef3c7;color:#92400e}
    .badge.submitted{background:#dcfce7;color:#166534}
    .badge.failed{background:#fee2e2;color:#991b1b}
    .badge.warn{background:#fde68a;color:#92400e}
    .muted{color:#64748b}
    .flag{font-weight:700}
    .flag.ok{color:#166534}
    .flag.bad{color:#991b1b}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h1>Inspection Reports</h1>
  <p class="muted">Review inspection reports with QC checks, photo evidence, and follow-up indicators.</p>

  <section class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Report</th>
            <th>Customer / Vehicle</th>
            <th>Branch / Service</th>
            <th>Supervisor / Mechanic</th>
            <th>Quality</th>
            <th>QC Checks</th>
            <th>Photos</th>
            <th>Status</th>
            <th>Follow-up</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($records)): ?>
            <?php foreach ($records as $r): ?>
              <?php
                $needsFollowUp =
                    ((int)($r['quality_rating'] ?? 0) <= 2)
                    || ((int)($r['checklist_verified'] ?? 0) === 0)
                    || ((int)($r['test_driven'] ?? 0) === 0)
                    || ((int)($r['concerns_addressed'] ?? 0) === 0);
              ?>
              <tr>
                <td>
                  <strong>#<?= (int)$r['report_id'] ?></strong><br>
                  <span class="muted">WO-<?= htmlspecialchars($r['work_order_id'] ?? '—') ?></span><br>
                  <span class="muted"><?= htmlspecialchars($r['created_at'] ?? '—') ?></span>
                </td>
                <td>
                  <?= htmlspecialchars($r['customer_name'] ?? '—') ?><br>
                  <span class="muted"><?= htmlspecialchars(trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? ''))) ?></span><br>
                  <span class="muted"><?= htmlspecialchars($r['license_plate'] ?? '—') ?></span>
                </td>
                <td>
                  <?= htmlspecialchars($r['branch_name'] ?? '—') ?><br>
                  <span class="muted"><?= htmlspecialchars($r['service_name'] ?? '—') ?></span>
                </td>
                <td>
                  <strong>Supervisor:</strong> <?= htmlspecialchars($r['supervisor_name'] ?? '—') ?><br>
                  <span class="muted"><strong>Mechanic:</strong> <?= htmlspecialchars($r['mechanic_name'] ?? '—') ?></span>
                </td>
                <td>
                  <strong><?= htmlspecialchars((string)($r['quality_rating'] ?? '—')) ?></strong><br>
                  <?php if ((int)($r['quality_rating'] ?? 0) <= 2): ?>
                    <span class="badge failed">Low Score</span>
                  <?php elseif ((int)($r['quality_rating'] ?? 0) === 3): ?>
                    <span class="badge warn">Average</span>
                  <?php else: ?>
                    <span class="badge submitted">Good</span>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="flag <?= (int)($r['checklist_verified'] ?? 0) ? 'ok' : 'bad' ?>">Checklist: <?= (int)($r['checklist_verified'] ?? 0) ? 'Yes' : 'No' ?></div>
                  <div class="flag <?= (int)($r['test_driven'] ?? 0) ? 'ok' : 'bad' ?>">Test Drive: <?= (int)($r['test_driven'] ?? 0) ? 'Yes' : 'No' ?></div>
                  <div class="flag <?= (int)($r['concerns_addressed'] ?? 0) ? 'ok' : 'bad' ?>">Concerns: <?= (int)($r['concerns_addressed'] ?? 0) ? 'Yes' : 'No' ?></div>
                </td>
                <td><?= (int)($r['report_photo_count'] ?? 0) ?></td>
                <td>
                  <span class="badge <?= htmlspecialchars($r['status'] ?? 'draft') ?>">
                    <?= htmlspecialchars(ucfirst($r['status'] ?? 'draft')) ?>
                  </span>
                </td>
                <td>
                  <?php if ($needsFollowUp): ?>
                    <span class="badge failed">Needs Follow-up</span>
                  <?php else: ?>
                    <span class="badge submitted">OK</span>
                  <?php endif; ?>
                  <br><span class="muted"><?= htmlspecialchars($r['next_service_recommendation'] ?? '—') ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="9">No inspection reports found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>