<?php
/** @var array  $workOrders */
/** @var array  $branches */
/** @var string $selectedDate */
/** @var string $currentDateText */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'progress';
$B = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($pageTitle ?? 'Ongoing Services') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin/ongoingservices/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* minimal safety if your CSS file is empty */
    .main-content{margin-left:260px;padding:30px;background:#f4f5f7;min-height:100vh;}
    .filters{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;}
    .filters select,.filters input{padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:14px;}
    .cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px;margin-top:10px;}
    .card{background:#fff;border-radius:16px;padding:18px;box-shadow:0 1px 4px rgba(15,23,42,.08);}
    .card-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:8px;}
    .card-header h2{font-size:16px;margin:0;}
    .duration-pill{background:#f3f4f6;border-radius:999px;padding:6px 10px;font-size:12px;}
    .status-badge{padding:4px 10px;border-radius:999px;font-size:12px;}
    .status-badge.received{background:#fee2e2;color:#b91c1c;}
    .status-badge.in-service{background:#dbeafe;color:#1d4ed8;}
    .status-badge.completed{background:#dcfce7;color:#15803d;}
    .progress-bar{margin-top:10px;font-size:12px;}
    .progress-steps{display:flex;gap:10px;margin-bottom:4px;}
    .progress-steps .step{color:#9ca3af;}
    .progress-steps .step.active{color:#dc2626;font-weight:600;}
    .bar{height:6px;background:#e5e7eb;border-radius:999px;overflow:hidden;}
    .progress{height:100%;border-radius:999px;background:#f97316;width:0;}
    .progress.received{width:33%;}
    .progress.in-service{width:66%;}
    .progress.completed{width:100%;}
    .card-footer{margin-top:14px;display:flex;justify-content:flex-end;}
    .btn-view{padding:6px 12px;border-radius:8px;border:none;background:#111827;color:#fff;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
    .date-label{color:#6b7280;margin-bottom:16px;}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h2>Ongoing Services</h2>
  <div class="date-label">
    <?= htmlspecialchars($currentDateText) ?>
  </div>

  <div class="filters">
    <!-- Branch filter -->
    <select id="branchFilter">
      <option value="">All Branches</option>
      <?php foreach ($branches as $b): ?>
        <option value="<?= (int)$b['branch_id'] ?>">
          <?= htmlspecialchars($b['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <!-- Status filter -->
    <select id="statusFilter">
      <option value="">All Status</option>
      <option value="Received">Received</option>
      <option value="In Service">In Service</option>
      <option value="Completed">Completed</option>
    </select>

    <!-- Time filter -->
    <input id="timeFilter" type="time" />
  </div>

  <div class="cards" id="cardsContainer">
    <?php foreach ($workOrders as $w):
      $dt  = new DateTime($w['datetime']);
      $timeDisplay = $dt->format('g:i A');
      $time24      = $dt->format('H:i');

      $uiStatus  = $w['status_ui'];
      $statusDb  = $w['status_db'];

      $badgeClass = $uiStatus === 'Received'   ? 'received'
                  : ($uiStatus === 'In Service' ? 'in-service'
                  : 'completed');

      $progressClass = $badgeClass;
    ?>
      <div class="card"
           data-branch-id="<?= (int)$w['branch_id'] ?>"
           data-status="<?= htmlspecialchars($uiStatus) ?>"
           data-time="<?= $time24 ?>">
        <div class="card-header">
          <div>
            <h2><?= htmlspecialchars($w['service']) ?></h2>
            <div style="margin-top:4px;">
              <span class="status-badge <?= $badgeClass ?>">
                <?= htmlspecialchars($uiStatus) ?>
              </span>
            </div>
          </div>
          <div class="duration-pill">
            <?= (int)$w['duration_minutes'] ?: 0 ?> min
          </div>
        </div>

        <p><strong>Customer:</strong> <?= htmlspecialchars($w['customer']) ?></p>
        <p><strong>Branch:</strong> <?= htmlspecialchars($w['branch']) ?></p>
        <p><strong>Assigned to:</strong> <?= htmlspecialchars($w['mechanic']) ?></p>
        <p><strong>Appointment Time:</strong> <?= htmlspecialchars($timeDisplay) ?></p>

        <div class="progress-bar">
          <div class="progress-steps">
            <span class="step <?= in_array($statusDb, ['open','in_progress','completed'], true) ? 'active' : '' ?>">Received</span>
            <span class="step <?= in_array($statusDb, ['in_progress','completed'], true) ? 'active' : '' ?>">In Service</span>
            <span class="step <?= $statusDb === 'completed' ? 'active' : '' ?>">Completed</span>
          </div>
          <div class="bar">
            <div class="progress <?= $progressClass ?>"></div>
          </div>
        </div>

        <div class="card-footer">
          <button class="btn-view"
                  data-url="<?= $B ?>/admin/admin-ongoingservices/show?id=<?= (int)$w['id'] ?>">
            <i class="fa-regular fa-eye"></i>
            View
          </button>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const cardsContainer = document.getElementById('cardsContainer');
  if (!cardsContainer) return;

  const cards        = Array.from(cardsContainer.querySelectorAll('.card'));
  const branchFilter = document.getElementById('branchFilter');
  const statusFilter = document.getElementById('statusFilter');
  const timeFilter   = document.getElementById('timeFilter');

  function applyFilters() {
    const branchVal = branchFilter ? branchFilter.value : '';
    const statusVal = statusFilter ? statusFilter.value : '';
    const timeVal   = timeFilter   ? timeFilter.value   : '';

    cards.forEach(card => {
      const cardBranch = card.dataset.branchId || '';
      const cardStatus = card.dataset.status || '';
      const cardTime   = card.dataset.time   || '';

      const matchesBranch = !branchVal || cardBranch === branchVal;
      const matchesStatus = !statusVal || cardStatus === statusVal;
      const matchesTime   = !timeVal   || (cardTime && cardTime.startsWith(timeVal));

      const visible = matchesBranch && matchesStatus && matchesTime;
      card.style.display = visible ? '' : 'none';
    });
  }

  if (branchFilter) branchFilter.addEventListener('change', applyFilters);
  if (statusFilter) statusFilter.addEventListener('change', applyFilters);
  if (timeFilter)   timeFilter.addEventListener('change', applyFilters);

  // View button navigation
  cardsContainer.addEventListener('click', e => {
    const btn = e.target.closest('.btn-view');
    if (!btn) return;
    const url = btn.dataset.url;
    if (url) window.location.href = url;
  });
});
</script>
</body>
</html>
