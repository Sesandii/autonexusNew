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
    th,td{padding:12px 10px;border-bottom:1px solid #e2e8f0;text-align:left;vertical-align:top}
    th{background:#f8fafc}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
    .badge.draft{background:#fef3c7;color:#92400e}
    .badge.submitted{background:#dcfce7;color:#166534}
    .muted{color:#64748b}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h1>Inspection Reports</h1>
  <p class="muted">Review inspection reports created by workshop supervisors.</p>

  <section class="card">
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>Report ID</th>
            <th>Work Order</th>
            <th>Customer / Vehicle</th>
            <th>Branch / Service</th>
            <th>Supervisor</th>
            <th>Quality Rating</th>
            <th>Status</th>
            <th>Next Service</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($records)): ?>
            <?php foreach ($records as $r): ?>
              <tr>
                <td>#<?= (int)$r['report_id'] ?></td>
                <td>
                  WO-<?= htmlspecialchars($r['work_order_id'] ?? '—') ?><br>
                  <span class="muted"><?= htmlspecialchars($r['appointment_date'] ?? '—') ?></span>
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
                <td><?= htmlspecialchars($r['supervisor_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars((string)($r['quality_rating'] ?? '—')) ?></td>
                <td>
                  <span class="badge <?= htmlspecialchars($r['status'] ?? 'draft') ?>">
                    <?= htmlspecialchars(ucfirst($r['status'] ?? 'draft')) ?>
                  </span>
                </td>
                <td><?= htmlspecialchars($r['next_service_recommendation'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8">No inspection reports found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>