<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Profiles</title>

  <!-- Remembered sidebar CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">
  <!-- Page CSS (changed from sm_css to css/manager) -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/newCustomer.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  
  <div class="main">
    <div class="details-section">
      <h3>Customer Details</h3>
          <label>Customer:</label>
          <input type="text" id="customer">

          <label>Phone:</label>
          <input type="text" id="phone">

          <label>Email:</label>
          <input type="email" id="email">

          <label>ID:</label>
          <input type="text" id="id">

          <label>Vehicles:</label>
              <div id="vehicles-container">
                <div class="vehicle-entry">
                  <input type="text" name="vehicle[]" placeholder="Vehicle Name">
                  <input type="text" name="vehicle-number[]" placeholder="Vehicle Number">
                  <button type="button" class="remove-vehicle">❌</button>
                </div>
              </div>
                  <button type="button" id="add-vehicle">+ Add Vehicle</button>


          <label>Date:</label>
          <input type="date" id="Date">


       <div class="modal-footer">
      <button class="cancel-button">Cancel</button>
      <button class="save-button">Save</button>
    </div>
  </div>

    </div>

    <script src="<?= $base ?>/public/assets/js/manager/newCustomer.js"></script>
    </body>
    </html>