<?php
/** @var string $base */
/** @var array $managers */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Branch</title>
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
<link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">

  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar { position:fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
    .form { max-width:900px; background:#fff; border:1px solid #eee; border-radius:10px; padding:20px; }
    .grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px; }
    .label { font-size:12px; color:#666; margin-bottom:2px; display:block; }
    .input, select, textarea { width:100%; padding:10px 12px; border:1px solid #ccc; border-radius:6px; font-size:14px; }
    .btns { margin-top:18px; display:flex; gap:10px; }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Add New Branch</h2>

    <form class="form" method="post" action="<?= htmlspecialchars($base . '/admin/branches', ENT_QUOTES, 'UTF-8') ?>">
      <div class="grid">
        <div>
          <label class="label">Branch Code</label>
          <input class="input" name="code" placeholder="e.g. BR010" required>
        </div>

        <div>
          <label class="label">Status</label>
          <select class="input" name="status">
            <option value="active" selected>Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>

        <div>
          <label class="label">Branch Name</label>
          <input class="input" name="name" placeholder="Branch name" required>
        </div>

        <div>
          <label class="label">City / Location</label>
          <input class="input" name="city" placeholder="City" required>
        </div>

        <div>
          <label class="label">Manager</label>
          <select class="input" name="manager">
            <option value="">— None —</option>
            <?php foreach (($managers ?? []) as $m): ?>
              <?php
                $id   = (int)($m['manager_id'] ?? 0);
                $code = (string)($m['manager_code'] ?? '');
                $name = trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''));
                $label = $code ? "$code — $name" : $name;
              ?>
              <option value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="label">Phone</label>
          <input class="input" name="phone" placeholder="e.g. +94 11 234 5678">
        </div>

        <div>
          <label class="label">Email</label>
          <input class="input" type="email" name="email" placeholder="example@autonexus.com">
        </div>

        <div>
          <label class="label">Created At</label>
          <input class="input" type="date" name="created_at" value="<?= htmlspecialchars(date('Y-m-d')) ?>">
        </div>

        <div>
          <label class="label">Capacity</label>
          <input class="input" type="number" name="capacity" min="0" value="0">
        </div>

        <div>
          <label class="label">Staff Count</label>
          <input class="input" type="number" name="staff" min="0" value="0">
        </div>

        <div style="grid-column:1 / -1;">
          <label class="label">Working Hours / Address</label>
          <input class="input" name="working_hours" placeholder="e.g. Mon–Fri 08:00–17:00 or address">
        </div>

        <div style="grid-column:1 / -1;">
          <label class="label">Notes</label>
          <textarea class="input" name="notes" rows="3" placeholder="Optional notes..."></textarea>
        </div>
      </div>

      <div class="btns">
        <button type="submit" class="btn-primary">
          <i class="fas fa-plus"></i> Create Branch
        </button>
        <a href="<?= htmlspecialchars($base . '/admin/branches', ENT_QUOTES, 'UTF-8') ?>" class="btn-secondary">
          Cancel
        </a>
      </div>
    </form>
  </main>
</body>
</html>
