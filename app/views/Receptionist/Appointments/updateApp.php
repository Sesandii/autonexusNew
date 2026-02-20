<<?php
// Set the active page for sidebar highlighting
$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/updateApp.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
    <div class="details-section">
      <h3>Appointment Details</h3>
          <label>Customer:</label>
          <input type="text" id="customer">

          <label>Phone:</label>
          <input type="text" id="phone">

          <label>Vehicle Number:</label>
          <input type="text" id="vehicle-number">

          <label>Vehicle:</label>
          <input type="text" id="vehicle">

          <label>Service:</label>
          <input type="text" id="service">

          <label>Date:</label>
          <input type="date" id="Date">

          <label>Time:</label>
          <input type="time" id="Time">

          <label>Status:</label>
          <select id="status">
            <option>Not Arrived</option>
            <option>Waiting</option>
            <option>In Service</option>
            <option>Completed</option>
            <option>Canceled</option>
          </select>

       <div class="modal-footer">
      <button class="cancel-button">Cancel</button>
      <button class="save-button">Save</button>
    </div>
  </div>

    </div>
<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/r_js/updateApp.js"></script>

    </body>
    </html>