<?php
$base = rtrim(BASE_URL,'/');
$title = 'AutoNexus - Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/dashboard.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <main class="main-content">
    <h1>Welcome back, <?= htmlspecialchars($user_first_name ?? 'Customer') ?> 👋</h1>
    <p>Manage your vehicle services with ease.</p>
     <?php require APP_ROOT . '/views/partials/lang-switcher.php'; ?>

    <!-- Stats Section -->
    <section class="stats">
      <div class="stat-card red">
        <h3>Next Appointment</h3>
        <p><?= htmlspecialchars($next_appointment['date']) ?></p>
        <span><?= htmlspecialchars($next_appointment['service']) ?></span>
      </div>
      <div class="stat-card gold">
        <h3>Mileage</h3>
        <p><?= number_format((int)($mileage['current'] ?? 0)) ?> km</p>
        <span>Next service at <?= number_format((int)($mileage['next_service_at'] ?? 0)) ?> km</span>
      </div>
      <div class="stat-card dark">
        <h3>Feedback Pending</h3>
        <p><?= (int)($feedback_pending ?? 0) ?> Service<?= (int)($feedback_pending ?? 0) === 1 ? '' : 's' ?></p>
        <span>Needs your review</span>
      </div>
    </section>

    <!-- Recent Services + Quick Actions in one row -->
    <section class="lower-grid">
      <!-- Recent Services card -->
      <section class="history-preview card">
        <h2>Recent Services</h2>
        <div class="history-list">
          <?php foreach (($recent_services ?? []) as $item): ?>
            <div class="history-item">
              <p><?= htmlspecialchars($item['title']) ?></p>
              <span><?= htmlspecialchars($item['date']) ?></span>
            </div>
          <?php endforeach; ?>
          <?php if (empty($recent_services)): ?>
            <div class="history-item">
              <p>No services yet.</p>
              <span>-</span>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- Quick Actions card (like admin quick links) -->
      <aside class="quick-actions card">
        <h2>Quick Actions</h2>
        <div class="ql-grid">
          <a class="ql-card" href="<?= $base ?>/customer/book">
            <i class="fa-regular fa-calendar-check"></i>
            <span>Book Service</span>
          </a>

          <a class="ql-card" href="<?= $base ?>/customer/appointments">
            <i class="fa-solid fa-list-check"></i>
            <span>View Appointments</span>
          </a>

          <a class="ql-card" href="<?= $base ?>/customer/track">
            <i class="fa-solid fa-location-dot"></i>
            <span>Track Service</span>
          </a>

          <a class="ql-card" href="<?= $base ?>/customer/history">
            <i class="fa-solid fa-clock-rotate-left"></i>
            <span>Service History</span>
          </a>
        </div>
      </aside>
    </section>
  </main>

  <script src="<?= $base ?>/public/assets/js/customer-dashboard.js" defer></script>
</body>
</html>
