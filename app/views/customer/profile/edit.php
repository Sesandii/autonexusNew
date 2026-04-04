<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Edit Profile') ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/profile.css?v=20260403" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content">

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <?php
      $username = (string)($profile['username'] ?? 'customer');
      $email = (string)($profile['email'] ?? '—');
      $role = (string)($profile['role'] ?? '—');
      $status = (string)($profile['status'] ?? '—');
      $avatar = !empty($profile['profile_picture'])
        ? $base . '/public/' . ltrim((string)$profile['profile_picture'], '/')
        : $base . '/public/assets/img/User.PNG';
    ?>

    <div class="profile-shell edit-shell">
      <div class="profile-hero edit-hero">
        <img class="profile-avatar" src="<?= $avatar ?>" alt="Profile photo">

        <div class="profile-hero-text">
          <h1>Edit Profile</h1>
          <p class="hero-subtitle">Update your personal details</p>

          <div class="profile-badges">
            <span class="badge user"><i class="fa fa-user"></i> <?= htmlspecialchars($username) ?></span>
            <span class="badge mail"><i class="fa fa-envelope"></i> <?= htmlspecialchars($email) ?></span>
          </div>
        </div>
      </div>

      <form class="form-card edit-form" method="post" action="<?= $base ?>/customer/profile/update" enctype="multipart/form-data">
        <div class="form-section-title">Personal Information</div>

        <div class="grid edit-grid">
          <label>Username
            <input type="text" name="username" value="<?= htmlspecialchars($profile['username'] ?? '') ?>" required>
          </label>
          <label>Email
            <input type="email" name="email" value="<?= htmlspecialchars($profile['email'] ?? '') ?>" required>
          </label>
        </div>

        <div class="grid edit-grid">
          <label>First Name
            <input type="text" name="first_name" value="<?= htmlspecialchars($profile['first_name'] ?? '') ?>" required>
          </label>
          <label>Last Name
            <input type="text" name="last_name" value="<?= htmlspecialchars($profile['last_name'] ?? '') ?>" required>
          </label>
        </div>

        <div class="grid edit-grid">
          <label>Phone
            <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone'] ?? '') ?>">
          </label>
          <label>Alt Phone
            <input type="text" name="alt_phone" value="<?= htmlspecialchars($profile['alt_phone'] ?? '') ?>">
          </label>
        </div>

        <div class="grid edit-grid">
          <label>Profile Picture
            <input type="file" name="profile_picture" accept="image/jpeg,image/png,image/gif,image/webp">
          </label>
          <label>State
            <input type="text" name="state" value="<?= htmlspecialchars($profile['state'] ?? '') ?>">
          </label>
        </div>

        <div class="grid edit-grid form-readonly">
          <label>Role
            <input type="text" value="<?= htmlspecialchars($role) ?>" readonly>
          </label>
          <label>Status
            <input type="text" value="<?= htmlspecialchars($status) ?>" readonly>
          </label>
        </div>

        <div class="grid edit-grid address-grid">
          <label class="span-2">Address
            <input type="text" name="street_address" value="<?= htmlspecialchars($profile['street_address'] ?? '') ?>">
          </label>
          <label class="span-2">City
            <input type="text" name="city" value="<?= htmlspecialchars($profile['city'] ?? '') ?>">
          </label>
        </div>

        <div class="actions edit-actions">
          <a class="btn-cancel" href="<?= $base ?>/customer/profile">Cancel</a>
          <button type="submit" class="btn-primary">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

</body>
</html>
