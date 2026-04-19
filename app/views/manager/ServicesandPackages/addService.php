<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Service Package</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/addService.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

  <div class="modal">

    <!-- HEADER -->
    <div class="modal-header">
      <h2>Add Service / Package</h2>
    </div>

<form method="POST" action="<?= BASE_URL ?>/manager/services/store">

  <div class="details-section">

    <div class="form-group">
      <label>Type</label>
      <select name="type" id="typeSelect">
    <option value="service" <?= ($defaultType ?? 'service') === 'service' ? 'selected' : '' ?>>Service</option>
    <option value="package" <?= ($defaultType ?? 'service') === 'package' ? 'selected' : '' ?>>Package</option>
</select>
    </div>

  <div class="form-group" id="codeGroup">
    <label>Code</label>
    <input type="text" name="service_code" id="serviceCode" value="<?= $lastCode ?>">
</div>

    <div class="form-group">
  <label>Name</label>
  <input type="text" name="name" required>
</div>

<div class="form-group full-width">
  <label>Description</label>
  <textarea name="description"></textarea>
</div>

<div class="form-group">
  <label>Duration (min)</label>
  <input type="number" name="duration">
</div>

<div class="form-group">
  <label>Price</label>
  <input type="number" name="price">
</div>

<div class="form-group full-width">
  <label>Service Type</label>
  <select name="type_id" required>
    <?php foreach ($serviceTypes as $t): ?>
      <option value="<?= $t['type_id'] ?>"><?= $t['type_name'] ?></option>
    <?php endforeach; ?>
  </select>
</div>

    <!-- PACKAGE FIELDS (UNCHANGED FOR JS) -->
<div id="packageFields" style="display:none;" class="form-group full-width">
  <label>Included Services</label>

  <select name="services[]" multiple size="6">
    <?php foreach ($services as $service): ?>
      <option value="<?= $service['service_code'] ?>">
        <?= htmlspecialchars($service['name']) ?>
      </option>
    <?php endforeach; ?>
  </select>
</div>

<!-- FOOTER (MATCH YOUR CSS) -->
    <div class="modal-footer">
      <button type="button" class="cancel-button">Cancel</button>
      <button type="submit" class="save-button">Save</button>
    </div>

    </div>

    

    </form>
  </div>

<!-- At the bottom of your addService.php, before including the JS -->
<script>
  window.lastCode = '<?= $lastCode ?>';
</script>

<script src="<?= BASE_URL ?>/public/assets/js/manager/addServices.js"></script>
</body>
</html>
