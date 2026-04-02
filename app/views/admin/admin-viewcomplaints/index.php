<?php
$B = rtrim(BASE_URL, '/');
$current = $current ?? 'complaints';
$f = $filters ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Complaints') ?></title>

  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    .main-content{margin-left:280px;padding:30px;background:#f4f5f7;min-height:100vh;}
    .page-title{margin:0 0 18px}
    .cards{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:14px;margin-bottom:20px}
    .card-mini,.card,.analytics-card{background:#fff;border-radius:16px;box-shadow:0 1px 4px rgba(15,23,42,.08);padding:16px}
    .card-mini .label{font-size:12px;color:#6b7280}
    .card-mini .value{font-size:26px;font-weight:800;margin-top:6px}
    .filters{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr 1fr auto;gap:10px;align-items:end}
    .filters input,.filters select{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:10px;background:#fff}
    .filters .btn{padding:10px 14px;border:none;border-radius:10px;text-decoration:none;cursor:pointer}
    .btn-dark{background:#111827;color:#fff}
    .btn-light{background:#e5e7eb;color:#111827}
    .grid-two{display:grid;grid-template-columns:1.2fr .8fr;gap:18px;margin:20px 0}
    .queue-item{border:1px solid #e5e7eb;border-radius:12px;padding:12px;margin-bottom:10px;background:#fafafa}
    .queue-item:last-child{margin-bottom:0}
    .queue-item .top{display:flex;justify-content:space-between;gap:12px}
    .table-wrap{overflow:auto}
    table{width:100%;border-collapse:collapse;background:#fff}
    th,td{padding:12px 10px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top}
    th{font-size:13px;text-transform:uppercase;color:#6b7280}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700}
    .badge.open{background:#fef2f2;color:#b91c1c}
    .badge.in_progress{background:#fff7ed;color:#c2410c}
    .badge.resolved{background:#ecfdf5;color:#047857}
    .badge.closed{background:#eef2ff;color:#3730a3}
    .priority.low{color:#2563eb;font-weight:700}
    .priority.medium{color:#d97706;font-weight:700}
    .priority.high{color:#dc2626;font-weight:700}
    .sla.healthy{color:#047857;font-weight:700}
    .sla.due_soon{color:#c2410c;font-weight:700}
    .sla.breached{color:#b91c1c;font-weight:700}
    .flag{display:inline-block;padding:3px 8px;border-radius:999px;font-size:11px;font-weight:700}
    .flag.escalated{background:#fee2e2;color:#991b1b}
    .flag.normal{background:#e5e7eb;color:#374151}
    .analytics-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .analytics-card h3{margin:0 0 10px}
    .analytics-list{margin:0;padding:0;list-style:none}
    .analytics-list li{display:flex;justify-content:space-between;gap:10px;padding:8px 0;border-bottom:1px solid #f1f5f9}
    .analytics-list li:last-child{border-bottom:none}
    .muted{color:#6b7280}
    .link-btn{display:inline-flex;align-items:center;gap:8px;text-decoration:none;background:#111827;color:#fff;padding:8px 12px;border-radius:10px}
    @media (max-width: 1280px){
      .cards{grid-template-columns:repeat(2,minmax(0,1fr))}
      .filters{grid-template-columns:1fr 1fr}
      .grid-two,.analytics-grid{grid-template-columns:1fr}
      .main-content{margin-left:0;padding:18px}
    }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <h1 class="page-title">Complaints Management</h1>

  <section class="cards">
    <div class="card-mini"><div class="label">Total</div><div class="value"><?= (int)($summary['total_count'] ?? 0) ?></div></div>
    <div class="card-mini"><div class="label">Open</div><div class="value"><?= (int)($summary['open_count'] ?? 0) ?></div></div>
    <div class="card-mini"><div class="label">In Progress</div><div class="value"><?= (int)($summary['progress_count'] ?? 0) ?></div></div>
    <div class="card-mini"><div class="label">Resolved / Closed</div><div class="value"><?= (int)($summary['done_count'] ?? 0) ?></div></div>
    <div class="card-mini"><div class="label">Urgent Open</div><div class="value"><?= (int)($summary['urgent_open_count'] ?? 0) ?></div></div>
    <div class="card-mini"><div class="label">Unassigned Queue</div><div class="value"><?= (int)($summary['unassigned_count'] ?? 0) ?></div></div>
  </section>

  <section class="card">
    <form method="GET" action="<?= $B ?>/admin/admin-viewcomplaints" class="filters">
      <div>
        <label class="muted">Search</label>
        <input type="text" name="q" value="<?= htmlspecialchars($f['search'] ?? '') ?>" placeholder="Subject, customer, branch, vehicle, service">
      </div>

      <div>
        <label class="muted">Status</label>
        <select name="status">
          <option value="">All</option>
          <?php foreach (['open'=>'Open','in_progress'=>'In Progress','resolved'=>'Resolved','closed'=>'Closed'] as $k => $v): ?>
            <option value="<?= $k ?>" <?= (($f['status'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted">Priority</label>
        <select name="priority">
          <option value="">All</option>
          <?php foreach (['high'=>'High','medium'=>'Medium','low'=>'Low'] as $k => $v): ?>
            <option value="<?= $k ?>" <?= (($f['priority'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted">Branch</label>
        <select name="branch_id">
          <option value="">All</option>
          <?php foreach (($branches ?? []) as $b): ?>
            <option value="<?= (int)$b['branch_id'] ?>" <?= ((string)($f['branch_id'] ?? '') === (string)$b['branch_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($b['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted">Assigned</label>
        <select name="assigned_to">
          <option value="">All</option>
          <option value="unassigned" <?= (($f['assigned_to'] ?? '') === 'unassigned') ? 'selected' : '' ?>>Unassigned</option>
          <?php foreach (($assignableUsers ?? []) as $u): ?>
            <option value="<?= (int)$u['user_id'] ?>" <?= ((string)($f['assigned_to'] ?? '') === (string)$u['user_id']) ? 'selected' : '' ?>>
              <?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['role']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div>
        <label class="muted">SLA</label>
        <select name="sla">
          <option value="">All</option>
          <option value="healthy" <?= (($f['sla'] ?? '') === 'healthy') ? 'selected' : '' ?>>Healthy</option>
          <option value="due_soon" <?= (($f['sla'] ?? '') === 'due_soon') ? 'selected' : '' ?>>Due Soon</option>
          <option value="breached" <?= (($f['sla'] ?? '') === 'breached') ? 'selected' : '' ?>>Breached</option>
        </select>
      </div>

      <div style="display:flex;gap:10px;">
        <button class="btn btn-dark" type="submit">Filter</button>
        <a class="btn btn-light" href="<?= $B ?>/admin/admin-viewcomplaints">Reset</a>
      </div>
    </form>
  </section>

  <section class="grid-two">
    <div class="card">
      <h2 style="margin-top:0;">Assignment Queue</h2>
      <?php if (!empty($assignmentQueue)): ?>
        <?php foreach ($assignmentQueue as $q): ?>
          <div class="queue-item">
            <div class="top">
              <div>
                <strong>#<?= (int)$q['complaint_id'] ?> — <?= htmlspecialchars($q['subject']) ?></strong><br>
                <span class="muted"><?= htmlspecialchars($q['customer_name']) ?> · <?= htmlspecialchars($q['branch_name'] ?? '—') ?></span>
              </div>
              <div style="text-align:right;">
                <div class="priority <?= htmlspecialchars($q['priority']) ?>"><?= htmlspecialchars(ucfirst($q['priority'])) ?></div>
                <div class="muted"><?= htmlspecialchars($q['aging_label']) ?></div>
              </div>
            </div>
            <div style="margin-top:10px;">
              <a class="link-btn" href="<?= $B ?>/admin/admin-viewcomplaints/show?id=<?= (int)$q['complaint_id'] ?>">Open</a>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p class="muted">No unassigned complaints in queue.</p>
      <?php endif; ?>
    </div>

    <div class="card">
      <h2 style="margin-top:0;">Analytics Snapshot</h2>
      <div class="analytics-grid">
        <div class="analytics-card">
          <h3>By Branch</h3>
          <ul class="analytics-list">
            <?php foreach (($analytics['by_branch'] ?? []) as $row): ?>
              <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int)$row['total'] ?></strong></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="analytics-card">
          <h3>By Staff</h3>
          <ul class="analytics-list">
            <?php foreach (($analytics['by_staff'] ?? []) as $row): ?>
              <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int)$row['total'] ?></strong></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="analytics-card">
          <h3>By Service</h3>
          <ul class="analytics-list">
            <?php foreach (($analytics['by_service'] ?? []) as $row): ?>
              <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int)$row['total'] ?></strong></li>
            <?php endforeach; ?>
          </ul>
        </div>

        <div class="analytics-card">
          <h3>By Status / Priority</h3>
          <ul class="analytics-list">
            <?php foreach (($analytics['by_status'] ?? []) as $row): ?>
              <li><span>Status: <?= htmlspecialchars($row['label']) ?></span><strong><?= (int)$row['total'] ?></strong></li>
            <?php endforeach; ?>
            <?php foreach (($analytics['by_priority'] ?? []) as $row): ?>
              <li><span>Priority: <?= htmlspecialchars($row['label']) ?></span><strong><?= (int)$row['total'] ?></strong></li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>
  </section>

  <section class="card">
    <h2 style="margin-top:0;">All Complaints</h2>

    <div class="table-wrap">
      <table>
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
                <td>#<?= (int)$r['complaint_id'] ?></td>
                <td>
                  <strong><?= htmlspecialchars($r['subject']) ?></strong><br>
                  <span class="muted"><?= htmlspecialchars(mb_strimwidth((string)$r['description'], 0, 70, '...')) ?></span>
                </td>
                <td>
                  <?= htmlspecialchars($r['customer_name']) ?><br>
                  <span class="muted"><?= htmlspecialchars($r['customer_code'] ?? '') ?></span>
                </td>
                <td>
                  <?= htmlspecialchars($r['branch_name'] ?? '—') ?><br>
                  <span class="muted"><?= htmlspecialchars($r['service_name'] ?? 'No linked service') ?></span>
                </td>
                <td><span class="priority <?= htmlspecialchars($r['priority']) ?>"><?= htmlspecialchars(ucfirst($r['priority'])) ?></span></td>
                <td><span class="badge <?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r['status']))) ?></span></td>
                <td><span class="sla <?= htmlspecialchars($r['sla_status']) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r['sla_status']))) ?></span></td>
                <td><?= htmlspecialchars($r['aging_label']) ?></td>
                <td>
                  <?php if (!empty($r['escalated'])): ?>
                    <span class="flag escalated">Escalated</span>
                  <?php else: ?>
                    <span class="flag normal">Normal</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?= htmlspecialchars($r['assigned_user_name'] ?? 'Unassigned') ?><br>
                  <?php if (!empty($r['assigned_user_role'])): ?>
                    <span class="muted"><?= htmlspecialchars($r['assigned_user_role']) ?></span>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($r['created_at']) ?></td>
                <td>
                  <a class="link-btn" href="<?= $B ?>/admin/admin-viewcomplaints/show?id=<?= (int)$r['complaint_id'] ?>">View</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr>
              <td colspan="12" class="muted">No complaints found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>