<?php /* Admin view: renders admin-viewmanagers/create page. */ ?>
<?php
$base = rtrim($base ?? BASE_URL, '/');
$current = 'service-managers';
$errors = $errors ?? [];
$old = $old ?? [];

function e($value): string
{
  return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Service Manager</title>
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-shared/management.css">
  <link rel="stylesheet" href="<?= $base ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/admin/branches/create.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>

<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content branch-create-page">
    <div class="branch-create-shell">
      <header class="create-header">
        <div class="create-title">
          <h1>Add Service Manager</h1>
          <p>Create a new manager profile with secure login and contact details.</p>
        </div>
        <a href="<?= e($base . '/admin/service-managers') ?>" class="btn-secondary">
          <i class="fa-solid fa-arrow-left"></i>
          <span>Back to Managers</span>
        </a>
      </header>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
          <?php foreach ($errors as $err): ?>
            <div><?= e($err) ?></div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <form class="create-form-shell" method="post" action="<?= e($base . '/admin/service-managers') ?>">
        <section class="create-card">
          <div class="create-card-header">
            <i class="fa-solid fa-user-tie"></i>
            <h2>Manager Information</h2>
          </div>

          <div class="create-card-body">
            <div class="form-grid">
              <div class="field">
                <label class="label">First Name</label>
                <input class="input" name="first_name" value="<?= e($old['first_name'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Last Name</label>
                <input class="input" name="last_name" value="<?= e($old['last_name'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Username</label>
                <input class="input" name="username" value="<?= e($old['username'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Email</label>
                <input class="input" type="email" name="email" value="<?= e($old['email'] ?? '') ?>" required>
              </div>

              <div class="field">
                <label class="label">Phone</label>
                <input class="input" name="phone" type="tel" inputmode="numeric" pattern="^0[0-9]{9}$" maxlength="10"
                  placeholder="0712345678" value="<?= e($old['phone'] ?? '') ?>">
              </div>

              <div class="field">
                <label class="label">Status</label>
                <select class="input" name="status" required>
                  <option value="active" <?= (($old['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active
                  </option>
                  <option value="inactive" <?= (($old['status'] ?? 'active') === 'inactive') ? 'selected' : '' ?>>Inactive
                  </option>
                </select>
              </div>

              <div class="field">
                <label class="label">Manager Code (auto-generated)</label>
                <input class="input" value="<?= e($nextCode ?? 'MAN001') ?>" readonly>
                <div class="hint">This code is automatically generated when you save.</div>
              </div>

              <div class="field">
                <label class="label">Password</label>
                <input class="input" type="password" name="password"
                  value="<?= e($old['password'] ?? 'Manager@123') ?>">
                <div class="hint">Default password is Manager@123.</div>
              </div>
            </div>
          </div>
        </section>

        <div class="form-actions">
          <a href="<?= e($base . '/admin/service-managers') ?>" class="btn-secondary">
            <i class="fa-solid fa-xmark"></i>
            <span>Cancel</span>
          </a>

          <button type="submit" class="btn-primary">
            <i class="fa-solid fa-floppy-disk"></i>
            <span>Save Manager</span>
          </button>
        </div>
      </form>
    </div>
  </main>
</body>

</html>