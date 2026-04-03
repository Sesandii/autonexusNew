<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Profile') ?> - AutoNexus</title>
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
      $fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? ''));
      $username = (string)($profile['username'] ?? '—');
      $email = (string)($profile['email'] ?? '—');
      $role = (string)($profile['role'] ?? 'Customer');
      $avatar = $base . '/public/assets/img/User.PNG';
    ?>

    <div class="profile-shell">
      <div class="profile-hero">
        <img class="profile-avatar" src="<?= $avatar ?>" alt="Profile photo">

        <div class="profile-hero-text">
          <h1><?= htmlspecialchars($fullName ?: $username) ?></h1>
          <p class="username">@<?= htmlspecialchars($username) ?></p>

          <div class="profile-badges">
            <span class="badge role"><i class="fa fa-user"></i> Customer</span>
            <span class="badge mail"><i class="fa fa-envelope"></i> <?= htmlspecialchars($email) ?></span>
          </div>

          <a class="edit-link" href="<?= $base ?>/customer/profile/edit">
            <i class="fa fa-pen"></i> Edit
          </a>
        </div>
      </div>

      <div class="profile-grid">
        <section class="panel info-panel">
          <h2>Profile Information</h2>

          <div class="info-table">
            <div class="info-row"><span>Username</span><span><?= htmlspecialchars($username) ?></span></div>
            <div class="info-row"><span>Full Name</span><span><?= htmlspecialchars($fullName ?: '—') ?></span></div>
            <div class="info-row"><span>Email</span><span><?= htmlspecialchars($email) ?></span></div>
            <div class="info-row"><span>Phone</span><span><?= htmlspecialchars($profile['phone'] ?? '—') ?></span></div>
            <div class="info-row"><span>Street Address</span><span><?= htmlspecialchars($profile['street_address'] ?? '—') ?></span></div>
            <div class="info-row"><span>City / State</span><span><?= htmlspecialchars(trim(($profile['city'] ?? '') . ' ' . ($profile['state'] ?? ''))) ?: '—' ?></span></div>
            <div class="info-row"><span>Role</span><span><?= htmlspecialchars($role) ?></span></div>
            <div class="info-row"><span>Status</span><span><?= htmlspecialchars($profile['status'] ?? '—') ?></span></div>
          </div>
        </section>

        <section class="panel vehicle-panel">
          <div class="panel-head">
            <h2>My Vehicles</h2>
          </div>

          <div class="vehicles-container">
            <?php foreach ($vehicles as $v): ?>
              <article class="vehicle-card">
                <h3><?= htmlspecialchars(trim(($v['make'] ?? '') . ' ' . ($v['model'] ?? ''))) ?: 'Vehicle' ?></h3>

                <div class="vehicle-meta">
                  <div><span>License Plate</span><strong><?= htmlspecialchars($v['license_plate'] ?? '—') ?></strong></div>
                  <div><span><?= htmlspecialchars($v['color'] ?? '—') ?></span></div>
                  <div><span><?= htmlspecialchars($v['year'] ?? '—') ?></span></div>
                </div>

                <div class="vehicle-actions">
                  <a class="btn edit" href="<?= $base ?>/customer/profile/vehicle?id=<?= (int)$v['vehicle_id'] ?>">
                    <i class="fa fa-pen"></i> Edit
                  </a>
                </div>
              </article>
            <?php endforeach; ?>

            <?php if (empty($vehicles)): ?>
              <p class="notes">No vehicles registered yet. Use “Add Vehicle” to register your car with AutoNexus.</p>
            <?php endif; ?>
          </div>
        </section>
      </div>
    </div>
  </div>

</body>
</html>
