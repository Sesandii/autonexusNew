<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Complaint - AutoNexus</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newComplaint.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>
  
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

  <div class="main">
    <div class="details-section">
      <h3>Complaint Details</h3>

      <form method="POST" action="<?= BASE_URL ?>/receptionist/complaints">

  <label>Phone:</label>
  <input type="text" name="phone" id="phone">


<input type="hidden" name="customer_id" id="customer_id">
<input type="hidden" name="vehicle_id" id="vehicle_id">

<label>Customer:</label>
<input type="text" name="customer_name" disabled>

<label>Email:</label>
<input type="email" name="email" disabled>

<label>Vehicle:</label>
<div id="vehicle-container">
    <input type="text" name="vehicle" class="form-control" disabled>
</div>

<label>Vehicle Number:</label>
<input type="text" name="vehicle_number" id="vehicle_number" disabled>


  <label>Date:</label>
  <input type="date" name="complaint_date">

  <label>Time:</label>
  <input type="time" name="complaint_time">

  <label>Description:</label>
  <textarea name="description" rows="4"></textarea>

  <label>Priority:</label>
  <select name="priority">
    <option>High</option>
    <option selected>Medium</option>
    <option>Low</option>
  </select>

  <label>Status:</label>
  <select name="status">
    <option>Open</option>
    <option>In Progress</option>
    <option>Resolved</option>
    <option>Canceled</option>
  </select>

  <div class="modal-footer">
    <button type="button" class="cancel-button" onclick="window.history.back()">Cancel</button>
    <button type="submit" class="save-button">Save</button>
  </div>

</form>

    </div>
  </div>
  
<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/y.js"></script>
</body>
</html>
