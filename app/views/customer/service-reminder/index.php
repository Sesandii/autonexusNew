<?php
/** @var string $title */
/** @var array  $reminders */

$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Service Reminder') ?> • AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/service-reminder.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

<div class="cx-container">
  <main class="cx-main">
    <header class="cx-header">
      <div>
        <h1 class="cx-title">Service Reminder</h1>
        <p class="cx-subtitle">
          Track mileage and upcoming services for all your vehicles.
        </p>
      </div>
      <span class="cx-chip">
        <i class="fa-solid fa-gauge-high"></i>
        AutoNexus Care
      </span>
    </header>

    <?php if (empty($reminders)): ?>
      <section class="cx-empty">
        <div class="cx-empty-icon">
          <i class="fa-solid fa-car-side"></i>
        </div>
        <h2>No vehicles yet</h2>
        <p>
          Once you add vehicles to your profile and start tracking mileage,
          we’ll show reminders here.
        </p>
      </section>
    <?php else: ?>
      <section class="cx-grid">
        <?php foreach ($reminders as $r): ?>
          <?php
            $brand   = trim(($r['make'] ?? '') . ' ' . ($r['model'] ?? ''));
            $regNo   = $r['reg_no'] ?? '—';
            $mileage = max(0, (int)($r['current_mileage'] ?? 0));
            $interval = max(1000, (int)($r['service_interval_km'] ?? 5000));

            $last = isset($r['last_service_mileage'])
                ? (int)$r['last_service_mileage']
                : null;

            $statusLabel = 'No history';
            $statusClass = 'cx-status-unknown';
            $progress    = 0;
            $nextDueKm   = null;
            $kmToNext    = null;

            if ($last !== null && $last >= 0) {
                $elapsed   = max(0, $mileage - $last);
                $nextDueKm = $last + $interval;
                $kmToNext  = $nextDueKm - $mileage;

                if ($interval > 0) {
                    $ratio    = min(1, $elapsed / $interval);
                    $progress = (int)round($ratio * 100);
                }

                if ($kmToNext <= 0) {
                    $statusLabel = 'Overdue';
                    $statusClass = 'cx-status-overdue';
                    $progress    = 100;
                } elseif ($kmToNext <= $interval * 0.1) { // last 10%
                    $statusLabel = 'Due soon';
                    $statusClass = 'cx-status-soon';
                } else {
                    $statusLabel = 'On track';
                    $statusClass = 'cx-status-ok';
                }
            }
          ?>

          <article class="cx-card">
            <header class="cx-card-header">
              <div>
                <h2 class="cx-card-title">
                  <?= htmlspecialchars($brand ?: 'Unnamed vehicle') ?>
                </h2>
                <p class="cx-card-reg"><?= htmlspecialchars($regNo) ?></p>
              </div>
              <span class="cx-status-pill <?= $statusClass ?>">
                <span class="dot"></span><?= htmlspecialchars($statusLabel) ?>
              </span>
            </header>

            <div class="cx-card-body">
              <div class="cx-card-row">
                <span class="cx-label">Current mileage</span>
                <span class="cx-value"><?= number_format($mileage) ?> km</span>
              </div>

              <div class="cx-card-row">
                <span class="cx-label">Last service mileage</span>
                <span class="cx-value">
                  <?= $last !== null ? number_format($last) . ' km' : 'Not set' ?>
                </span>
              </div>

              <div class="cx-card-row">
                <span class="cx-label">Service interval</span>
                <span class="cx-value"><?= number_format($interval) ?> km</span>
              </div>

              <div class="cx-card-row">
                <span class="cx-label">Next service due at</span>
                <span class="cx-value">
                  <?= $nextDueKm !== null ? number_format($nextDueKm) . ' km' : '—' ?>
                </span>
              </div>

              <?php if ($kmToNext !== null && $kmToNext > 0): ?>
                <div class="cx-card-row cx-card-row--muted">
                  <span class="cx-label">Distance to next service</span>
                  <span class="cx-value">
                    <?= number_format($kmToNext) ?> km
                  </span>
                </div>
              <?php endif; ?>

              <div class="cx-progress-wrap">
                <div class="cx-progress-label">
                  <span>Progress toward next service</span>
                  <span><?= $progress ?>%</span>
                </div>
                <div class="cx-progress-bar">
                  <div class="cx-progress-fill"
                       style="width: <?= $progress ?>%;"></div>
                </div>
              </div>

              <form class="cx-form" method="post"
                    action="<?= $base ?>/customer/service-reminder/update">
                <input type="hidden" name="vehicle_id"
                       value="<?= (int)$r['vehicle_id'] ?>">
                <label class="cx-form-group">
                  <span class="cx-label">Update mileage</span>
                  <input type="number"
                         name="mileage"
                         min="0"
                         required
                         placeholder="Enter new odometer reading">
                </label>
                <button type="submit" class="cx-btn-primary">
                  <i class="fa-solid fa-plus"></i>
                  <span>Add mileage update</span>
                </button>
              </form>

              <?php if ($last === null): ?>
                <p class="cx-note">
                  Tip: ask our staff to set your last service mileage
                  after your next visit so reminders become more accurate.
                </p>
              <?php endif; ?>
            </div>
          </article>
        <?php endforeach; ?>
      </section>
    <?php endif; ?>
  </main>
</div>

<script src="<?= $base ?>/public/assets/js/customer/service-reminder.js"></script>
</body>
</html>
