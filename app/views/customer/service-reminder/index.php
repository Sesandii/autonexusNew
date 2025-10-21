<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Service Reminder') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/service-reminder.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <div class="main-content">
      <h2 class="page-title">⏰ Service Reminder</h2>
      <p class="subtitle">Track your vehicles' mileage and upcoming service needs.</p>

      <div id="reminderList" class="reminder-list">
        <?php if (!empty($reminders)): ?>
          <?php foreach ($reminders as $r): 
              $progress = 0;
              if (!empty($r['last_service_date']) && !empty($r['next_service_due'])) {
                  $last = strtotime($r['last_service_date']);
                  $next = strtotime($r['next_service_due']);
                  $today = time();
                  $progress = max(0, min(100, (($today - $last) / max(1, $next - $last)) * 100));
              }
              $overdue = $progress >= 100;
          ?>
          <div class="reminder-card <?= $overdue ? 'overdue' : '' ?>">
            <h3><?= htmlspecialchars($r['brand'] . ' ' . $r['model']) ?></h3>
            <p class="vehicle-info"><?= htmlspecialchars($r['reg_no']) ?></p>
            <p class="mileage"><?= number_format((int)$r['current_mileage']) ?> <span>km</span></p>

            <div class="service-dates">
              <p><strong>Last Service:</strong> <?= $r['last_service_date'] ? date('M d, Y', strtotime($r['last_service_date'])) : '—' ?></p>
              <p><strong>Next Service Due:</strong> <?= $r['next_service_due'] ? date('M d, Y', strtotime($r['next_service_due'])) : '—' ?></p>
            </div>

            <div class="progress-bar">
              <div class="progress" style="width:<?= (int)$progress ?>%"></div>
            </div>

            <?php if ($overdue): ?>
              <span class="overdue-label">⚠ Service Overdue!</span>
            <?php endif; ?>

            <form class="update-form" method="POST" action="<?= $base ?>/customer/service-reminder/update">
              <input type="hidden" name="vehicle_id" value="<?= $r['vehicle_id'] ?>">
              <input type="number" name="mileage" min="0" placeholder="Enter new mileage" required>
              <button type="submit" class="btn">+ Add Mileage Update</button>
            </form>
            <span class="set-reminder">🔔 Set Reminder</span>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No vehicles found.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="<?= $base ?>/assets/js/customer/service-reminder.js"></script>
</body>
</html>
