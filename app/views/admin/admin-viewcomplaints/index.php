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

  <style>
    .main-content {
      min-height: 100vh;
    }

    .dash-wrap {
      display: flex;
      flex-direction: column;
      gap: 20px;
    }

    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(6, 1fr);
      gap: 16px;
    }

    .kpi-card-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .kpi-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
      padding: 16px;
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .kpi-card:hover {
      box-shadow: 0 4px 12px rgba(15, 23, 42, .1);
    }

    .kpi-icon {
      height: 48px;
      width: 48px;
      border-radius: 12px;
      display: grid;
      place-items: center;
      background: #f3f4f6;
      margin-bottom: 10px;
    }

    .kpi-icon i {
      font-size: 22px;
      color: #ef4444;
    }

    .kpi-meta {
      display: flex;
      flex-direction: column;
      gap: 4px;
    }

    .kpi-meta h3 {
      font-size: 12px;
      color: #6b7280;
      margin: 0;
      font-weight: 600;
    }

    .kpi-value {
      font-size: 24px;
      font-weight: 700;
      color: #111827;
      margin: 0;
    }

    .compact-grid {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 18px;
    }

    .double-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 18px;
    }

    .panel {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
      padding: 16px;
    }

    .panel-head {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
      margin-bottom: 12px;
    }

    .panel-head h2 {
      margin: 0;
      font-size: 16px;
      color: #111827;
      font-weight: 700;
    }

    .panel-link {
      font-size: 13px;
      font-weight: 600;
      color: #2563eb;
      text-decoration: none;
      white-space: nowrap;
    }

    .panel-link:hover {
      text-decoration: underline;
    }

    .table-wrap {
      overflow-x: auto;
      margin-top: 8px;
    }

    .table {
      width: 100%;
      border-collapse: collapse;
    }

    .table th,
    .table td {
      text-align: left;
      padding: 12px 8px;
      border-bottom: 1px solid #e5e7eb;
      font-size: 13px;
      vertical-align: top;
    }

    .table th {
      font-weight: 700;
      color: #374151;
      background: #f9fafb;
    }

    .row-link {
      text-decoration: none;
      color: inherit;
      display: block;
    }

    .row-link:hover {
      color: #2563eb;
    }

    .mini-list {
      display: flex;
      flex-direction: column;
      gap: 8px;
    }

    .mini-item {
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      padding: 12px;
      background: #fff;
      text-decoration: none;
      color: inherit;
      display: block;
      transition: .15s ease;
      cursor: pointer;
    }

    .mini-item:hover {
      border-color: #c7d2fe;
      background: #f8faff;
      box-shadow: 0 2px 6px rgba(37, 99, 235, 0.1);
    }

    .mini-item-top {
      display: flex;
      justify-content: space-between;
      gap: 10px;
      align-items: flex-start;
      margin-bottom: 6px;
    }

    .mini-item-title {
      margin: 0;
      font-size: 13px;
      font-weight: 700;
      color: #111827;
      line-height: 1.3;
    }

    .mini-item-sub {
      margin: 2px 0 0;
      font-size: 12px;
      color: #6b7280;
      line-height: 1.35;
    }

    .mini-badge {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      font-size: 11px;
      font-weight: 700;
      white-space: nowrap;
      background: #eef2ff;
      color: #3730a3;
    }

    .mini-badge.warn {
      background: #fff7ed;
      color: #c2410c;
    }

    .mini-badge.danger {
      background: #fef2f2;
      color: #b91c1c;
    }

    .mini-badge.success {
      background: #ecfdf5;
      color: #047857;
    }

    .status-pill {
      display: inline-block;
      padding: 4px 10px;
      border-radius: 999px;
      background: #f3f4f6;
      font-size: 11px;
      font-weight: 700;
      color: #374151;
    }

    .empty-state {
      color: #6b7280;
      font-size: 13px;
      margin: 0;
      padding: 6px 0;
    }

    .filters-panel {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 2px 8px rgba(15, 23, 42, .06);
      padding: 16px;
    }

    .filters-panel h3 {
      margin: 0 0 12px;
      font-size: 16px;
      font-weight: 700;
      color: #111827;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .filters-panel h3 i {
      font-size: 18px;
      color: #6b7280;
    }

    .filters {
      display: grid;
      grid-template-columns: 2fr 1fr 1fr 1fr 1fr 1fr auto;
      gap: 12px;
      align-items: end;
    }

    .filters input,
    .filters select {
      width: 100%;
      padding: 8px 10px;
      border: 1px solid #e5e7eb;
      border-radius: 10px;
      background: #fff;
      font-size: 13px;
      color: #111827;
    }

    .filters label {
      display: block;
      font-size: 11px;
      font-weight: 600;
      color: #6b7280;
      margin-bottom: 4px;
    }

    .filters>div {
      display: flex;
      flex-direction: column;
    }

    .btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      padding: 8px 14px;
      border: none;
      border-radius: 10px;
      font-size: 13px;
      font-weight: 600;
      text-decoration: none;
      cursor: pointer;
    }

    .btn-dark {
      background: #111827;
      color: #fff;
    }

    .btn-dark:hover {
      background: #1f2937;
    }

    .btn-light {
      background: #f3f4f6;
      color: #111827;
      border: 1px solid #e5e7eb;
    }

    .btn-light:hover {
      background: #e5e7eb;
    }

    .queue-item {
      padding: 12px;
      border: 1px solid #e5e7eb;
      border-radius: 12px;
      background: #fafafa;
      margin-bottom: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 12px;
    }

    .queue-item:last-child {
      margin-bottom: 0;
    }

    .queue-item-content {
      flex: 1;
    }

    .queue-item-icon {
      font-size: 18px;
      color: #6b7280;
    }

    .analytics-card {
      background: #fff;
      border-radius: 12px;
      padding: 0;
    }

    .analytics-card h3 {
      margin: 0 0 8px;
      font-size: 13px;
      font-weight: 700;
      color: #111827;
    }

    .analytics-list {
      margin: 0;
      padding: 0;
      list-style: none;
    }

    .analytics-list li {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      padding: 6px 0;
      border-bottom: 1px solid #f1f5f9;
      font-size: 12px;
    }

    .analytics-list li:last-child {
      border-bottom: none;
    }

    @media (max-width: 1200px) {
      .filters {
        grid-template-columns: 1fr 1fr;
      }

      .compact-grid,
      .double-grid {
        grid-template-columns: 1fr;
      }

      .kpi-grid {
        grid-template-columns: repeat(3, minmax(0, 1fr));
      }
    }

    @media (max-width: 768px) {
      .filters {
        grid-template-columns: 1fr;
      }

      .kpi-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
      }
    }

    @media (max-width: 600px) {
      .kpi-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
    <header class="topbar">
      <div>
        <h1 class="page-title">Complaints</h1>
        <p class="subtitle">Track complaints, assignments, SLA status, and resolutions.</p>
        <?php require APP_ROOT . '/views/partials/lang-switcher.php'; ?>
      </div>

      <a class="user-chip user-chip--link" href="<?= $B ?>/admin/profile" aria-label="Open profile">
        <div class="avatar"><i class="fa-solid fa-user"></i></div>
        <span><?= e($adminName) ?></span>
      </a>
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
          <div>
            <label><i class="fa-solid fa-gauge"></i> SLA</label>
            <select name="sla">
              <option value="">All</option>
              <option value="healthy" <?= (($f['sla'] ?? '') === 'healthy') ? 'selected' : '' ?>>Healthy</option>
              <option value="due_soon" <?= (($f['sla'] ?? '') === 'due_soon') ? 'selected' : '' ?>>Due Soon</option>
              <option value="breached" <?= (($f['sla'] ?? '') === 'breached') ? 'selected' : '' ?>>Breached</option>
            </select>
          </div>
          <div style="display:flex; gap:8px;">
            <button class="btn btn-dark" type="submit"><i class="fa-solid fa-check"></i> Apply</button>
            <a class="btn btn-light" href="<?= $B ?>/admin/admin-viewcomplaints"><i class="fa-solid fa-undo"></i>
              Clear</a>
          </div>
        </form>
      </div>

      <div class="compact-grid">
        <section class="panel">
          <div class="panel-head">
            <h2><i class="fa-solid fa-tasks"></i> Assignment Queue</h2>
          </div>
          <?php if (!empty($assignmentQueue)): ?>
            <div class="mini-list">
              <?php foreach ($assignmentQueue as $q): ?>
                <a class="mini-item" href="<?= $B ?>/admin/admin-viewcomplaints/show?id=<?= (int) $q['complaint_id'] ?>">
                  <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:10px;">
                    <div style="flex:1;">
                      <p class="mini-item-title">#<?= (int) $q['complaint_id'] ?> — <?= e(firstWords($q['subject'], 8)) ?>
                      </p>
                      <p class="mini-item-sub"><i class="fa-solid fa-user"></i> <?= e($q['customer_name']) ?></p>
                      <p class="mini-item-sub"><i class="fa-solid fa-building"></i> <?= e($q['branch_name'] ?? '—') ?></p>
                    </div>
                    <div style="text-align:right; white-space:nowrap;">
                      <?= priority($q['priority']) ?>
                      <p class="mini-item-sub"><i class="fa-solid fa-clock"></i> <?= e($q['aging_label']) ?></p>
                    </div>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="empty-state"><i class="fa-solid fa-inbox"></i> No unassigned complaints in queue.</p>
          <?php endif; ?>
        </section>

        <section class="panel">
          <div class="panel-head">
            <h2><i class="fa-solid fa-chart-bar"></i> Analytics</h2>
          </div>
          <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div class="analytics-card">
              <h3><i class="fa-solid fa-building"></i> By Branch</h3>
              <ul class="analytics-list">
                <?php if (!empty($analytics['by_branch'])): ?>
                  <?php foreach (array_slice($analytics['by_branch'], 0, 5) as $row): ?>
                    <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int) $row['total'] ?></strong></li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li><span class="empty-state">No data</span></li>
                <?php endif; ?>
              </ul>
            </div>
            <div class="analytics-card">
              <h3><i class="fa-solid fa-circle"></i> By Status</h3>
              <ul class="analytics-list">
                <?php if (!empty($analytics['by_status'])): ?>
                  <?php foreach (array_slice($analytics['by_status'], 0, 5) as $row): ?>
                    <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int) $row['total'] ?></strong></li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li><span class="empty-state">No data</span></li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </section>
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
                <th>SLA</th>
                <th>Aging</th>
                <th>Escalation</th>
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
                    <td><?= slaStatus($r['sla_status']) ?></td>
                    <td><?= htmlspecialchars($r['aging_label']) ?></td>
                    <td><?= escalationFlag($r['escalated']) ?></td>
                    <td><?= assignedUserInfo($r) ?></td>
                    <td><?= htmlspecialchars($r['created_at']) ?></td>
                    <td>
                      <?= linkBtn($B . '/admin/admin-viewcomplaints/show?id=' . (int) $r['complaint_id'], 'View', 'fa-eye') ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="12" class="empty-state"><i class="fa-solid fa-inbox"></i> No complaints found.</td>
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