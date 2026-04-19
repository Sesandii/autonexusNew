<?php /* Admin view: renders admin-viewcomplaints/index page. */ ?>
<?php
$B = rtrim(BASE_URL, '/');
$current = $current ?? 'complaints';
$f = $filters ?? [];
$adminName = trim((($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''))) ?: 'Admin User';
include 'helpers.php';

function e($value): string
{
  return htmlspecialchars((string) $value);
}

function firstWords(?string $text, int $max = 10): string
{
  $text = trim((string) $text);
  if ($text === '') {
    return '—';
  }

  $words = preg_split('/\s+/', $text);
  if (!$words) {
    return '—';
  }

  if (count($words) <= $max) {
    return $text;
  }

  return implode(' ', array_slice($words, 0, $max)) . '...';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Complaints') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-dashboard.css?v=4">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= $B ?>/public/assets/css/admin-admin-viewcomplaintsindex.css?v=1">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <header class="topbar">
      <div>
        <h1 class="page-title">Complaints</h1>
        <p class="subtitle">Track complaints, assignments, SLA status, and resolutions.</p>
      </div>
    </header>

    <section class="dash-wrap">
      <div class="kpi-grid">
        <div class="kpi-card-link">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-list-check"></i></div>
            <div class="kpi-meta">
              <h3>Total</h3>
              <p class="kpi-value"><?= number_format((int) ($summary['total_count'] ?? 0)) ?></p>
            </div>
          </article>
        </div>

        <div class="kpi-card-link">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-circle-exclamation"></i></div>
            <div class="kpi-meta">
              <h3>Open</h3>
              <p class="kpi-value"><?= number_format((int) ($summary['open_count'] ?? 0)) ?></p>
            </div>
          </article>
        </div>

        <div class="kpi-card-link">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="kpi-meta">
              <h3>In Progress</h3>
              <p class="kpi-value"><?= number_format((int) ($summary['progress_count'] ?? 0)) ?></p>
            </div>
          </article>
        </div>

        <div class="kpi-card-link">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="kpi-meta">
              <h3>Resolved</h3>
              <p class="kpi-value"><?= number_format((int) ($summary['done_count'] ?? 0)) ?></p>
            </div>
          </article>
        </div>

        <div class="kpi-card-link">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="kpi-meta">
              <h3>Urgent</h3>
              <p class="kpi-value"><?= number_format((int) ($summary['urgent_open_count'] ?? 0)) ?></p>
            </div>
          </article>
        </div>

        <div class="kpi-card-link">
          <article class="kpi-card">
            <div class="kpi-icon"><i class="fa-solid fa-inbox"></i></div>
            <div class="kpi-meta">
              <h3>Unassigned</h3>
              <p class="kpi-value"><?= number_format((int) ($summary['unassigned_count'] ?? 0)) ?></p>
            </div>
          </article>
        </div>
      </div>

      <div class="filters-panel">
        <h3><i class="fa-solid fa-filter"></i> Filters & Search</h3>
        <form method="GET" action="<?= $B ?>/admin/admin-viewcomplaints" class="filters">
          <div>
            <label><i class="fa-solid fa-search"></i> Search</label>
            <input type="text" name="q" value="<?= htmlspecialchars($f['search'] ?? '') ?>"
              placeholder="Subject, customer, branch...">
          </div>
          <div>
            <label><i class="fa-solid fa-circle"></i> Status</label>
            <select name="status">
              <option value="">All</option>
              <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $k => $v): ?>
                <option value="<?= $k ?>" <?= (($f['status'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label><i class="fa-solid fa-flag"></i> Priority</label>
            <select name="priority">
              <option value="">All</option>
              <?php foreach (['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $k => $v): ?>
                <option value="<?= $k ?>" <?= (($f['priority'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label><i class="fa-solid fa-building"></i> Branch</label>
            <select name="branch_id">
              <option value="">All</option>
              <?php foreach (($branches ?? []) as $b): ?>
                <option value="<?= (int) $b['branch_id'] ?>" <?= ((string) ($f['branch_id'] ?? '') === (string) $b['branch_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($b['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label><i class="fa-solid fa-user-check"></i> Assigned</label>
            <select name="assigned_to">
              <option value="">All</option>
              <option value="unassigned" <?= (($f['assigned_to'] ?? '') === 'unassigned') ? 'selected' : '' ?>>Unassigned
              </option>
              <?php foreach (($assignableUsers ?? []) as $u): ?>
                <option value="<?= (int) $u['user_id'] ?>" <?= ((string) ($f['assigned_to'] ?? '') === (string) $u['user_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($u['full_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="filter-actions">
            <button class="btn btn-apply" type="submit"><i class="fa-solid fa-check"></i> Apply</button>
            <a class="btn btn-light" href="<?= $B ?>/admin/admin-viewcomplaints"><i class="fa-solid fa-undo"></i>
              Clear</a>
          </div>
        </form>
      </div>

      <div class="panel">
        <div class="panel-head">
          <h2><i class="fa-solid fa-table-list"></i> All Complaints</h2>
        </div>
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Subject</th>
                <th>Customer</th>
                <th>Branch / Service</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Assigned To</th>
                <th>Created</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($records)): ?>
                <?php foreach ($records as $r): ?>
                  <tr>
                    <td>#<?= (int) $r['complaint_id'] ?></td>
                    <td>
                      <strong><?= htmlspecialchars($r['subject']) ?></strong><br>
                      <span
                        style="font-size:12px; color:#6b7280;"><?= htmlspecialchars(mb_strimwidth((string) $r['description'], 0, 70, '...')) ?></span>
                    </td>
                    <td><?= customerInfo($r) ?></td>
                    <td><?= branchServiceInfo($r) ?></td>
                    <td><?= priority($r['priority']) ?></td>
                    <td><?= badge($r['status']) ?></td>
                    <td><?= assignedUserInfo($r) ?></td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td>
                      <?= linkBtn($B . '/admin/admin-viewcomplaints/show?id=' . (int) $r['complaint_id'], 'View', 'fa-eye') ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="9" class="empty-state"><i class="fa-solid fa-inbox"></i> No complaints found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </section>
  </main>

  <script src="<?= $B ?>/app/views/admin/admin-viewcomplaints/script.js"></script>
</body>

</html>