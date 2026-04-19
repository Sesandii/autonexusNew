<?php
$activePage = 'appointments';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Appointment - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newAppointment.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
    <div class="details-section">
        <h3>Update Appointment</h3>

        <form id="updateForm">

            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($appointment['appointment_id']) ?>">
            <input type="hidden" id="original-branch" value="<?= htmlspecialchars($appointment['branch_id']) ?>">

            <div class="grid">

                <!-- ROW 1: Customer + Phone -->
                <div class="field">
                    <label>Customer</label>
                    <input type="text" value="<?= htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']) ?>" readonly>
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="text" value="<?= htmlspecialchars($appointment['phone'] ?? '') ?>" readonly>
                </div>

                <!-- ROW 2: Vehicle Number + Vehicle -->
                <div class="field">
                    <label>Vehicle number</label>
                    <input type="text" value="<?= htmlspecialchars($appointment['license_plate']) ?>" readonly>
                </div>

                <div class="field">
                    <label>Vehicle</label>
                    <input type="text" value="<?= htmlspecialchars($appointment['make'] . ' ' . $appointment['model']) ?>" readonly>
                </div>

                <!-- ROW 3: Service + Branch -->
                <div class="field">
                    <label>Service <span style="color:#e84040">*</span></label>
                    <select name="service_id" required>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service['service_id'] ?>"
                                <?= $appointment['service_id'] == $service['service_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($service['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Branch <span style="color:#e84040">*</span></label>
                    <select name="branch_id" id="branch-select" required>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['branch_id'] ?>"
                                <?= $appointment['branch_id'] == $branch['branch_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($branch['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Branch warning (full width) -->
                <div class="field full" id="branch-warning" style="display:none;">
                    <div style="
                        background: #fff8e1;
                        border: 1px solid #f9c825;
                        border-radius: 8px;
                        padding: 10px 14px;
                        font-size: 13px;
                        color: #7c5e00;
                        display: flex;
                        align-items: center;
                        gap: 8px;
                    ">
                        <i class="fas fa-exclamation-triangle" style="font-size:14px;color:#f9c825;"></i>
                        Changing branch will reset the assigned supervisor and status.
                    </div>
                </div>

                <!-- ROW 4: Date + Time -->
                <div class="field">
                    <label>Date</label>
                    <input type="date" name="appointment_date" value="<?= htmlspecialchars($appointment['appointment_date']) ?>">
                </div>

                <div class="field">
                    <label>Time</label>
                    <input type="time" name="appointment_time" value="<?= htmlspecialchars($appointment['appointment_time']) ?>">
                </div>

                <!-- ROW 5: Status + Assigned To (full width each) -->
                <div class="field">
                    <label>Current status</label>
                    <input type="text" value="<?= htmlspecialchars($appointment['status']) ?>" readonly>
                </div>

                <div class="field">
                    <label>Assigned to</label>
                    <input type="text"
                        value="<?= htmlspecialchars(
                            trim(($appointment['sup_first_name'] ?? '') . ' ' . ($appointment['sup_last_name'] ?? '')) ?: 'Not assigned'
                        ) ?>"
                        readonly>
                </div>

                <!-- Notes (full width) -->
                <div class="field full">
                    <label>Notes</label>
                    <textarea name="notes" rows="3"><?= htmlspecialchars($appointment['notes'] ?? '') ?></textarea>
                </div>

                <!-- Footer (full width) -->
                <div class="field full">
                    <div class="modal-footer">
                        <button type="button" class="cancel-button"
                            onclick="window.location.href='<?= BASE_URL ?>/receptionist/appointments/day?date=<?= $appointment['appointment_date'] ?>'">
                            Cancel
                        </button>
                        <button type="submit" class="save-button">
                            Update Appointment
                        </button>
                    </div>
                </div>

            </div><!-- /.grid -->

        </form>
    </div>
</div>

<script>
const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/updateApp.js"></script>

</body>
</html>