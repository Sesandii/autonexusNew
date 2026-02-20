<?php
/** @var array $row */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Branch <?= htmlspecialchars($row['branch_code'] ?? '') ?> • Details</title>
 <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
<link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar { position:fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
    .card { border:1px solid #eee; border-radius:10px; padding:20px; max-width:900px; background:#fff; }
    .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    .label { font-size:12px; color:#666; }
    .value { font-weight:600; }
    .actions { margin-top:16px; display:flex; gap:10px; }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Branch Details</h2>

    <div class="card">
      <div class="grid">
        <div><div class="label">Branch Code</div><div class="value"><?= htmlspecialchars($row['branch_code'] ?? '') ?></div></div>
        <div><div class="label">Status</div>
             <?php $st = $row['status'] ?? 'active'; ?>
             <div class="value <?= $st==='inactive' ? 'status--inactive':'status--active' ?>"><?= htmlspecialchars($st) ?></div>
        </div>

        <div><div class="label">Name</div><div class="value"><?= htmlspecialchars($row['name'] ?? '') ?></div></div>
        <div><div class="label">City</div><div class="value"><?= htmlspecialchars($row['city'] ?? '') ?></div></div>

        <div><div class="label">Manager ID</div><div class="value"><?= htmlspecialchars($row['manager_id'] ?? '') ?></div></div>
        <div><div class="label">Phone</div><div class="value"><?= htmlspecialchars($row['phone'] ?? '') ?></div></div>

        <div><div class="label">Email</div><div class="value"><?= htmlspecialchars($row['email'] ?? '') ?></div></div>
        <div><div class="label">Created At</div><div class="value"><?= htmlspecialchars($row['created_at'] ?? '') ?></div></div>

        <div><div class="label">Capacity</div><div class="value"><?= htmlspecialchars($row['capacity'] ?? '') ?></div></div>
        <div><div class="label">Staff Count</div><div class="value"><?= htmlspecialchars($row['staff_count'] ?? '') ?></div></div>

        <div style="grid-column:1 / -1;">
          <div class="label">Address / Working Hours</div>
          <div class="value"><?= htmlspecialchars($row['address_line'] ?? '') ?></div>
        </div>

        <div style="grid-column:1 / -1;">
          <div class="label">Notes</div>
          <div class="value"><?= htmlspecialchars($row['notes'] ?? '') ?></div>
        </div>
      </div>

      <div class="actions">
        <a class="btn-secondary" href="<?= htmlspecialchars($base . '/admin/branches', ENT_QUOTES, 'UTF-8') ?>">← Back to list</a>
<a class="btn" href="<?= htmlspecialchars($base . '/admin/branches/' . rawurlencode((string)$row['branch_code']) . '/edit', ENT_QUOTES, 'UTF-8') ?>">Edit</a>
      </div>
    </div>
  </main>
</body>
</html>
