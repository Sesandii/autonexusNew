<?php $base = rtrim(BASE_URL, '/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vehicle History<?= isset($vehicle['license_plate']) ? ' - ' . htmlspecialchars($vehicle['license_plate']) : '' ?></title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-history.css">
</head>
<body>

<?php include __DIR__ . '/../partials/sidebar.php'; ?>

<div class="main-content">
<header class="page-header">
    <div>
      <?php if (!empty($vehicle)): ?>
        <h1><?= htmlspecialchars($vehicle['make'] . ' ' . $vehicle['model']) ?> (<?= htmlspecialchars($vehicle['license_plate']) ?>)</h1>
        <p class="subtitle">Owner: <?= htmlspecialchars($vehicle['owner_name'] ?? 'Unknown') ?></p>
      <?php else: ?>
        <h1>Vehicle Not Found</h1>
        <p class="subtitle">Please check the license plate number and try again.</p>
      <?php endif; ?>
    </div>
    <a class="btn" href="<?= $base ?>/supervisor/history">Back</a>
      </header>

  <?php if (!empty($appointments)): ?>
    <div class="tiles">
      <?php foreach ($appointments as $a): ?>
        <div class="tile">
  <h3><?= htmlspecialchars($a['service_name']) ?></h3>
  <p><b>Date:</b> <?= htmlspecialchars($a['appointment_date']) ?></p>
  <p><b>Time:</b> <?= htmlspecialchars($a['appointment_time']) ?></p>
  <p><b>Branch:</b> <?= htmlspecialchars($a['branch_id']) ?></p>
  <p><b>Status:</b> <?= htmlspecialchars($a['status']) ?></p>
  <p><b>Price:</b> Rs. <?= htmlspecialchars($a['default_price']) ?></p>
  <p><b>Description:</b> <?= htmlspecialchars($a['service_description'] ?? '—') ?></p>
  <a href="<?= $base ?>/supervisor/history/details/<?= $a['appointment_id'] ?>" class="view-btn">View Details</a>
</div>
      <?php endforeach; ?>
    </div>
  <?php elseif (!empty($vehicle)): ?>
    <p class="subtitle" style="text-align:center;">No completed services found for this vehicle.</p>
  <?php endif; ?>
  </div>

</body>
</html>
