<?php
// Set the active page for sidebar highlighting
$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Appointment - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/updateApp.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  
 <?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
    <div class="details-section">

        <h3><i class="fas fa-calendar-check"></i> Appointment Details</h3>

        <input type="hidden" id="appointment-id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>">

        <div class="form-group">
            <label>Customer</label>
            <input type="text" id="customer" value="<?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Phone</label>
            <input type="text" id="phone" value="<?= htmlspecialchars($appointment['phone'] ?? '') ?>" readonly>
        </div>

        <div class="form-group">
            <label>Vehicle Number</label>
            <input type="text" id="vehicle-number" value="<?= htmlspecialchars($appointment['license_plate']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Vehicle</label>
            <input type="text" id="vehicle" value="<?= htmlspecialchars($appointment['make'] . ' ' . $appointment['model']) ?>" readonly>
        </div>

        <div class="form-group full-width">
            <label>Service</label>
            <input type="text" id="service" value="<?= htmlspecialchars($appointment['service_name']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Date</label>
            <input type="date" id="Date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Time</label>
            <input type="time" id="Time" value="<?= htmlspecialchars($appointment['appointment_time']) ?>" readonly>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select id="status" disabled>
                <option <?= $appointment['status'] === 'Not Arrived' ? 'selected' : '' ?>>Not Arrived</option>
                <option <?= $appointment['status'] === 'Waiting' ? 'selected' : '' ?>>Waiting</option>
                <option <?= $appointment['status'] === 'In Service' ? 'selected' : '' ?>>In Service</option>
                <option <?= $appointment['status'] === 'Completed' ? 'selected' : '' ?>>Completed</option>
                <option <?= $appointment['status'] === 'Canceled' ? 'selected' : '' ?>>Canceled</option>
            </select>
        </div>

        <div class="form-group">
            <label>Assigned To</label>
            <select id="assigned-to">
                <option value="">-- Select Supervisor --</option>
                <?php foreach ($supervisors as $supervisor): ?>
                    <option value="<?= $supervisor['supervisor_id'] ?>"
                        <?= $appointment['assigned_to'] == $supervisor['supervisor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($supervisor['name']) ?>
                        (<?= htmlspecialchars($supervisor['specialization'] ?? 'General') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group full-width">
            <label>Notes</label>
            <textarea id="notes" rows="4"><?= htmlspecialchars($appointment['notes'] ?? '') ?></textarea>
        </div>

        <div class="modal-footer full-width">
            <button class="cancel-button" onclick="window.location.href='<?= BASE_URL ?>/manager/appointments'">
                <i class="fas fa-times"></i> Cancel
            </button>
            <button class="save-button" onclick="updateAssignment()">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </div>

    </div>
</div>

<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/manager/updateApp.js"></script>

</body>
</html>