<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Customer - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/updateCustomer.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
  <form id="customer-form" method="POST" action="<?= BASE_URL ?>/receptionist/customers/update/<?= $customer['customer_id'] ?>">

    <div class="details-section">
      <h3>Customer Details</h3>

      <label>First Name:</label>
      <input type="text" name="first_name" value="<?= htmlspecialchars($customer['first_name'] ?? '') ?>" required>

      <label>Last Name:</label>
      <input type="text" name="last_name" value="<?= htmlspecialchars($customer['last_name'] ?? '') ?>" required>

      <label>Username:</label>
      <input type="text" name="username" value="<?= htmlspecialchars($customer['username'] ?? '') ?>" required>

      <label>Email:</label>
      <input type="email" name="email" value="<?= htmlspecialchars($customer['email'] ?? '') ?>" required>

      <label>Phone:</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($customer['phone'] ?? '') ?>" required>

      <label>Vehicles:</label>

      <div id="vehicles-container">
        <?php if (!empty($customer['vehicles'])): ?>
          <?php foreach ($customer['vehicles'] as $i => $v): ?>
          <div class="vehicle-entry">
            <input type="text" name="vehicles[<?= $i ?>][license_plate]" placeholder="License Plate" value="<?= htmlspecialchars($v['license_plate']) ?>" required>
            <input type="text" name="vehicles[<?= $i ?>][make]" placeholder="Make" value="<?= htmlspecialchars($v['make']) ?>">
            <input type="text" name="vehicles[<?= $i ?>][model]" placeholder="Model" value="<?= htmlspecialchars($v['model']) ?>">
            <input type="text" name="vehicles[<?= $i ?>][year]" placeholder="Year" value="<?= htmlspecialchars($v['year']) ?>">
            <input type="text" name="vehicles[<?= $i ?>][color]" placeholder="Color" value="<?= htmlspecialchars($v['color']) ?>">
            <button type="button" class="remove-vehicle">❌</button>
          </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="vehicle-entry">
            <input type="text" name="vehicles[0][license_plate]" placeholder="License Plate" required>
            <input type="text" name="vehicles[0][make]" placeholder="Make">
            <input type="text" name="vehicles[0][model]" placeholder="Model">
            <input type="text" name="vehicles[0][year]" placeholder="Year">
            <input type="text" name="vehicles[0][color]" placeholder="Color">
            <button type="button" class="remove-vehicle">❌</button>
          </div>
        <?php endif; ?>
      </div>

      <button type="button" id="add-vehicle">+ Add Vehicle</button>

      <div class="modal-footer">
        <button type="submit" class="update-button">Update</button>
      </div>
    </div>

  </form>
</div>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/updateCustomer.js"></script>



</body>
</html>
