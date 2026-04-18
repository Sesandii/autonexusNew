<?php $base = rtrim(BASE_URL, '/');
$current = 'service-managers'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Add Manager</title>
  <link rel="stylesheet" href="../../app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="../../app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="../../app/views/admin/admin-viewmanagers/service-managers.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

    <form class="form" method="post"
      action="<?= htmlspecialchars($base . '/admin/service-managers', ENT_QUOTES, 'UTF-8') ?>">
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
        <a href="<?= htmlspecialchars($base . '/admin/service-managers', ENT_QUOTES, 'UTF-8') ?>"
          class="btn-secondary">Cancel</a>
      </div>
    </form>
  </main>
</body>

</html>