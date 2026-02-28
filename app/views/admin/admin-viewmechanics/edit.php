<?php $current = $current ?? 'mechanics'; $B = rtrim(BASE_URL, '/'); $errors = $errors ?? []; $m = $mechanic; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Mechanic</title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
  
  </style>
</head>
<body>
<?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

<main class="main-content">
  <h2>Edit Mechanic</h2>

  <?php if ($errors): ?>
    <div class="error"><ul style="margin:0;padding-left:18px;">
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul></div>
  <?php endif; ?>

  <form method="post" action="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>">
    <div class="form-grid">
      <div>
        <label>ID</label>
        <input value="<?= htmlspecialchars($m['mechanic_id']) ?>" readonly>
      </div>

      <div>
        <label>Mechanic Code</label>
        <input name="mechanic_code" value="<?= htmlspecialchars($m['mechanic_code'] ?? '') ?>">
      </div>

      <div>
        <label>First Name</label>
        <input name="first_name" value="<?= htmlspecialchars($m['first_name'] ?? '') ?>">
      </div>

      <div>
        <label>Last Name</label>
        <input name="last_name" value="<?= htmlspecialchars($m['last_name'] ?? '') ?>">
      </div>

      <div>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($m['email'] ?? '') ?>">
      </div>

      <div>
        <label>Phone</label>
        <input name="phone" value="<?= htmlspecialchars($m['phone'] ?? '') ?>">
      </div>

      <div>
  <label>Branch</label>
  <select name="branch_id" required>
    <option value="">-- Select Branch --</option>
    <?php foreach ($branches as $b): ?>
      <option value="<?= $b['branch_id'] ?>"
        <?= (($m['branch_id'] ?? '') == $b['branch_id']) ? 'selected' : '' ?>>
        [<?= htmlspecialchars($b['branch_code']) ?>] <?= htmlspecialchars($b['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>


      <div>
        <label>Specialization</label>
        <input name="specialization" value="<?= htmlspecialchars($m['specialization'] ?? '') ?>">
      </div>

      <div>
        <label>Experience (years)</label>
        <input type="number" min="0" name="experience_years" value="<?= htmlspecialchars($m['experience_years'] ?? '0') ?>">
      </div>

      <div>
        <label>Mechanic Status</label>
        <select name="mech_status">
          <option value="active"   <?= (($m['mech_status'] ?? 'active')==='active')?'selected':''; ?>>Active</option>
          <option value="inactive" <?= (($m['mech_status'] ?? '')==='inactive')?'selected':''; ?>>Inactive</option>
        </select>
      </div>

      <div>
        <label>User Status</label>
        <select name="user_status">
          <option value="active"   <?= (($m['user_status'] ?? 'active')==='active')?'selected':''; ?>>Active</option>
          <option value="inactive" <?= (($m['user_status'] ?? '')==='inactive')?'selected':''; ?>>Inactive</option>
          <option value="pending"  <?= (($m['user_status'] ?? '')==='pending')?'selected':''; ?>>Pending</option>
        </select>
      </div>

      <div style="grid-column:1/-1;">
        <label>Reset Password (optional)</label>
        <input type="text" name="password" placeholder="Leave blank to keep current">
      </div>
    </div>

    <div class="btn-row">
      <button type="submit"><i class="fa fa-save"></i> Update</button>
      <a class="btn secondary" href="<?= $B ?>/admin/mechanics/<?= urlencode($m['mechanic_id']) ?>">Cancel</a>
    </div>
  </form>
</main>
</body>
</html>
