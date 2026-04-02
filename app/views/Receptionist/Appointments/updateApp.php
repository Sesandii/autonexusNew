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
         <input type="text" id="customer" value="<?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?>" readonly>
<input type="text" id="phone" value="<?= htmlspecialchars($appointment['phone']) ?>" readonly>
<input type="text" id="vehicle-number" value="<?= htmlspecialchars($appointment['license_plate']) ?>" readonly>
<input type="text" id="vehicle" value="<?= htmlspecialchars($appointment['make'] . ' ' . $appointment['model']) ?>" readonly>

<label>Service:</label>
<select id="service">
    <?php foreach ($services as $service): ?>
        <option value="<?= $service['service_id'] ?>" <?= $appointment['service_id'] == $service['service_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($service['name']) ?>
        </option>
    <?php endforeach; ?>
</select>

<label>Branch:</label>
<select id="branch">
    <?php foreach ($branches as $branch): ?>
        <option value="<?= $branch['branch_id'] ?>" <?= $appointment['branch_id'] == $branch['branch_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($branch['name']) ?>
        </option>
    <?php endforeach; ?>
</select>

<label>Date:</label>
<input type="date" id="Date" value="<?= $appointment['appointment_date'] ?>">

<label>Time:</label>
<input type="time" id="Time" value="<?= $appointment['appointment_time'] ?>">

<label>Status:</label>
<select id="status">
    <?php
    $statuses = ['requested', 'confirmed', 'in_service', 'completed', 'cancelled'];
    foreach ($statuses as $statusOption):
    ?>
        <option value="<?= $statusOption ?>" <?= $appointment['status'] == $statusOption ? 'selected' : '' ?>>
            <?= $statusOption ?>
        </option>
    <?php endforeach; ?>
</select>
<label>Assigned To:</label>
<select id="assigned_to">
    <option value="">-- Select Supervisor --</option>
    <?php foreach ($supervisors as $sup): ?>
        <option value="<?= $sup['supervisor_id'] ?>"
            <?= $appointment['assigned_to'] == $sup['supervisor_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($sup['first_name'] . ' ' . $sup['last_name']) ?>
        </option>
    <?php endforeach; ?>
</select>
<label>Notes: </label>
<textarea id="notes"><?= htmlspecialchars($appointment['notes']) ?></textarea>

<div class="modal-footer">
    <button class="cancel-button">Cancel</button>
    <button class="save-button" data-id="<?= $appointment['appointment_id'] ?>">Save</button>
</div>
       
  </div>

    </div>
<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/r_js/updateApp.js"></script>

    </body>
    </html>