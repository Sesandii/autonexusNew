<?php $B = rtrim(BASE_URL, '/'); $current = $current ?? 'qc-approvals'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Final Approvals') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content{margin-left:260px;padding:30px;background:#f8fafc;min-height:100vh}
    .card{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:20px}
    .list{display:grid;gap:14px}
    .item{border:1px solid #e2e8f0;border-radius:14px;padding:16px;background:#fff}
    .item h3{margin:0 0 8px}
    .muted{color:#64748b}
    .meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin:10px 0}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
    .badge.good{background:#dcfce7;color:#166534}
    .badge.warn{background:#fef3c7;color:#92400e}
    .badge.bad{background:#fee2e2;color:#991b1b}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h1>Final Approvals</h1>
  <p class="muted">Review final reports with linked inspection details and QC verification context.</p>

  <section class="card">
    <div class="list">
      <?php if (!empty($records)): ?>
        <?php foreach ($records as $r): ?>
          <?php
            $rating = (float)($r['quality_rating'] ?? 0);
            $badgeClass = $rating <= 2 ? 'bad' : ($rating == 3 ? 'warn' : 'good');
          ?>
          <div class="item">
            <h3>Final Report #<?= (int)$r['report_id'] ?></h3>

            <div class="meta">
              <div><strong>Customer:</strong> <?= htmlspecialchars($r['customer_name'] ?? '—') ?></div>
              <div><strong>Vehicle:</strong> <?= htmlspecialchars($r['license_plate'] ?? '—') ?></div>
              <div><strong>Branch:</strong> <?= htmlspecialchars($r['branch_name'] ?? '—') ?></div>
              <div><strong>Service:</strong> <?= htmlspecialchars($r['service_name'] ?? '—') ?></div>
              <div><strong>Mechanic:</strong> <?= htmlspecialchars($r['mechanic_name'] ?? '—') ?></div>
              <div><strong>Supervisor:</strong> <?= htmlspecialchars($r['supervisor_name'] ?? '—') ?></div>
            </div>

            <p>
              <strong>Inspection Rating:</strong>
              <span class="badge <?= $badgeClass ?>">
                <?= htmlspecialchars((string)($r['quality_rating'] ?? '0')) ?>
              </span>
              &nbsp; <strong>Inspection Status:</strong> <?= htmlspecialchars($r['inspection_status'] ?? '—') ?>
            </p>

            <p><strong>Report Details:</strong><br><?= nl2br(htmlspecialchars($r['report_details'] ?? '')) ?></p>
            <p><strong>Recommendations:</strong><br><?= nl2br(htmlspecialchars($r['recommendations'] ?? '')) ?></p>

            <p class="muted">
              <strong>Verification History:</strong>
              Inspection updated <?= htmlspecialchars($r['inspection_updated_at'] ?? '—') ?>,
              Final report created <?= htmlspecialchars($r['created_at'] ?? '—') ?>
            </p>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No final approvals found.</p>
      <?php endif; ?>
    </div>
  </section>
</main>
</body>
</html>