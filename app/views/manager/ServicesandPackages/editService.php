<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= ($editing ?? false) ? 'Edit' : 'Add' ?> Service / Package</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/addService.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
  <div class="modal">

    <div class="modal-header">
      <h2><?= $editing ? 'Edit' : 'Add' ?> Service / Package</h2>
    </div>

    <form method="POST" action="<?= BASE_URL ?>/manager/services/<?= $editing ? 'update' : 'store' ?>">

      <?php if ($editing): ?>
        <input type="hidden" name="id"
          value="<?= $editType === 'service' ? $service['service_id'] : $package['package_id'] ?>">
      <?php endif; ?>

      <!-- IMPORTANT: keep type submitted -->
      <input type="hidden" name="type" value="<?= $editType ?>">

      <!-- Type Selection (Disabled in Edit) -->
      <div class="form-group">
        <label>Type</label>
        <select <?= $editing ? 'disabled' : '' ?>>
          <option value="service" <?= $editType === 'service' ? 'selected' : '' ?>>Service</option>
          <option value="package" <?= $editType === 'package' ? 'selected' : '' ?>>Package</option>
        </select>
      </div>

      <!-- Service Code -->
      <div class="form-group" <?= $editType==='service' ? '' : 'style="display:none;"' ?>>
        <label>Service Code</label>
        <input type="text" name="service_code"
               value="<?= $editing && $editType==='service' ? $service['service_code'] : $lastServiceCode ?>"
               readonly>
      </div>

      <!-- Package Code -->
      <div class="form-group" <?= $editType==='package' ? '' : 'style="display:none;"' ?>>
        <label>Package Code</label>
        <input type="text" name="package_code"
               value="<?= $editing && $editType==='package' ? $package['package_code'] : $lastPackageCode ?>"
               readonly>
      </div>

      <!-- Name -->
      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" required
               value="<?= $editing
                   ? htmlspecialchars($editType==='service' ? $service['name'] : $package['name'])
                   : '' ?>">
      </div>

      <!-- Description -->
      <div class="form-group">
        <label>Description</label>
        <textarea name="description"><?= $editing
                   ? htmlspecialchars($editType==='service' ? $service['description'] : $package['description'])
                   : '' ?></textarea>
      </div>

      <!-- ================= SERVICE ================= -->
      <?php if ($editType === 'service'): ?>
      <div id="serviceFields">

        <div class="form-group">
          <label>Duration (minutes)</label>
          <input type="number" name="duration"
                 value="<?= $editing ? $service['base_duration_minutes'] : '' ?>">
        </div>

        <div class="form-group">
          <label>Price</label>
          <input type="number" name="price"
                 value="<?= $editing ? $service['default_price'] : '' ?>">
        </div>

        <div class="form-group">
          <label>Service Type</label>
          <select disabled>
            <?php foreach ($serviceTypes as $t): ?>
              <option value="<?= $t['type_id'] ?>"
                <?= $editing && $service['type_id'] == $t['type_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['type_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="type_id"
                 value="<?= $editing ? $service['type_id'] : '' ?>">
        </div>

      </div>
      <?php endif; ?>

      <!-- ================= PACKAGE ================= -->
      <?php if ($editType === 'package'): ?>
      <div id="packageFields">

        <div class="form-group">
          <label>Included Services</label>
          <?php
            $selectedServices = [];
            if ($editing && isset($package['services']) && is_array($package['services'])) {
                $selectedServices = array_column($package['services'], 'service_code');
            }
          ?>
          <select name="services[]" multiple size="6" required style="min-height:140px;">
            <?php foreach ($services as $s): ?>
              <option value="<?= $s['service_code'] ?>"
                <?= in_array($s['service_code'], $selectedServices) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['name']) ?> (<?= $s['service_code'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label>Package Type</label>
          <select disabled>
            <?php foreach ($serviceTypes as $t): ?>
              <option value="<?= $t['type_id'] ?>"
                <?= $editing && $package['service_type_id'] == $t['type_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($t['type_name']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <input type="hidden" name="service_type_id"
                 value="<?= $editing ? $package['service_type_id'] : '' ?>">
        </div>

        <div class="form-group">
          <label>Total Duration (minutes)</label>
          <input type="number" name="total_duration" required
                 value="<?= $editing ? $package['total_duration_minutes'] : '' ?>">
        </div>

        <div class="form-group">
          <label>Total Price</label>
          <input type="number" name="total_price" required
                 value="<?= $editing ? $package['total_price'] : '' ?>">
        </div>

      </div>
      <?php endif; ?>

      <button type="submit"><?= $editing ? 'Update' : 'Save' ?></button>
    </form>

  </div>
</div>

</body>
</html>