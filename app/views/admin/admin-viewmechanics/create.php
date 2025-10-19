<?php $current = $current ?? 'mechanics'; $B = rtrim(BASE_URL, '/'); $old = $old ?? []; $errors = $errors ?? []; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Mechanic</title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    .main-content{margin-left:260px;padding:30px;background:#fff;min-height:100vh}
    .form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:16px}
    .error{background:#ffe6e6;color:#991b1b;border:1px solid #fca5a5;padding:10px;border-radius:8px;margin-bottom:14px}
    .btn-row{margin-top:16px;display:flex;gap:10px}
    .btn,button{border:none;background:#111;color:#fff;padding:10px 14px;border-radius:8px;cursor:pointer}
    .btn.secondary{background:#666}
    label{font-weight:600;margin-bottom:4px;display:block}
    input,select,textarea{width:100%;padding:10px;border:1px solid #ddd;border-radius:8px}
  </style>
</head>
<body>
   <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

<main class="main-content">
  <h2>Create Mechanic</h2>

  <?php if ($errors): ?>
    <div class="error"><ul style="margin:0;padding-left:18px;">
      <?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?>
    </ul></div>
  <?php endif; ?>

  <form method="post" action="<?= rtrim(BASE_URL,'/') ?>/admin/mechanics">

    <div class="form-grid">
      <div>
        <label>Mechanic Code</label>
        <input name="mechanic_code" value="<?= htmlspecialchars($old['mechanic_code'] ?? '') ?>" placeholder="MEC010">
        <small>Leave empty to auto-generate.</small>
      </div>

      <div>
  <label>Branch</label>
  <select name="branch_id" required>
    <option value="">-- Select Branch --</option>
    <?php foreach ($branches as $b): ?>
      <option value="<?= $b['branch_id'] ?>"
        <?= (($old['branch_id'] ?? '') == $b['branch_id']) ? 'selected' : '' ?>>
        [<?= htmlspecialchars($b['branch_code']) ?>] <?= htmlspecialchars($b['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>


      <div>
        <label>Specialization</label>
        <input name="specialization" value="<?= htmlspecialchars($old['specialization'] ?? '') ?>">
      </div>

      <div>
        <label>Experience (years)</label>
        <input type="number" min="0" name="experience_years" value="<?= htmlspecialchars($old['experience_years'] ?? '0') ?>">
      </div>

      <div>
        <label>Mechanic Status</label>
        <select name="mech_status">
          <option value="active"   <?= (($old['mech_status'] ?? 'active')==='active')?'selected':''; ?>>Active</option>
          <option value="inactive" <?= (($old['mech_status'] ?? '')==='inactive')?'selected':''; ?>>Inactive</option>
        </select>
      </div>

      <div>
        <label>User Status</label>
        <select name="user_status">
          <option value="active"   <?= (($old['user_status'] ?? 'active')==='active')?'selected':''; ?>>Active</option>
          <option value="inactive" <?= (($old['user_status'] ?? '')==='inactive')?'selected':''; ?>>Inactive</option>
          <option value="pending"  <?= (($old['user_status'] ?? '')==='pending')?'selected':''; ?>>Pending</option>
        </select>
      </div>

      <div>
        <label>First Name *</label>
        <input name="first_name" required value="<?= htmlspecialchars($old['first_name'] ?? '') ?>">
      </div>

      <div>
        <label>Last Name *</label>
        <input name="last_name" required value="<?= htmlspecialchars($old['last_name'] ?? '') ?>">
      </div>

      <div>
        <label>Email</label>
        <input type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>">
      </div>

      <div>
        <label>Phone</label>
        <input name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
      </div>

      <div style="grid-column:1/-1;">
        <label>Initial Password (optional)</label>
        <input type="text" name="password" placeholder="default: autonexus" value="<?= htmlspecialchars($old['password'] ?? '') ?>">
      </div>
    </div>

    <div class="btn-row">
      <button type="submit"><i class="fa fa-plus"></i> Create</button>
      <a class="btn secondary" href="<?= $B ?>/admin-viewmechanics">Cancel</a>
    </div>
  </form>
</main>
</body>
</html>
