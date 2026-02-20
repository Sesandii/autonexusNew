<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newCustomer.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>
  
<div class="main">
      <form id="customer-form" action="<?= BASE_URL ?>/receptionist/customers/store" method="POST">


  <div class="details-section">
      <h3>Customer Details</h3>

      <label>First Name:</label>
      <input type="text" name="first_name" required>

      <label>Last Name:</label>
      <input type="text" name="last_name" required>

      <label>Username:</label>
      <input type="text" name="username" required>

      <label>Email:</label>
      <input type="email" name="email" required>

      <label>Password:</label>
      <input type="password" name="password" required>

      <label>Phone:</label>
      <input type="text" name="phone" required>

      <label>Vehicles:</label>

      <div id="vehicles-container">
        <div class="vehicle-entry">
          <input type="text" name="vehicles[0][license_plate]" placeholder="License Plate" required>
          <input type="text" name="vehicles[0][make]" placeholder="Make">
          <input type="text" name="vehicles[0][model]" placeholder="Model">
          <input type="text" name="vehicles[0][year]" placeholder="Year">
          <input type="text" name="vehicles[0][color]" placeholder="Color">
          <button type="button" class="remove-vehicle">❌</button>
        </div>
      </div>

      <button type="button" id="add-vehicle">+ Add Vehicle</button>

      <div class="modal-footer">
      
        <button type="submit" class="save-button">Save</button>
      </div>
  </div>

</form>

    </div>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/newCustomer.js"></script>

  </body>
    </html>