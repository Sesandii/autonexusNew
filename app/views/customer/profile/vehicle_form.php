<?php
$base = rtrim(BASE_URL, '/');
$errors = isset($errors) && is_array($errors) ? $errors : [];
$editing = isset($vehicle['vehicle_id']) && (int)$vehicle['vehicle_id'] > 0;
$currentYear = (int)date('Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1.0" />
  <title><?= $editing ? 'Edit your Vehicle' : 'Add Vehicle' ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/profile.css?v=20260404c" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content vehicle-main-content customer-layout-main">

    <?php if (!empty($flash)): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <div class="section-card vehicle-section-card">
      <div class="section-header">
        <div>
          <h2><?= $editing ? 'Edit your Vehicle' : 'Add Vehicle' ?></h2>
          <p class="section-subtitle">
            <?= $editing 
              ? 'Update your vehicle details used for bookings and service history.'
              : 'Add a vehicle to quickly book services and track its history.' ?>
          </p>
        </div>
      </div>

      <form class="form-card vehicle-form-card" method="post" action="<?= $base ?>/customer/profile/vehicle" novalidate>
        <?php if ($editing): ?>
          <input type="hidden" name="vehicle_id" value="<?= (int)$vehicle['vehicle_id'] ?>">
        <?php endif; ?>

        <div class="grid vehicle-grid vehicle-row-single">
          <label>License Plate
            <input
              type="text"
              name="license_plate"
              value="<?= htmlspecialchars($vehicle['license_plate'] ?? '') ?>"
              class="<?= isset($errors['license_plate']) ? 'input-error' : '' ?>"
              required
              maxlength="20"
              autocomplete="off"
            >
            <?php if (!empty($errors['license_plate'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['license_plate']) ?></small>
            <?php endif; ?>
          </label>
        </div>

        <div class="grid vehicle-grid">
          <label>Brand
            <input type="text" name="make" value="<?= htmlspecialchars($vehicle['make'] ?? '') ?>" class="<?= isset($errors['make']) ? 'input-error' : '' ?>" required>
            <?php if (!empty($errors['make'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['make']) ?></small>
            <?php endif; ?>
          </label>

          <label>Model
            <input type="text" name="model" value="<?= htmlspecialchars($vehicle['model'] ?? '') ?>" class="<?= isset($errors['model']) ? 'input-error' : '' ?>" required>
            <?php if (!empty($errors['model'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['model']) ?></small>
            <?php endif; ?>
          </label>
        </div>

        <div class="grid vehicle-grid">
          <label>Year
            <input
              type="number"
              name="year"
              value="<?= htmlspecialchars((string)($vehicle['year'] ?? '')) ?>"
              min="1950"
              max="<?= $currentYear + 1 ?>"
              inputmode="numeric"
              class="<?= isset($errors['year']) ? 'input-error' : '' ?>"
              required
            >
            <?php if (!empty($errors['year'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['year']) ?></small>
            <?php endif; ?>
          </label>

          <label>Color
            <input type="text" name="color" value="<?= htmlspecialchars($vehicle['color'] ?? '') ?>" class="<?= isset($errors['color']) ? 'input-error' : '' ?>">
            <?php if (!empty($errors['color'])): ?>
              <small class="field-error"><?= htmlspecialchars($errors['color']) ?></small>
            <?php endif; ?>
          </label>
        </div>

        <div class="actions vehicle-actions-clean">
          <a class="btn-ghost" href="<?= $base ?>/customer/profile">Cancel</a>
          <button type="submit" class="btn-primary">
            <?= $editing ? 'Update Vehicle' : 'Save Vehicle' ?>
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    (function () {
      var form = document.querySelector('.vehicle-form-card');
      var plateInput = document.querySelector('input[name="license_plate"]');
      var brandInput = document.querySelector('input[name="make"]');
      var modelInput = document.querySelector('input[name="model"]');
      var yearInput = document.querySelector('input[name="year"]');

      if (!form || !plateInput || !brandInput || !modelInput || !yearInput) return;

      var maxYear = <?= $currentYear + 1 ?>;

      function fieldByName(name) {
        return form.querySelector('[name="' + name + '"]');
      }

      function ensureErrorNode(input) {
        var existing = input.parentElement.querySelector('.field-error');
        if (existing) return existing;

        var node = document.createElement('small');
        node.className = 'field-error';
        input.parentElement.appendChild(node);
        return node;
      }

      function setError(name, message) {
        var input = fieldByName(name);
        if (!input) return;
        input.classList.add('input-error');
        var node = ensureErrorNode(input);
        node.textContent = message;
      }

      function clearError(name) {
        var input = fieldByName(name);
        if (!input) return;
        input.classList.remove('input-error');

        var node = input.parentElement.querySelector('.field-error');
        if (!node) return;

        if (node.hasAttribute('data-server') && node.textContent.trim() !== '') {
          return;
        }

        node.textContent = '';
      }

      function validateLicensePlate() {
        var value = plateInput.value.trim();
        if (value === '') {
          setError('license_plate', 'License plate is required.');
          return false;
        }
        clearError('license_plate');
        return true;
      }

      function validateBrand() {
        if (brandInput.value.trim() === '') {
          setError('make', 'Brand is required.');
          return false;
        }
        clearError('make');
        return true;
      }

      function validateModel() {
        if (modelInput.value.trim() === '') {
          setError('model', 'Model is required.');
          return false;
        }
        clearError('model');
        return true;
      }

      function validateYear() {
        var value = yearInput.value.trim();
        if (value === '') {
          setError('year', 'Year is required.');
          return false;
        }
        if (!/^\d+$/.test(value)) {
          setError('year', 'Year must contain only numbers.');
          return false;
        }

        var year = parseInt(value, 10);
        if (year < 1950 || year > maxYear) {
          setError('year', 'Year must be between 1950 and ' + maxYear + '.');
          return false;
        }

        clearError('year');
        return true;
      }

      function markServerErrors() {
        form.querySelectorAll('.field-error').forEach(function (node) {
          if (node.textContent.trim() !== '') {
            node.setAttribute('data-server', '1');
          }
        });
      }

      markServerErrors();

      function normalizePlate() {
        plateInput.value = plateInput.value.trim().toUpperCase();
      }

      plateInput.addEventListener('input', function () {
        plateInput.value = plateInput.value.toUpperCase();
        validateLicensePlate();
      });

      plateInput.addEventListener('blur', normalizePlate);
      plateInput.addEventListener('blur', validateLicensePlate);
      brandInput.addEventListener('blur', validateBrand);
      modelInput.addEventListener('blur', validateModel);
      yearInput.addEventListener('blur', validateYear);

      yearInput.addEventListener('input', function () {
        yearInput.value = yearInput.value.replace(/[^0-9]/g, '');
        validateYear();
      });

      form.addEventListener('submit', function (e) {
        normalizePlate();

        var ok = true;
        ok = validateLicensePlate() && ok;
        ok = validateBrand() && ok;
        ok = validateModel() && ok;
        ok = validateYear() && ok;

        if (!ok) {
          e.preventDefault();
        }
      });

      normalizePlate();
    })();
  </script>

</body>
</html>
