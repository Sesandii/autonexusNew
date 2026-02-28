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

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <!-- Profile card -->
    <div class="profile-card">
      <div class="card-header">
        <h2>My Profile</h2>
        <a class="btn red" href="<?= $base ?>/customer/profile/edit">
          <i class="fa fa-pen"></i>
          Edit
        </a>
      </div>

      <form class="form-card form-readonly" aria-readonly="true">
        <?php $fullName = trim(($profile['first_name'] ?? '') . ' ' . ($profile['last_name'] ?? '')); ?>

        <div class="grid-2">
          <label>Username
            <input type="text" value="<?= htmlspecialchars($profile['username'] ?? '—') ?>" readonly>
          </label>
          <label>Full Name
            <input type="text" value="<?= htmlspecialchars($fullName ?: '—') ?>" readonly>
          </label>

          <label>Email
            <input type="text" value="<?= htmlspecialchars($profile['email'] ?? '—') ?>" readonly>
          </label>
          <label>Phone
            <input type="text" value="<?= htmlspecialchars($profile['phone'] ?? '—') ?>" readonly>
          </label>

          <label>Alt. Phone
            <input type="text" value="<?= htmlspecialchars($profile['alt_phone'] ?? '—') ?>" readonly>
          </label>
          <label>Member Since
            <input type="text" value="<?= htmlspecialchars($profile['created_at'] ?? '—') ?>" readonly>
          </label>

          <label>Street Address
            <input type="text" value="<?= htmlspecialchars($profile['street_address'] ?? '—') ?>" readonly>
          </label>
          <label>City / State
            <input type="text" value="<?= htmlspecialchars(($profile['city'] ?? '') . ' ' . ($profile['state'] ?? '')) ?>" readonly>
          </label>

          <label>Role
            <input type="text" value="<?= htmlspecialchars($profile['role'] ?? '—') ?>" readonly>
          </label>
          <label>Status
            <input type="text" value="<?= htmlspecialchars($profile['status'] ?? '—') ?>" readonly>
          </label>
        </div>
      </form>
    </div>

    <!-- Vehicles section as its own card -->
    <div class="section-card mt-24">
      <div class="section-header">
        <h2>Registered Vehicles</h2>
        <a class="btn yellow" href="<?= $base ?>/customer/profile/vehicle">
          <i class="fa fa-plus"></i>
          Add Vehicle
        </a>
      </div>

      <div class="vehicles-container">
        <?php foreach ($vehicles as $v): ?>
          <div class="vehicle-card">
            <h3><?= htmlspecialchars(($v['make'] ?? '') . ' ' . ($v['model'] ?? '')) ?></h3>
            <div class="grid-2 small">
              <div><span class="label">Code:</span> <?= htmlspecialchars($v['vehicle_code'] ?? '') ?></div>
              <div><span class="label">Reg No:</span> <?= htmlspecialchars($v['license_plate'] ?? '') ?></div>
              <div><span class="label">Color:</span> <?= htmlspecialchars($v['color'] ?? '') ?></div>
              <div><span class="label">Year:</span> <?= htmlspecialchars($v['year'] ?? '') ?></div>
            </div>
            <div class="vehicle-actions">
              <a class="btn edit" href="<?= $base ?>/customer/profile/vehicle?id=<?= (int)$v['vehicle_id'] ?>">
                <i class="fa fa-pen"></i>
                Edit
              </a>
              <form method="post"
                    action="<?= $base ?>/customer/profile/vehicle/delete"
                    onsubmit="return confirm('Remove this vehicle?')">
                <input type="hidden" name="vehicle_id" value="<?= (int)$v['vehicle_id'] ?>">
                <button class="btn red" type="submit">
                  <i class="fa fa-trash"></i>
                  Remove
                </button>
              </form>
            </div>
          </div>
        <?php endforeach; ?>

        <?php if (empty($vehicles)): ?>
          <p class="notes">No vehicles registered yet. Use “Add Vehicle” to register your car with AutoNexus.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

</body>
</html>
