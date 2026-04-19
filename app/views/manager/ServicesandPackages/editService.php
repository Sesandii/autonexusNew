<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Edit Service / Package</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/addService.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
  <div class="modal">

    <div class="modal-header">
      <h2>Edit Service / Package</h2>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/manager/services/update">

      <input type="hidden" name="id"
        value="<?= $editType === 'service' ? $service['service_id'] : $package['package_id'] ?>">
      <input type="hidden" name="type" value="<?= $editType ?>">

      <div class="details-section">

    
        <!-- Type (disabled, just for display) -->
        <div class="form-group">
          <label>Type</label>
          <select disabled>
            <option value="service" <?= $editType === 'service' ? 'selected' : '' ?>>Service</option>
            <option value="package" <?= $editType === 'package' ? 'selected' : '' ?>>Package</option>
          </select>
        </div>

        <!-- Code -->
        <div class="form-group">
          <label>Code</label>
          <input type="text" name="service_code"
                 value="<?= $editType === 'service' ? $service['service_code'] : $package['package_code'] ?>"
                 readonly>
        </div>

        <!-- Name -->
        <div class="form-group">
          <label>Name</label>
          <input type="text" name="name" required
                 value="<?= htmlspecialchars($editType === 'service' ? $service['name'] : $package['name']) ?>">
        </div>

        <!-- Description -->
        <div class="form-group full-width">
          <label>Description</label>
          <textarea name="description"><?= htmlspecialchars($editType === 'service' ? $service['description'] : $package['description']) ?></textarea>
        </div>

        <!-- Duration -->
        <div class="form-group">
          <label>Duration (min)</label>
          <input type="number" name="<?= $editType === 'service' ? 'duration' : 'total_duration' ?>"
                 value="<?= $editType === 'service' ? $service['base_duration_minutes'] : $package['total_duration_minutes'] ?>">
        </div>

        <!-- Price -->
        <div class="form-group">
          <label>Price</label>
          <input type="number" name="<?= $editType === 'service' ? 'price' : 'total_price' ?>"
                 value="<?= $editType === 'service' ? $service['default_price'] : $package['total_price'] ?>">
        </div>

        <!-- Service Type -->
        <div class="form-group full-width">
          <label>Service Type</label>
          <select name="type_id" required>
            <?php
              $currentTypeId = $editType === 'service' ? $service['type_id'] : $package['service_type_id'];
            ?>
            <?php foreach ($serviceTypes as $t): ?>
              <option value="<?= $t['type_id'] ?>"
                <?= $currentTypeId == $t['type_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['type_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <!-- Package: Included Services -->
        <?php if ($editType === 'package'): ?>
        <?php
          $selectedServices = [];
          if (isset($package['services']) && is_array($package['services'])) {
              $selectedServices = array_column($package['services'], 'service_code');
          }
        ?>
        <div class="form-group full-width">
          <label>Included Services</label>
          <select name="services[]" multiple size="6">
            <?php foreach ($services as $s): ?>
              <option value="<?= $s['service_code'] ?>"
                <?= in_array($s['service_code'], $selectedServices) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php endif; ?>

        <div class="modal-footer">
        <button type="button" class="cancel-button">Cancel</button>
        <button type="submit" class="save-button">Update</button>
      </div>

      </div>

      
    </form>
  </div>
</div>

</body>
</html>