<?php $current = 'profile'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Profile • AutoNexus</title>

  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin-profile.css?v=2">
  <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/app/views/layouts/admin-sidebar/styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <section class="profile-shell">

      <!-- Cover / header -->
      <div class="profile-cover">
        <div class="cover-overlay"></div>
        <div class="cover-content">
          <div class="avatar-wrap">
            <div class="avatar-lg">
              <i class="fa-solid fa-user"></i>
            </div>
            <div class="id-block">
              <h1 class="title">
                <?= htmlspecialchars(($admin['first_name'] ?? 'Admin') . ' ' . ($admin['last_name'] ?? '')) ?>
              </h1>
              <div class="meta">
                <span class="role-badge"><i class="fa-solid fa-shield-halved"></i> Admin</span>
                <span class="email"><i class="fa-regular fa-envelope"></i> <?= htmlspecialchars($admin['email'] ?? '') ?></span>
              </div>
            </div>
          </div>

          <div class="actions">
            <a class="btn ghost" href="<?= rtrim(BASE_URL,'/') ?>/admin-dashboard">
              <i class="fa-solid fa-gauge"></i> Dashboard
            </a>
          </div>
        </div>
      </div>

      <?php if (!empty($_SESSION['flash'])): ?>
        <div class="flash">
          <i class="fa-regular fa-circle-check"></i>
          <span><?= htmlspecialchars($_SESSION['flash']) ?></span>
        </div>
        <?php unset($_SESSION['flash']); ?>
      <?php endif; ?>

      <!-- Card -->
      <div class="profile-card card">
        <div class="card-head">
          <h2><i class="fa-regular fa-id-card"></i> Profile Details</h2>
          <p class="muted">Update your contact and address information.</p>
        </div>

        <form method="POST" action="<?= rtrim(BASE_URL,'/') ?>/admin/profile/update" class="grid">
          <!-- left column -->
          <div class="group">
            <div class="field">
              <label>First Name</label>
              <div class="input with-icon">
                <i class="fa-regular fa-user"></i>
                <input type="text" name="first_name" value="<?= htmlspecialchars($admin['first_name'] ?? '') ?>" required>
              </div>
            </div>

            <div class="field">
              <label>Email</label>
              <div class="input with-icon">
                <i class="fa-regular fa-envelope"></i>
                <input type="email" name="email" value="<?= htmlspecialchars($admin['email'] ?? '') ?>" required>
              </div>
            </div>

            <div class="field">
              <label>Alternate Phone</label>
              <div class="input with-icon">
                <i class="fa-solid fa-phone"></i>
                <input type="text" name="alt_phone" value="<?= htmlspecialchars($admin['alt_phone'] ?? '') ?>" placeholder="Optional">
              </div>
            </div>

            <div class="field">
              <label>City</label>
              <div class="input with-icon">
                <i class="fa-regular fa-building"></i>
                <input type="text" name="city" value="<?= htmlspecialchars($admin['city'] ?? '') ?>">
              </div>
            </div>
          </div>

          <!-- right column -->
          <div class="group">
            <div class="field">
              <label>Last Name</label>
              <div class="input with-icon">
                <i class="fa-regular fa-user"></i>
                <input type="text" name="last_name" value="<?= htmlspecialchars($admin['last_name'] ?? '') ?>" required>
              </div>
            </div>

            <div class="field">
              <label>Phone</label>
              <div class="input with-icon">
                <i class="fa-solid fa-phone"></i>
                <input type="text" name="phone" value="<?= htmlspecialchars($admin['phone'] ?? '') ?>" placeholder="10-digit phone">
              </div>
            </div>

            <div class="field">
              <label>Street</label>
              <div class="input with-icon">
                <i class="fa-regular fa-map"></i>
                <input type="text" name="street" value="<?= htmlspecialchars($admin['street'] ?? '') ?>">
              </div>
            </div>

            <div class="field">
              <label>State</label>
              <div class="input with-icon">
                <i class="fa-regular fa-compass"></i>
                <input type="text" name="state" value="<?= htmlspecialchars($admin['state'] ?? '') ?>">
              </div>
            </div>
          </div>

          <div class="card-foot">
            <button type="submit" class="btn primary">
              <i class="fa-regular fa-floppy-disk"></i> Save Changes
            </button>
          </div>
        </form>
      </div>
    </section>
  </main>
</body>
</html>
