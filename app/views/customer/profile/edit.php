<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Edit Profile') ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/profile.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content">
    <h2>Edit Profile</h2>

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <form class="form-card" method="post" action="<?= $base ?>/customer/profile/update">
      <div class="grid">
        <label>First Name
          <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required>
        </label>
        <label>Last Name
          <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required>
        </label>
      </div>

      <div class="grid">
        <label>Phone
          <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
        </label>
        <label>Alt. Phone
          <input type="text" name="alt_phone" value="<?= htmlspecialchars($profile['alt_phone'] ?? '') ?>">
        </label>
      </div>

      <label>Street Address
        <input type="text" name="street_address" value="<?= htmlspecialchars($profile['street_address'] ?? '') ?>">
      </label>

      <div class="grid">
        <label>City
          <input type="text" name="city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>">
        </label>
        <label>State
          <input type="text" name="state" value="<?= htmlspecialchars($profile['state'] ?? '') ?>">
        </label>
      </div>

      <div class="actions">
        <a class="btn-ghost" href="<?= $base ?>/customer/profile">Cancel</a>
        <button type="submit" class="btn-primary">Save Changes</button>
      </div>
    </form>
  </div>

</body>
</html>
