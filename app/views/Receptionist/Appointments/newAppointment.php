<?php
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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newAppointment.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

 <?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>
 
<div class="main">
    <div class="details-section">
      <h3>Appointment Details</h3>

      
<form action="<?= BASE_URL ?>/receptionist/appointments/save" method="post">

<label>Phone:</label>
<input type="text" id="phone">

<input type="hidden" id="customer_id">

<label>Customer Name:</label>
<input type="text" id="customer" readonly>

<input type="hidden" id="vehicle_id">

<label>Vehicle:</label>
<select id="vehicle-select">
    <option value=""></option>
</select>

<label>Vehicle Number:</label>
<input type="text" id="vehicle-number" readonly>

<input type="hidden" id="customer_id" name="customer_id">
<input type="hidden" id="vehicle_id" name="vehicle_id">

<input type="hidden" id="service_id" name="service_id">

<label>Date:</label>
<input type="date" id="Date" name="appointment_date">

<label>Time:</label>
<input type="time" id="Time" name="appointment_time">

<label>Service:</label>
<select id="service" name="service">
    <option value="">-- Select Service or Package --</option>

    <?php if (!empty($services)) : ?>
        <?php foreach ($services as $service) : ?>
            <option value="<?= $service['service_id'] ?>">
                <?= htmlspecialchars($service['name']) ?> (₨<?= number_format($service['default_price'], 2) ?>)
            </option>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if (!empty($packages)) : ?>
        <?php foreach ($packages as $package) : ?>
            <option value="package_<?= htmlspecialchars($package['package_code']) ?>">
                <?= htmlspecialchars($package['name']) ?> (₨<?= number_format($package['total_price'], 2) ?>)
            </option>
        <?php endforeach; ?>
    <?php endif; ?>

</select>



<label>Status:</label>
<select id="status">
    <option>Requested</option>
    <option>Postponed</option>
    <option>Waiting</option>
    <option>In Service</option>
    <option>Completed</option>
    <option>Canceled</option>
</select>


<input type="hidden" id="branch_id" name="branch_id">

<label>Branch:</label>
<input list="branch-list" id="branch-search" placeholder="Type branch name...">
<datalist id="branch-list">
    <?php foreach ($branches as $branch) : ?>
        <option data-id="<?= $branch['branch_id'] ?>" value="<?= htmlspecialchars($branch['name'] . ' (' . $branch['city'] . ')') ?>"></option>
    <?php endforeach; ?>
</datalist>




</form>

       <div class="modal-footer">
      <button class="cancel-button">Cancel</button>
      <button class="save-button">Save</button>
    </div>
  </div>

    </div>
<!-- Define BASE_URL for JS -->
<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<!-- Load JS -->
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/newAppointment.js"></script>

</body>
    </html>