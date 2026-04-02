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
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h1>Final Approvals</h1>
  <p class="muted">Review final reports ready for admin-level checking.</p>

  <section class="card">
    <div class="list">
      <?php if (!empty($records)): ?>
        <?php foreach ($records as $r): ?>
          <div class="item">
            <h3>Final Report #<?= (int)$r['report_id'] ?></h3>
            <p><strong>Customer:</strong> <?= htmlspecialchars($r['customer_name'] ?? '—') ?></p>
            <p><strong>Vehicle:</strong> <?= htmlspecialchars($r['license_plate'] ?? '—') ?></p>
            <p><strong>Branch:</strong> <?= htmlspecialchars($r['branch_name'] ?? '—') ?></p>
            <p><strong>Service:</strong> <?= htmlspecialchars($r['service_name'] ?? '—') ?></p>
            <p><strong>Report Details:</strong><br><?= nl2br(htmlspecialchars($r['report_details'] ?? '')) ?></p>
            <p><strong>Recommendations:</strong><br><?= nl2br(htmlspecialchars($r['recommendations'] ?? '')) ?></p>
            <p class="muted"><strong>Created:</strong> <?= htmlspecialchars($r['created_at'] ?? '—') ?></p>
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