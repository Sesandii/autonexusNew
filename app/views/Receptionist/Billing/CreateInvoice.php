<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Invoice - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newComplaint.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
  <div class="details-section">
    <h3>Invoice</h3>

    <form method="POST" action="<?= BASE_URL ?>/receptionist/complaints">

      <!-- Phone -->
      <label for="phone">Phone:</label>
      <input type="text" id="phone" name="phone">

      <!-- Customer Name -->
      <label for="customer_name">Name:</label>
      <input type="text" id="customer_name" name="customer_name" required>

      <!-- Email -->
      <label for="email">Email:</label>
      <input type="email" id="email" name="email">

      <!-- Vehicle Dropdown -->
      <label for="vehicle">Vehicle:</label>
      <div id="vehicle-container">
        <input type="text" id="vehicle" name="vehicle">
      </div>

      <!-- Vehicle Number -->
      <label for="vehicle_number">Vehicle Number:</label>
      <input type="text" id="vehicle_number" name="vehicle_number">

      <!-- Date -->
      <label for="complaint_date">Date:</label>
      <input type="date" id="complaint_date" name="complaint_date">

      <!-- Time -->
      <label for="complaint_time">Time:</label>
      <input type="time" id="complaint_time" name="complaint_time">

      <!-- Service Type -->
      <label for="service_type">Service Type:</label>
      <select id="service_type" name="service_type">
        <option value="">Select type</option>
        <?php
          // Example: populate from backend
          foreach ($serviceTypes as $type) {
            echo "<option value='{$type['type_id']}'>{$type['type_name']}</option>";
          }
        ?>
      </select>

      <!-- Service -->
      <label for="service">Service:</label>
      <select id="service" name="service">
        <option value="">Select service</option>
      </select>

      <!-- Price -->
      <label for="Price">Price:</label>
      <div class="price-field">
        <span class="currency">Rs.</span>
        <input type="number" id="Price" name="Price" placeholder="0.00">
      </div>

      <!-- Buttons -->
      <div class="modal-footer">
        <button type="button" class="cancel-button">Cancel</button>
        <button type="submit" class="save-button">Generate</button>
      </div>

    </form>
  </div>
</div>

<!-- Define BASE_URL for JS -->
<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/createInvoice.js"></script>

</body>
</html>
