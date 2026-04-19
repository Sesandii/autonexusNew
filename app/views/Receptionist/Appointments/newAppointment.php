<?php
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
    <div class="details-section">
        <h3>Appointment Details</h3>

        <form id="appointmentForm">

            <div class="grid">

                <!-- ROW 1: Phone + Customer Name -->
                <div class="field">
                    <label>Phone</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter phone number">
                </div>

                <div class="field">
                    <label>Customer Name</label>
                    <input type="text" id="customer" name="customer_name" readonly placeholder="Auto-filled">
                </div>

                <!-- Hidden fields -->
                <input type="hidden" id="customer_id" name="customer_id">
                <input type="hidden" id="vehicle_id"  name="vehicle_id">
                <input type="hidden" id="branch_id"   name="branch_id">

                <!-- ROW 2: Vehicle Select + Vehicle Number -->
                <div class="field">
                    <label>Vehicle</label>
                    <select id="vehicle-select" name="vehicle_select">
                        <option value="">-- Select Vehicle --</option>
                    </select>
                </div>

                <div class="field">
                    <label>Vehicle Number</label>
                    <input type="text" id="vehicle-number" name="vehicle_number" readonly placeholder="Auto-filled">
                </div>

                <!-- ROW 3: Date + Time -->
                <div class="field">
                    <label>Date</label>
                    <input type="date" id="Date" name="appointment_date">
                </div>

                <div class="field">
                    <label>Time</label>
                    <input type="time" id="Time" name="appointment_time">
                </div>

                <!-- ROW 4: Service + Status -->
                <div class="field">
                    <label>Service</label>
                    <select id="service" name="service_id">
                        <option value="">-- Select Service or Package --</option>
                        <?php if (!empty($services)): ?>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['service_id'] ?>">
                                    <?= htmlspecialchars($service['name']) ?> (Rs.<?= number_format($service['default_price'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if (!empty($packages)): ?>
                            <?php foreach ($packages as $package): ?>
                                <option value="package_<?= htmlspecialchars($package['package_code']) ?>">
                                    <?= htmlspecialchars($package['name']) ?> (Rs.<?= number_format($package['total_price'], 2) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select id="status" name="status">
                        <option value="Requested">Requested</option>
                        <option value="Postponed">Postponed</option>
                        <option value="Waiting">Waiting</option>
                        <option value="In Service">In Service</option>
                        <option value="Completed">Completed</option>
                        <option value="Canceled">Canceled</option>
                    </select>
                </div>

                <!-- ROW 5: Branch (full width) -->
                <div class="field full">
                    <label>Branch</label>
                    <input list="branch-list" id="branch-search" name="branch_search" placeholder="Type branch name...">
                    <datalist id="branch-list">
                        <?php foreach ($branches as $branch): ?>
                            <option
                                data-id="<?= $branch['branch_id'] ?>"
                                value="<?= htmlspecialchars($branch['name'] . ' (' . $branch['city'] . ')') ?>">
                            </option>
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <!-- FOOTER (full width) -->
                <div class="field full">
                    <div class="modal-footer">
                        <button type="button" class="cancel-button">Cancel</button>
                        <button type="submit" class="save-button">Save Appointment</button>
                    </div>
                </div>

            </div><!-- /.grid -->

        </form>
    </div>
</div>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>

<script src="<?= BASE_URL ?>/public/assets/js/receptionist/newAppointment.js"></script>

</body>
</html>