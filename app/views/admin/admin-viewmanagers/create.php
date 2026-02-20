<?php $base = rtrim(BASE_URL, '/'); $current = 'service-managers'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Manager</title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    .sidebar { position:fixed; top:0; left:0; width:260px; height:100vh; overflow-y:auto; }
    .main-content { margin-left:260px; padding:30px; background:#fff; min-height:100vh; }
    .form { max-width:720px; }
    .row { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:12px; }
    .label { color:#666; font-size:12px; margin-bottom:2px; }
    .input { width:100%; padding:10px 12px; border:1px solid #e5e7eb; border-radius:6px; }
    .input[readonly] { background:#f9fafb; }
    .btns { margin-top:16px; display:flex; gap:10px; }
    .hint { font-size:12px; color:#6b7280; margin-top:4px; }
  </style>
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <h2>Add Manager</h2>

    <?php if (!empty($errors)): ?>
      <div style="color:#b00; margin:10px 0;">
        <?php foreach ($errors as $e): ?>
          <div><?= htmlspecialchars($e) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form class="form" method="post" action="<?= htmlspecialchars($base . '/admin/service-managers', ENT_QUOTES, 'UTF-8') ?>">
      <div class="row">
        <div>
          <div class="label">First name</div>
          <input class="input" name="first_name" value="<?= htmlspecialchars($old['first_name'] ?? '') ?>" required>
        </div>
        <div>
          <div class="label">Last name</div>
          <input class="input" name="last_name" value="<?= htmlspecialchars($old['last_name'] ?? '') ?>" required>
        </div>
        <div>
          <div class="label">Username</div>
          <input class="input" name="username" value="<?= htmlspecialchars($old['username'] ?? '') ?>" required>
        </div>
        <div>
          <div class="label">Email</div>
          <input class="input" type="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required>
        </div>
        <div>
          <div class="label">Phone</div>
          <input class="input" name="phone" value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
        </div>

        <!-- Auto-generated Manager Code (read-only display) -->
        <div>
          <div class="label">Manager Code (auto)</div>
          <input class="input" value="<?= htmlspecialchars($nextCode ?? 'MAN001') ?>" readonly>
          <div class="hint">This code is auto-generated when you save.</div>
        </div>

        <div>
          <div class="label">Password</div>
          <input class="input" type="password" name="password" required>
        </div>
        <div>
          <div class="label">Status</div>
          <input class="input" name="status" value="<?= htmlspecialchars($old['status'] ?? 'active') ?>">
        </div>
      </div>

      <div class="btns">
        <button type="submit" class="btn-primary">Save</button>
        <a href="<?= htmlspecialchars($base . '/admin/service-managers', ENT_QUOTES, 'UTF-8') ?>" class="btn-secondary">Cancel</a>
      </div>
    </form>
  </main>
</body>
</html>
