<?php
$base = rtrim(BASE_URL, '/');
$editing = !empty($vehicle);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= $editing ? 'Edit Vehicle' : 'Add Vehicle' ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/profile.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content">
    <h2><?= $editing ? 'Edit Vehicle' : 'Add Vehicle' ?></h2>

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <form class="form-card" method="post" action="<?= $base ?>/customer/profile/vehicle">
      <?php if ($editing): ?>
        <input type="hidden" name="vehicle_id" value="<?= (int)$vehicle['vehicle_id'] ?>">
      <?php endif; ?>

      <div class="grid">
        <label>License Plate
          <input type="text" name="license_plate" value="<?= htmlspecialchars($vehicle['license_plate'] ?? '') ?>" required>
        </label>
        <label>Make (Brand)
          <input type="text" name="make" value="<?= htmlspecialchars($vehicle['make'] ?? '') ?>" required>
        </label>
      </div>

      <div class="grid">
        <label>Model
          <input type="text" name="model" value="<?= htmlspecialchars($vehicle['model'] ?? '') ?>" required>
        </label>
        <label>Year
          <input type="number" name="year" value="<?= htmlspecialchars($vehicle['year'] ?? '') ?>" min="1900" max="<?= date('Y')+1 ?>">
        </label>
      </div>

      <label>Color
        <input type="text" name="color" value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>">
      </label>

      <div class="actions">
        <a class="btn-ghost" href="<?= $base ?>/customer/profile">Cancel</a>
        <button type="submit" class="btn-primary"><?= $editing ? 'Save Changes' : 'Add Vehicle' ?></button>
      </div>
    </form>
  </div>

</body>
</html>
