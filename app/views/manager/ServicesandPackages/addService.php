<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Add Service Package</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
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



  <div class="form-group">
    <label>Type</label>
    <select name="type" id="typeSelect">
      <option value="service">Service</option>
      <option value="package">Package</option>
    </select>
  </div>

  <!-- SERVICE CODE -->
<div class="form-group" id="serviceCodeGroup">
  <label>Service Code</label>
  <input type="text" name="service_code" id="serviceCode"
         value="<?= $lastServiceCode ?>">
</div>

<!-- PACKAGE CODE -->
<div class="form-group" id="packageCodeGroup" style="display:none;">
  <label>Package Code</label>
  <input type="text" name="package_code" id="packageCode"
         value="<?= $lastPackageCode ?>">
</div>


  <div class="form-group">
    <label>Name</label>
    <input type="text" name="name" required>
  </div>

  <div class="form-group">
    <label>Description</label>
    <textarea name="description"></textarea>
  </div>

  <!-- SERVICE -->
  <div id="serviceFields">
    <input type="number" name="duration" placeholder="Duration (min)">
    <input type="number" name="price" placeholder="Price">

    <select name="type_id">
      <?php foreach ($serviceTypes as $t): ?>
        <option value="<?= $t['type_id'] ?>"><?= $t['type_name'] ?></option>
      <?php endforeach; ?>
    </select>
  </div>

 <!-- PACKAGE FIELDS -->
<div id="packageFields" style="display:none;">

  <div class="form-group">
    <label>Included Services</label>

    <select name="services[]" multiple size="6" required style="min-height:140px;">
      <?php foreach ($services as $service): ?>
        <option value="<?= $service['service_code'] ?>">
          <?= htmlspecialchars($service['name']) ?>
          (<?= $service['service_code'] ?>)
        </option>
      <?php endforeach; ?>
    </select>

    <small style="color:#666">
      Hold Ctrl (Windows) or Cmd (Mac) to select multiple services
    </small>
  </div>

  <div class="form-group">
    <label>Total Duration (minutes)</label>
    <input type="number" name="total_duration" required>
  </div>

  <div class="form-group">
    <label>Total Price</label>
    <input type="number" name="total_price" required>
  </div>

</div>


  <button type="submit">Save</button>
</form>


<script src="<?= BASE_URL ?>/public/assets/js/manager/addServices.js"></script>

</body>
</html>
