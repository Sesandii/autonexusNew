<?php
$B = rtrim(BASE_URL, '/');
$current = $current ?? 'complaints';
$f = $filters ?? [];
include 'helpers.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Complaints') ?></title>
  <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-sidebar/styles.css">
  <!-- <link rel="stylesheet" href="<?= $B ?>/app/views/layouts/admin-shared/management.css"> -->
  <link rel="stylesheet" href="<?= $B ?>/app/views/admin/admin-viewcomplaints/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

  <main class="main-content">
   <div class="page-header">
  <h1 class="page-title">Complaints</h1>
  <p class="muted" style="margin:0;">Track complaints, assignments, SLA status, and resolutions.</p>
</div>

    <section class="cards">
      <div class="card-mini">
        <div class="label">Total</div>
        <div class="value"><?= (int) ($summary['total_count'] ?? 0) ?></div>
      </div>
      <div class="card-mini">
        <div class="label">Open</div>
        <div class="value"><?= (int) ($summary['open_count'] ?? 0) ?></div>
      </div>
      <div class="card-mini">
        <div class="label">In Progress</div>
        <div class="value"><?= (int) ($summary['progress_count'] ?? 0) ?></div>
      </div>
      <div class="card-mini">
        <div class="label">Resolved / Closed</div>
        <div class="value"><?= (int) ($summary['done_count'] ?? 0) ?></div>
      </div>
      <div class="card-mini">
        <div class="label">Urgent Open</div>
        <div class="value"><?= (int) ($summary['urgent_open_count'] ?? 0) ?></div>
      </div>
      <div class="card-mini">
        <div class="label">Unassigned Queue</div>
        <div class="value"><?= (int) ($summary['unassigned_count'] ?? 0) ?></div>
      </div>
    </section>

    <section class="card">
      <form method="GET" action="<?= $B ?>/admin/admin-viewcomplaints" class="filters">
        <div>
          <label class="muted">Search</label>
          <input type="text" name="q" value="<?= htmlspecialchars($f['search'] ?? '') ?>"
            placeholder="Subject, customer, branch, vehicle, service">
        </div>
        <div>
          <label class="muted">Status</label>
          <select name="status">
            <option value="">All</option>
            <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $k => $v): ?>
              <option value="<?= $k ?>" <?= (($f['status'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="muted">Priority</label>
          <select name="priority">
            <option value="">All</option>
            <?php foreach (['high' => 'High', 'medium' => 'Medium', 'low' => 'Low'] as $k => $v): ?>
              <option value="<?= $k ?>" <?= (($f['priority'] ?? '') === $k) ? 'selected' : '' ?>><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="muted">Branch</label>
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
          <label class="muted">Assigned</label>
          <select name="assigned_to">
            <option value="">All</option>
            <option value="unassigned" <?= (($f['assigned_to'] ?? '') === 'unassigned') ? 'selected' : '' ?>>Unassigned
            </option>
            <?php foreach (($assignableUsers ?? []) as $u): ?>
              <option value="<?= (int) $u['user_id'] ?>" <?= ((string) ($f['assigned_to'] ?? '') === (string) $u['user_id']) ? 'selected' : '' ?>>
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
        <div>
          <button class="btn btn-dark" type="submit">Apply</button>
          <a class="btn btn-light" href="<?= $B ?>/admin/admin-viewcomplaints">Clear</a>
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
                  <strong>#<?= (int) $q['complaint_id'] ?> — <?= htmlspecialchars($q['subject']) ?></strong><br>
                  <span class="muted"><?= htmlspecialchars($q['customer_name']) ?> ·
                    <?= htmlspecialchars($q['branch_name'] ?? '—') ?></span>
                </div>
                <div style="text-align:right;">
                  <?= priority($q['priority']) ?>
                  <div class="muted"><?= htmlspecialchars($q['aging_label']) ?></div>
                </div>
              </div>
              <div style="margin-top:10px;">
                <?= linkBtn($B . '/admin/admin-viewcomplaints/show?id=' . (int) $q['complaint_id'], 'Open', 'fa-arrow-right') ?>
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
                <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int) $row['total'] ?></strong></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="analytics-card">
            <h3>By Staff</h3>
            <ul class="analytics-list">
              <?php foreach (($analytics['by_staff'] ?? []) as $row): ?>
                <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int) $row['total'] ?></strong></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="analytics-card">
            <h3>By Service</h3>
            <ul class="analytics-list">
              <?php foreach (($analytics['by_service'] ?? []) as $row): ?>
                <li><span><?= htmlspecialchars($row['label']) ?></span><strong><?= (int) $row['total'] ?></strong></li>
              <?php endforeach; ?>
            </ul>
          </div>
          <div class="analytics-card">
            <h3>By Status / Priority</h3>
            <ul class="analytics-list">
              <?php foreach (($analytics['by_status'] ?? []) as $row): ?>
                <li><span>Status: <?= htmlspecialchars($row['label']) ?></span><strong><?= (int) $row['total'] ?></strong>
                </li>
              <?php endforeach; ?>
              <?php foreach (($analytics['by_priority'] ?? []) as $row): ?>
                <li><span>Priority:
                    <?= htmlspecialchars($row['label']) ?></span><strong><?= (int) $row['total'] ?></strong></li>
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
                  <td>#<?= (int) $r['complaint_id'] ?></td>
                  <td>
                    <strong><?= htmlspecialchars($r['subject']) ?></strong><br>
                    <span
                      class="muted"><?= htmlspecialchars(mb_strimwidth((string) $r['description'], 0, 70, '...')) ?></span>
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