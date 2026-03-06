<?php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Service History') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/service-history.css?v=<?= time() ?>">
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

      <?php 
      // Debug: Show user ID and count
      if (isset($_SESSION['user']) || isset($_SESSION['user_id'])) {
          $debug_user_id = $_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? 'unknown';
          $debug_count = count($services ?? []);
          echo "<!-- Debug: User ID = $debug_user_id, Service Count = $debug_count -->";
      }
      ?>

      <?php if (empty($services)): ?>
        <section class="sh-empty">
          <div class="sh-empty-icon">
            <i class="fa-regular fa-folder-open"></i>
          </div>
          <h2>No service records found</h2>
          <p>Once your service jobs are completed, they will appear here.</p>
          <p style="font-size: 0.85rem; color: #9ca3af; margin-top: 10px;">
            <?php 
            $user_id = $_SESSION['user']['user_id'] ?? $_SESSION['user_id'] ?? 'unknown';
            echo "Searching for services for user ID: " . htmlspecialchars((string)$user_id);
            ?>
          </p>
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
              <div class="sh-card-topbar"></div>
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

                <div class="sh-meta-grid">
                  <div>
                    <div class="sh-label">Technician</div>
                    <div class="sh-value"><?= htmlspecialchars($s['technician'] ?? 'Not assigned') ?></div>
                  </div>

                  <div>
                    <div class="sh-label">Total Cost</div>
                    <div class="sh-value">Rs. <?= htmlspecialchars(number_format((float)($s['price'] ?? 0), 2)) ?></div>
                  </div>

                  <div>
                    <div class="sh-label">Work Order</div>
                    <div class="sh-value">#<?= htmlspecialchars($s['work_order_id'] ?? 'N/A') ?></div>
                  </div>

                  <div>
                    <div class="sh-label">Branch</div>
                    <div class="sh-value"><?= htmlspecialchars($s['branch_name'] ?? 'Main Branch') ?></div>
                  </div>
                </div>
              </div>

              <div class="sh-card-footer">
                <a href="<?= $base ?>/customer/service-history/<?= (int)$s['work_order_id'] ?>" class="sh-btn-outline">
                  <i class="fa-solid fa-file-lines"></i>
                  View Details
                </a>
              </div>
            </article>
          <?php endforeach; ?>
        </section>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
