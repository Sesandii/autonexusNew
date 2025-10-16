<?php
/** @var array $row */
/** @var array $managers */
/** @var string $base */
$base = rtrim($base ?? BASE_URL, '/');
$current = 'branches';
$curManagerId = (int)($row['manager_id'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Branch <?= htmlspecialchars($row['branch_code'] ?? '') ?></title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar { position:fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
    .form { max-width:900px; }
    .row { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .label { color:#666; font-size:12px; margin-bottom:2px; }
    .input, select, textarea { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px; }
    .btns { margin-top:16px; display:flex; gap:10px; }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Edit Branch</h2>

    <form class="form" method="post" action="<?= htmlspecialchars($base . '/branches/update/' . urlencode((string)$row['branch_code']), ENT_QUOTES, 'UTF-8') ?>">
      <div class="row">
        <div>
          <div class="label">Branch Code</div>
          <input class="input" name="code" value="<?= htmlspecialchars($row['branch_code'] ?? '') ?>" readonly>
        </div>
        <div>
          <div class="label">Status</div>
          <?php $st = $row['status'] ?? 'active'; ?>
          <select name="status" class="input">
            <option value="active"  <?= $st==='active'  ? 'selected':'' ?>>Active</option>
            <option value="inactive"<?= $st==='inactive'? 'selected':'' ?>>Inactive</option>
          </select>
        </div>

        <div>
          <div class="label">Name</div>
          <input class="input" name="name" value="<?= htmlspecialchars($row['name'] ?? '') ?>" required>
        </div>
        <div>
          <div class="label">City</div>
          <input class="input" name="city" value="<?= htmlspecialchars($row['city'] ?? '') ?>" required>
        </div>

        <div>
          <div class="label">Manager</div>
          <select class="input" name="manager">
            <option value="">— None —</option>
            <?php foreach (($managers ?? []) as $m): ?>
              <?php
                $id   = (int)($m['manager_id'] ?? 0);
                $code = (string)($m['manager_code'] ?? '');
                $name = trim(($m['first_name'] ?? '') . ' ' . ($m['last_name'] ?? ''));
                $label = $code ? "$code — $name" : $name;
              ?>
              <option value="<?= htmlspecialchars((string)$id, ENT_QUOTES, 'UTF-8') ?>"
                      <?= $id === $curManagerId ? 'selected' : '' ?>>
                <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <div class="label">Phone</div>
          <input class="input" name="phone" value="<?= htmlspecialchars($row['phone'] ?? '') ?>">
        </div>

        <div>
          <div class="label">Email</div>
          <input class="input" type="email" name="email" value="<?= htmlspecialchars($row['email'] ?? '') ?>">
        </div>
        <div>
          <div class="label">Created At (date)</div>
          <input class="input" type="date" name="created_at" value="<?= htmlspecialchars(substr((string)($row['created_at'] ?? ''),0,10)) ?>">
        </div>

        <div>
          <div class="label">Capacity</div>
          <input class="input" type="number" name="capacity" value="<?= htmlspecialchars((string)($row['capacity'] ?? 0)) ?>" min="0">
        </div>
        <div>
          <div class="label">Staff Count</div>
          <input class="input" type="number" name="staff" value="<?= htmlspecialchars((string)($row['staff_count'] ?? 0)) ?>" min="0">
        </div>

        <div style="grid-column:1 / -1;">
          <div class="label">Address / Working Hours</div>
          <input class="input" name="address_line" value="<?= htmlspecialchars($row['address_line'] ?? '') ?>" placeholder="e.g., Mon–Fri 08:00–17:00 or address">
        </div>

        <div style="grid-column:1 / -1;">
          <div class="label">Notes</div>
          <textarea class="input" name="notes" rows="3"><?= htmlspecialchars($row['notes'] ?? '') ?></textarea>
        </div>
      </div>

      <div class="btns">
        <button type="submit" class="btn-primary">Save Changes</button>
        <a href="<?= htmlspecialchars($base . '/branches', ENT_QUOTES, 'UTF-8') ?>" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </main>
</body>
</html>
