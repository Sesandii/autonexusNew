<?php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Service History') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/service-history.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="sh-layout">
    <main class="sh-main">
      <header class="sh-header">
        <div>
          <h1 class="sh-title">
            <i class="fa-solid fa-clipboard-list"></i>
            <span>Service History</span>
          </h1>
          <p class="sh-subtitle">View completed services for your vehicles.</p>
        </div>
      </header>

      <?php if (empty($services)): ?>
        <section class="sh-empty">
          <div class="sh-empty-icon">
            <i class="fa-regular fa-folder-open"></i>
          </div>
          <h2>No completed services yet</h2>
          <p>Once your service jobs are marked as <strong>Completed</strong>, they will appear here.</p>
        </section>
      <?php else: ?>
        <section class="sh-list">
          <?php foreach ($services as $s): ?>
            <?php
              $statusRaw = strtolower($s['status'] ?? '');
              $statusClass = 'sh-status--other';
              if ($statusRaw === 'completed') $statusClass = 'sh-status--completed';
              elseif ($statusRaw === 'in-progress' || $statusRaw === 'in progress') $statusClass = 'sh-status--inprogress';
              elseif ($statusRaw === 'cancelled' || $statusRaw === 'canceled') $statusClass = 'sh-status--cancelled';

              $dateText = !empty($s['date']) ? date('M d, Y', strtotime($s['date'])) : 'Date not set';
              $timeText = $s['time'] ?? '';
            ?>

            <article class="sh-card">
              <div class="sh-card-body">
                <div class="sh-card-header">
                  <div>
                    <div class="sh-vehicle">
                      <i class="fa-solid fa-car-side"></i>
                      <span><?= htmlspecialchars($s['vehicle'] ?? 'Unknown vehicle') ?></span>
                    </div>

                    <div class="sh-service-type">
                      <?= htmlspecialchars($s['service_type'] ?? 'Service') ?>
                    </div>
                  </div>

                  <div class="sh-chip-row">
                    <span class="sh-status <?= $statusClass ?>">
                      <span class="dot"></span>
                      <?= htmlspecialchars(ucfirst($s['status'] ?? '')) ?>
                    </span>

                    <span class="sh-chip">
                      <i class="fa-regular fa-calendar"></i>
                      <?= htmlspecialchars($dateText) ?>
                    </span>

                    <?php if (!empty($timeText)): ?>
                      <span class="sh-chip">
                        <i class="fa-regular fa-clock"></i>
                        <?= htmlspecialchars($timeText) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </div>

                <?php if (!empty($s['description'])): ?>
                  <p class="sh-desc"><?= htmlspecialchars($s['description']) ?></p>
                <?php endif; ?>

                <div class="sh-meta">
                  <div class="sh-meta-item">
                    <span class="k">Technician</span>
                    <span class="v"><?= htmlspecialchars($s['technician'] ?? 'N/A') ?></span>
                  </div>

                  <div class="sh-meta-item">
                    <span class="k">Cost</span>
                    <span class="v">Rs. <?= htmlspecialchars(number_format((float)($s['price'] ?? 0), 2)) ?></span>
                  </div>
                </div>
              </div>
            </article>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
