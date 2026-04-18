<?php
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
  <style>
    .readonly-field {
      background-color: #f5f5f5;
      cursor: not-allowed;
    }
    .editable-field {
      background-color: #fff;
      border: 1px solid #007bff;
    }
    .warning-message {
      color: #dc3545;
      font-size: 0.875rem;
      margin-top: 0.25rem;
      display: none;
    }
    .warning-message.show {
      display: block;
    }
  </style>
</head>
<body>
  
 <?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
    <div class="details-section">
      <h3>Update Appointment</h3>
      
      <form id="updateForm">
        <input type="hidden" id="appointment-id" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>">
        <input type="hidden" id="original-branch" value="<?= htmlspecialchars($appointment['branch_id']) ?>">
        
        <!-- Read-only fields -->
        <label>Customer:</label>
        <input type="text" class="readonly-field" value="<?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?>" readonly>

        <label>Phone:</label>
        <input type="text" class="readonly-field" value="<?= htmlspecialchars($appointment['phone'] ?? '') ?>" readonly>

        <label>Vehicle Number:</label>
        <input type="text" class="readonly-field" value="<?= htmlspecialchars($appointment['license_plate']) ?>" readonly>

        <label>Vehicle:</label>
        <input type="text" class="readonly-field" value="<?= htmlspecialchars($appointment['make'] . ' ' . $appointment['model']) ?>" readonly>

        <!-- Editable fields -->
        <label>Service: <span style="color: red;">*</span></label>
        <select id="service-id" name="service_id" class="editable-field" required>
          <?php foreach ($services as $service): ?>
            <option value="<?= $service['service_id'] ?>" 
              <?= $appointment['service_id'] == $service['service_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($service['name']) ?> - $<?= number_format($service['default_price'], 2) ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label>Branch: <span style="color: red;">*</span></label>
        <select id="branch-id" name="branch_id" class="editable-field" required>
          <?php foreach ($branches as $branch): ?>
            <option value="<?= $branch['branch_id'] ?>" 
              <?= $appointment['branch_id'] == $branch['branch_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($branch['name'] . ' - ' . $branch['city']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <div id="branch-warning" class="warning-message">
          <i class="fas fa-exclamation-triangle"></i> 
          Changing the branch will reset the assigned supervisor and set status to "Requested".
        </div>

        <label>Date: <span style="color: red;">*</span></label>
        <input type="date" id="appointment-date" name="appointment_date" class="editable-field" 
               value="<?= htmlspecialchars($appointment['appointment_date']) ?>" required>

        <label>Time: <span style="color: red;">*</span></label>
        <input type="time" id="appointment-time" name="appointment_time" class="editable-field" 
               value="<?= htmlspecialchars($appointment['appointment_time']) ?>" required>

        <!-- Read-only status and assigned supervisor -->
        <label>Current Status:</label>
        <input type="text" class="readonly-field" value="<?= htmlspecialchars($appointment['status']) ?>" readonly>

        <label>Currently Assigned To:</label>
        <input type="text" class="readonly-field" 
               value="<?= htmlspecialchars(($appointment['sup_first_name'] ?? '') . ' ' . ($appointment['sup_last_name'] ?? 'Not Assigned')) ?>" 
               readonly>

        <label>Notes:</label>
        <textarea id="notes" name="notes" class="editable-field" rows="4"><?= htmlspecialchars($appointment['notes'] ?? '') ?></textarea>

        <div class="modal-footer">
          <button type="button" class="cancel-button" onclick="window.location.href='<?= BASE_URL ?>/receptionist/appointments/day?date=<?= $appointment['appointment_date'] ?>'">Cancel</button>
          <button type="submit" class="save-button">Update Appointment</button>
        </div>
      </form>
    </div>
</div>

<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/updateApp.js"></script>

</body>
</html>