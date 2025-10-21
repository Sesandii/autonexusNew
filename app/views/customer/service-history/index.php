<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Service History') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/service-history.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <main class="main-content">
      <h2 class="page-title">📂 Service History</h2>

      <div id="serviceList" class="service-list">
        <?php if (!empty($services)): ?>
          <?php foreach ($services as $s): ?>
            <div class="service-card">
              <div class="service-header">
                <h3><?= htmlspecialchars($s['vehicle']) ?></h3>
                <span class="status <?= strtolower($s['status']) ?>">
                  <?= htmlspecialchars($s['status']) ?>
                </span>
              </div>

              <p><strong><?= date('M d, Y', strtotime($s['date'])) ?></strong></p>
              <span class="badge"><?= htmlspecialchars($s['service_type']) ?></span>
              <p><?= htmlspecialchars($s['description'] ?? 'No description available.') ?></p>
              <p class="technician">Technician: <?= htmlspecialchars($s['technician'] ?? 'N/A') ?></p>
              <p class="price">$<?= htmlspecialchars($s['price'] ?? '0.00') ?></p>
              <?php if (!empty($s['pdf'])): ?>
                <a href="<?= $base ?>/uploads/reports/<?= htmlspecialchars($s['pdf']) ?>" class="download" download>
                  ⬇ Download PDF
                </a>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No service history found.</p>
        <?php endif; ?>
      </div>
    </main>
  </div>
</body>
</html>
