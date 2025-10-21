<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= htmlspecialchars($title ?? 'Profile') ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/profile.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content">
    <div class="profile-card">
      <h2>My Profile</h2>
      <div class="profile-info">
        <div class="profile-pic" id="profile-pic">
          <?php if (!empty($profile['profile_pic'])): ?>
            <img src="<?= $base ?>/uploads/profiles/<?= htmlspecialchars($profile['profile_pic']) ?>" alt="Profile" style="width:100%;height:100%;border-radius:50%;">
          <?php else: ?>
            Profile Picture
          <?php endif; ?>
        </div>
        <div class="profile-details">
          <p><span class="label">Full Name:</span> <span id="profile-name"><?= htmlspecialchars($profile['first_name'] ?? '') ?></span></p>
          <p><span class="label">Email:</span> <span id="profile-email"><?= htmlspecialchars($profile['email'] ?? '') ?></span></p>
          <p><span class="label">Contact Number:</span> <span id="profile-phone"><?= htmlspecialchars($profile['phone'] ?? '') ?></span></p>
          <p><span class="label">NIC:</span> <span id="profile-nic"><?= htmlspecialchars($profile['nic'] ?? '') ?></span></p>
          <button class="btn red" id="edit-profile-btn">Edit Profile</button>
        </div>
      </div>
    </div>

    <h2>Registered Vehicles</h2>
    <div class="vehicles-container" id="vehicles-container">
      <?php foreach ($vehicles as $v): ?>
        <div class="vehicle-card" data-id="<?= $v['vehicle_id'] ?>">
          <h3><?= htmlspecialchars($v['brand'] . ' ' . $v['model']) ?></h3>
          <p><span class="label">Registration:</span> <?= htmlspecialchars($v['reg_no']) ?></p>
          <p><span class="label">Color:</span> <?= htmlspecialchars($v['color']) ?></p>
          <p><span class="label">Year of Manufacture:</span> <?= htmlspecialchars($v['year']) ?></p>
          <div class="vehicle-actions">
            <button class="btn edit" onclick="editVehicle(this)">Edit</button>
            <button class="btn red" onclick="removeVehicle(this)">Remove</button>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="vehicle-buttons">
      <button class="btn yellow" id="add-vehicle-btn">+ Add New Vehicle</button>
    </div>
  </div>

  <!-- Modals (unchanged from your HTML) -->
  <?php include APP_ROOT . '/views/customer/profile/modals.php'; ?>

  <script src="<?= $base ?>/assets/js/customer/profile.js"></script>
</body>
</html>
