<?php
$base = rtrim(BASE_URL, '/');
/** @var array $rows */
/** @var string $pageTitle */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Service History', ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Remembered sidebar CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">
  <!-- Page CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/servicehistory.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <main class="main">
    <header class="sh-header">
      <div>
        <h1>Service History</h1>
        <p class="muted">Branch: <strong><?= (int)($branchId ?? 0) ?: 'Current' ?></strong></p>
      </div>

      <form class="filters" method="get" action="<?= $base ?>/manager/service-history">
        <div class="field">
          <label>Date from</label>
          <input type="date" name="from" value="<?= htmlspecialchars($_GET['from'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
          <label>Date to</label>
          <input type="date" name="to" value="<?= htmlspecialchars($_GET['to'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
          <label>Search</label>
          <input type="text" name="q" placeholder="Vehicle no / Customer / Service" value="<?= htmlspecialchars($_GET['q'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
        </div>
        <div class="field">
          <label>Status</label>
          <select name="status">
            <?php
              $statuses = ['' => 'All', 'Completed' => 'Completed', 'In Service' => 'In Service', 'Waiting' => 'Waiting', 'Not Arrived' => 'Not Arrived', 'Canceled' => 'Canceled'];
              $cur = $_GET['status'] ?? '';
              foreach ($statuses as $k => $label) {
                $sel = ($cur === $k) ? ' selected' : '';
                echo "<option value=\"" . htmlspecialchars($k, ENT_QUOTES, 'UTF-8') . "\"$sel>" . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . "</option>";
              }
            ?>
          </select>
        </div>
        <button class="btn primary" type="submit">Filter</button>
      </form>
    </header>

    <section class="card">
      <div class="table-wrap">
        <table class="data-table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Time</th>
              <th>Vehicle No</th>
              <th>Vehicle</th>
              <th>Customer</th>
              <th>Service</th>
              <th>Technician</th>
              <th>Status</th>
              <th class="t-right">Cost (Rs.)</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
          <?php if (!empty($rows)): ?>
            <?php foreach ($rows as $r): 
              $status = (string)($r['status'] ?? '');
              $badge  = 'badge';
              if (strcasecmp($status, 'Completed') === 0) $badge .= ' success';
              elseif (strcasecmp($status, 'In Service') === 0) $badge .= ' info';
              elseif (strcasecmp($status, 'Waiting') === 0) $badge .= ' warn';
              elseif (strcasecmp($status, 'Not Arrived') === 0) $badge .= ' gray';
              elseif (strcasecmp($status, 'Canceled') === 0) $badge .= ' danger';
            ?>
              <tr>
                <td><?= htmlspecialchars($r['date'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['time'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><strong><?= htmlspecialchars($r['vehicle_no'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                <td><?= htmlspecialchars($r['vehicle'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['customer'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['service'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($r['technician'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><span class="<?= $badge ?>"><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></span></td>
                <td class="t-right"><?= number_format((float)$r['cost'], 2) ?></td>
                <td>
                  <a class="link" href="<?= $base ?>/manager/customers/10045/history">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="10" class="empty">No service history found for the selected filters.</td></tr>
          <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- (Optional) pagination placeholder -->
      <div class="pagination">
        <a class="page disabled" href="#">«</a>
        <a class="page active" href="#">1</a>
        <a class="page" href="#">2</a>
        <a class="page" href="#">3</a>
        <a class="page" href="#">»</a>
      </div>
    </section>
  </main>

</body>
</html>
