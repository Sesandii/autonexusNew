<?php
/** @var array $cards */
/** @var array $branches */
/** @var array $serviceTypes */
/** @var array $filters */
/** @var string $pageTitle */
/** @var string $current */

$current = $current ?? 'history';
$B = rtrim(BASE_URL, '/');

$q        = htmlspecialchars($filters['search']    ?? '', ENT_QUOTES, 'UTF-8');
$from     = htmlspecialchars($filters['from']      ?? '', ENT_QUOTES, 'UTF-8');
$to       = htmlspecialchars($filters['to']        ?? '', ENT_QUOTES, 'UTF-8');
$branchId = (int)($filters['branch_id']           ?? 0);
$typeId   = (int)($filters['type_id']             ?? 0);   // 👈 FIX: use type_id key
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Service History') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .main-content{margin-left:260px;padding:30px;background:#f4f5f7;min-height:100vh;}
    .filters-form{display:flex;flex-wrap:wrap;gap:12px;margin-bottom:20px;align-items:center;}
    .filters-form input,.filters-form select{padding:8px 10px;border-radius:8px;border:1px solid #d1d5db;font-size:14px;}
    .filters-form .search{min-width:220px;}
    .filters-form label{font-size:13px;color:#4b5563;display:flex;flex-direction:column;gap:4px;}
    .cards-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));gap:18px;}
    .card{background:#fff;border-radius:16px;padding:18px;box-shadow:0 1px 4px rgba(15,23,42,.08);}
    .card-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px;}
    .card-header h3{font-size:16px;margin:0;}
    .pill{padding:4px 10px;border-radius:999px;font-size:11px;background:#eff6ff;color:#1d4ed8;white-space:nowrap;}
    .meta-row{font-size:13px;margin-bottom:4px;}
    .meta-label{font-weight:600;color:#4b5563;margin-right:4px;}
    .amount{font-weight:600;}
    .card-footer{margin-top:12px;display:flex;justify-content:flex-end;}
    .btn-view{padding:6px 12px;border-radius:8px;border:none;background:#111827;color:#fff;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px;}
    .no-results{margin-top:20px;font-size:14px;color:#6b7280;}
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <div class="management-header">
    <h2>Service History</h2>
  </div>

  <form class="filters-form" method="get" action="<?= $B ?>/admin/admin-servicehistory">
    <input type="text" name="q" class="search"
           placeholder="Search by customer / vehicle / service / branch"
           value="<?= $q ?>">

    <label>
      From
      <input type="date" name="from" value="<?= $from ?>">
    </label>

    <label>
      To
      <input type="date" name="to" value="<?= $to ?>">
    </label>

    <select name="branch_id">
      <option value="">All Branches</option>
      <?php foreach ($branches as $br): ?>
        <option value="<?= (int)$br['branch_id'] ?>"
          <?= $branchId === (int)$br['branch_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($br['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select name="service_type">
      <option value="">All Service Types</option>
      <?php foreach ($serviceTypes as $st): ?>
        <option value="<?= (int)$st['type_id'] ?>"
          <?= $typeId === (int)$st['type_id'] ? 'selected' : '' ?>>
          <?= htmlspecialchars($st['type_name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <button type="submit" class="btn-view">
      <i class="fa-solid fa-magnifying-glass"></i> Apply
    </button>
  </form>

  <?php if (empty($cards)): ?>
    <div class="no-results">No completed services found for the selected filters.</div>
  <?php else: ?>
    <div class="cards-grid" id="cardsGrid">
      <?php foreach ($cards as $c): ?>
        <div class="card"
             data-completed-date="<?= htmlspecialchars($c['completed_date'], ENT_QUOTES, 'UTF-8') ?>">
          <div class="card-header">
            <div>
              <h3><?= htmlspecialchars($c['service_name']) ?></h3>
              <div class="meta-row">
                <span class="meta-label">Completed:</span>
                <span><?= htmlspecialchars($c['completed_at']) ?></span>
              </div>
            </div>
            <div class="pill"><?= htmlspecialchars($c['service_type']) ?></div>
          </div>

          <div class="meta-row">
            <span class="meta-label">Branch:</span>
            <span><?= htmlspecialchars($c['branch_name']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Customer:</span>
            <span><?= htmlspecialchars($c['customer_name']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Vehicle:</span>
            <span><?= htmlspecialchars($c['vehicle_label'] ?: '—') ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Mechanic:</span>
            <span><?= htmlspecialchars($c['mechanic_name']) ?></span>
          </div>
          <div class="meta-row">
            <span class="meta-label">Total:</span>
            <span class="amount"><?= number_format($c['total_cost'], 2) ?></span>
          </div>

          <div class="card-footer">
            <button class="btn-view"
                    data-url="<?= $B ?>/admin/admin-servicehistory/show?id=<?= (int)$c['id'] ?>">
              <i class="fa-regular fa-eye"></i> View
            </button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('cardsGrid');
  if (!grid) return;

  grid.addEventListener('click', e => {
    const btn = e.target.closest('.btn-view');
    if (!btn) return;
    const url = btn.dataset.url;
    if (url) window.location.href = url;
  });
});
</script>
</body>
</html>
