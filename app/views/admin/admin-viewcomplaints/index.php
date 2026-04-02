<?php
/** @var array $records */
/** @var array $branches */
/** @var array $assignableUsers */
/** @var array $filters */
/** @var string $pageTitle */
/** @var string $current */

$B = rtrim(BASE_URL, '/');
$current = $current ?? 'complaints';
$filters = $filters ?? [];
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
    .page-header{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-bottom:18px;}
    .page-title{margin:0;font-size:28px;color:#111827;}
    .subtitle{margin:4px 0 0;color:#6b7280;font-size:14px;}
    .card{background:#fff;border-radius:16px;box-shadow:0 1px 4px rgba(15,23,42,.08);padding:18px;}
    .filters-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr 1fr auto;gap:12px;align-items:end;}
    .field label{display:block;font-size:13px;font-weight:600;color:#374151;margin-bottom:6px;}
    .field input,.field select,.field textarea{width:100%;padding:10px 12px;border:1px solid #d1d5db;border-radius:10px;font-size:14px;background:#fff;}
    .btn{padding:10px 14px;border:none;border-radius:10px;cursor:pointer;font-size:14px;text-decoration:none;display:inline-flex;align-items:center;gap:8px;}
    .btn-primary{background:#111827;color:#fff;}
    .btn-secondary{background:#e5e7eb;color:#111827;}
    .table-wrap{margin-top:18px;overflow:auto;}
    table{width:100%;border-collapse:collapse;}
    th,td{padding:12px 10px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:14px;vertical-align:top;}
    th{background:#f9fafb;color:#374151;}
    .badge{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;}
    .badge.open{background:#fef2f2;color:#b91c1c;}
    .badge.in_progress{background:#fff7ed;color:#c2410c;}
    .badge.resolved{background:#ecfdf5;color:#047857;}
    .badge.closed{background:#eef2ff;color:#3730a3;}
    .priority.low{color:#2563eb;font-weight:700;}
    .priority.medium{color:#d97706;font-weight:700;}
    .priority.high{color:#dc2626;font-weight:700;}
    .link-btn{color:#2563eb;text-decoration:none;font-weight:600;}
    .link-btn:hover{text-decoration:underline;}
    .muted{color:#6b7280;}
    @media (max-width: 1200px){
      .filters-grid{grid-template-columns:1fr 1fr;}
      .main-content{margin-left:0;padding:20px;}
    }
  </style>
</head>
<body>
<?php include APP_ROOT . '/views/layouts/admin-sidebar/sidebar.php'; ?>

<main class="main-content">
  <div class="page-header">
    <div>
      <h1 class="page-title">Complaints</h1>
      <p class="subtitle">View, track, assign, and resolve customer complaints</p>
    </div>
  </div>

  <section class="card">
    <form method="GET" action="<?= $B ?>/admin/admin-viewcomplaints">
      <div class="filters-grid">
        <div class="field">
          <label>Search</label>
          <input type="text" name="q" value="<?= htmlspecialchars($filters['search'] ?? '') ?>" placeholder="Subject, customer, branch, vehicle...">
        </div>

        <div class="field">
          <label>Status</label>
          <select name="status">
            <option value="">All</option>
            <?php foreach (['open' => 'Open', 'in_progress' => 'In Progress', 'resolved' => 'Resolved', 'closed' => 'Closed'] as $value => $label): ?>
              <option value="<?= $value ?>" <?= (($filters['status'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>Priority</label>
          <select name="priority">
            <option value="">All</option>
            <?php foreach (['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'] as $value => $label): ?>
              <option value="<?= $value ?>" <?= (($filters['priority'] ?? '') === $value) ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>Branch</label>
          <select name="branch_id">
            <option value="">All</option>
            <?php foreach ($branches as $b): ?>
              <option value="<?= (int)$b['branch_id'] ?>" <?= ((string)($filters['branch_id'] ?? '') === (string)$b['branch_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($b['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label>Assigned To</label>
          <select name="assigned_to">
            <option value="">All</option>
            <?php foreach ($assignableUsers as $u): ?>
              <option value="<?= (int)$u['user_id'] ?>" <?= ((string)($filters['assigned_to'] ?? '') === (string)$u['user_id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($u['full_name']) ?> (<?= htmlspecialchars($u['role']) ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div style="display:flex;gap:8px;">
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-filter"></i> Filter</button>
          <a href="<?= $B ?>/admin/admin-viewcomplaints" class="btn btn-secondary">Reset</a>
        </div>
      </div>
    </form>

    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Customer</th>
            <th>Branch</th>
            <th>Vehicle</th>
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
                <td>#<?= (int)$r['complaint_id'] ?></td>
                <td>
                  <strong><?= htmlspecialchars($r['subject']) ?></strong><br>
                  <span class="muted"><?= htmlspecialchars(mb_strimwidth((string)$r['description'], 0, 70, '...')) ?></span>
                </td>
                <td>
                  <?= htmlspecialchars($r['customer_name']) ?><br>
                  <span class="muted"><?= htmlspecialchars($r['customer_code'] ?? '') ?></span>
                </td>
                <td><?= htmlspecialchars($r['branch_name'] ?? '—') ?></td>
                <td><?= htmlspecialchars(($r['license_plate'] ?? '') !== '' ? $r['license_plate'] : ($r['vehicle_code'] ?? '—')) ?></td>
                <td><span class="priority <?= htmlspecialchars($r['priority']) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r['priority']))) ?></span></td>
                <td><span class="badge <?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $r['status']))) ?></span></td>
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
              <td colspan="10" class="muted">No complaints found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</main>
</body>
</html>