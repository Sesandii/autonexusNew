<?php
$base = rtrim(BASE_URL,'/');
$title = 'AutoNexus - Dashboard';
$dashboardCssVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/css/customer/dashboard.css') ?: time();
$sidebarCssVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/css/customer/sidebar.css') ?: time();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css?v=<?= (int)$sidebarCssVersion ?>">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/page-header.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/dashboard.css?v=<?= (int)$dashboardCssVersion ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <main class="main-content customer-dashboard">
    <div class="dash-wrap">
      <header class="topbar">
        <div>
          <h1 class="page-title">Dashboard</h1>
          <p class="subtitle">Overview of your vehicle service activity.</p>
        </div>

        <div class="topbar-right">
          <?php require APP_ROOT . '/views/partials/lang-switcher.php'; ?>
          <a class="user-chip user-chip-link" href="<?= $base ?>/customer/profile" aria-label="Open profile">
            <div class="avatar"><i class="fa-solid fa-user"></i></div>
            <span><?= htmlspecialchars($user_display_name ?? $user_first_name ?? 'Customer') ?></span>
          </a>
        </div>
      </header>

      <section class="kpi-grid">
        <a class="kpi-card-link" href="<?= $base ?>/customer/appointments">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-regular fa-calendar-check"></i></div>
            <div class="kpi-meta">
              <h3>Next Appointment</h3>
              <p class="kpi-value"><?= htmlspecialchars($next_appointment['date'] ?? '-') ?></p>
              <div class="kpi-delta"><?= htmlspecialchars($next_appointment['service'] ?? 'No appointment scheduled') ?></div>
            </div>
          </article>
        </a>

        <a class="kpi-card-link" href="<?= $base ?>/customer/track-services">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-location-crosshairs"></i></div>
            <div class="kpi-meta">
              <h3>Track Service</h3>
              <p class="kpi-value">
                <?= (int)($track_summary['active'] ?? 0) ?> Active
                Service<?= (int)($track_summary['active'] ?? 0) === 1 ? '' : 's' ?>
              </p>
              <div class="kpi-delta">
                <?= htmlspecialchars((string)($track_summary['note'] ?? 'No active services right now')) ?>
              </div>
            </div>
          </article>
        </a>

        <a class="kpi-card-link" href="<?= $base ?>/customer/rate-service">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-regular fa-message"></i></div>
            <div class="kpi-meta">
              <h3>Feedback Pending</h3>
              <p class="kpi-value"><?= (int)($feedback_pending ?? 0) ?> Service<?= (int)($feedback_pending ?? 0) === 1 ? '' : 's' ?></p>
              <div class="kpi-delta"><?= (int)($feedback_pending ?? 0) > 0 ? 'Needs your review' : 'All caught up' ?></div>
            </div>
          </article>
        </a>
      </section>

      <section class="content-grid">
        <section class="panel history-panel">
          <div class="panel-head">
            <h2>Recent Services</h2>
            <a class="panel-link" href="<?= $base ?>/customer/service-history">View all</a>
          </div>

          <div class="table-wrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Service</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach (($recent_services ?? []) as $item): ?>
                  <tr>
                    <td><?= htmlspecialchars($item['title']) ?></td>
                    <td><?= htmlspecialchars($item['date']) ?></td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_services)): ?>
                  <tr>
                    <td colspan="2" class="empty-state">No services yet.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <aside class="panel quick-links">
          <div class="panel-head">
            <h2>Quick Actions</h2>
          </div>
          <div class="ql-grid">
            <a class="ql-card" href="<?= $base ?>/customer/book">
              <i class="fa-regular fa-calendar-check"></i>
              <span>Book Service</span>
            </a>

            <a class="ql-card" href="<?= $base ?>/customer/appointments">
              <i class="fa-solid fa-list-check"></i>
              <span>View Appointments</span>
            </a>

            <a class="ql-card" href="<?= $base ?>/customer/track-services">
              <i class="fa-solid fa-location-crosshairs"></i>
              <span>Track Service</span>
            </a>

            <a class="ql-card" href="<?= $base ?>/customer/service-history">
              <i class="fa-solid fa-clock-rotate-left"></i>
              <span>Service History</span>
            </a>
          </div>
        </aside>
      </section>
    </div>
  </main>

  <script src="<?= $base ?>/public/assets/js/customer-dashboard.js" defer></script>
</body>
</html>
