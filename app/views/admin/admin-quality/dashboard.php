<?php $B = rtrim(BASE_URL, '/'); $current = $current ?? 'qc-dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Quality Dashboard') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content{margin-left:260px;padding:30px;background:#f8fafc;min-height:100vh}
    .cards{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:14px;margin-bottom:20px}
    .card,.mini{background:#fff;border:1px solid #e2e8f0;border-radius:16px;padding:18px}
    .mini .label{font-size:12px;color:#64748b}
    .mini .value{font-size:24px;font-weight:800;margin-top:6px}
    .grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .list{list-style:none;padding:0;margin:0}
    .list li{display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #e2e8f0}
    .list li:last-child{border-bottom:none}
    @media (max-width: 1200px){
      .cards{grid-template-columns:repeat(2,minmax(0,1fr))}
      .grid{grid-template-columns:1fr}
      .main-content{margin-left:0}
    }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h1>Quality Dashboard</h1>
  <p style="color:#64748b;">Quick quality-control overview based on your existing reports, checklists, and photos tables.</p>

  <section class="cards">
    <div class="mini"><div class="label">Inspection Reports</div><div class="value"><?= (int)($summary['reports_total'] ?? 0) ?></div></div>
    <div class="mini"><div class="label">Draft Reports</div><div class="value"><?= (int)($summary['reports_draft'] ?? 0) ?></div></div>
    <div class="mini"><div class="label">Submitted Reports</div><div class="value"><?= (int)($summary['reports_submitted'] ?? 0) ?></div></div>
    <div class="mini"><div class="label">Final Reports</div><div class="value"><?= (int)($summary['final_reports_total'] ?? 0) ?></div></div>
    <div class="mini"><div class="label">Checklist Items</div><div class="value"><?= (int)($summary['checklists_total'] ?? 0) ?></div></div>
    <div class="mini"><div class="label">Uploaded Photos</div><div class="value"><?= (int)($summary['photos_total'] ?? 0) ?></div></div>
  </section>

  <section class="grid">
    <div class="card">
      <h2>Quality Ratings</h2>
      <ul class="list">
        <?php if (!empty($ratings)): ?>
          <?php foreach ($ratings as $r): ?>
            <li>
              <span>Rating <?= htmlspecialchars((string)$r['label']) ?></span>
              <strong><?= (int)$r['total'] ?></strong>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li><span>No data</span><strong>0</strong></li>
        <?php endif; ?>
      </ul>
    </div>

    <div class="card">
      <h2>Reports by Branch</h2>
      <ul class="list">
        <?php if (!empty($branches)): ?>
          <?php foreach ($branches as $b): ?>
            <li>
              <span><?= htmlspecialchars($b['label']) ?></span>
              <strong><?= (int)$b['total'] ?></strong>
            </li>
          <?php endforeach; ?>
        <?php else: ?>
          <li><span>No data</span><strong>0</strong></li>
        <?php endif; ?>
      </ul>
    </div>
  </section>
</main>
</body>
</html>