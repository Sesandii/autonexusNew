<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Complaint - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newAppointment.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
    <div class="details-section">
        <h3>Edit Complaint</h3>

        <form action="<?= BASE_URL ?>/receptionist/complaints/update/<?= $complaint['complaint_id'] ?>" method="POST">

            <input type="hidden" name="customer_id" id="customer_id" value="<?= $complaint['customer_id'] ?>">
            <input type="hidden" name="vehicle_id"  id="vehicle_id"  value="<?= $complaint['vehicle_id'] ?>">
            <input type="hidden" name="appointment_id" value="<?= $complaint['appointment_id'] ?>">

            <div class="grid">

                <!-- ROW 1: Name + Phone -->
                <div class="field">
                    <label>Name</label>
                    <input type="text" name="customer_name" value="<?= htmlspecialchars($complaint['customer_name']) ?>" readonly>
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($complaint['phone']) ?>" readonly>
                </div>

                <!-- ROW 2: Email + Vehicle -->
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($complaint['email']) ?>" readonly>
                </div>

                <div class="field">
                    <label>Vehicle</label>
                    <div id="vehicle-container" data-complaint-vehicle-id="<?= $complaint['vehicle_id'] ?>">
                        <!-- JS populates this -->
                        <input type="text" value="<?= htmlspecialchars($complaint['vehicle'] ?? '') ?>" readonly>
                    </div>
                </div>

                <!-- ROW 3: Vehicle Number + Appointment -->
                <div class="field">
                    <label>Vehicle number</label>
                    <input type="text" name="vehicle_number" id="vehicle_number"
                        value="<?= htmlspecialchars($complaint['vehicle_number']) ?>" readonly>
                </div>

                <div class="field">
                    <label>Appointment</label>
                    <input type="text"
                        value="<?= htmlspecialchars($complaint['appointment_display'] ?? 'No appointment') ?>"
                        readonly>
                </div>

                <!-- ROW 4: Date + Time -->
                <div class="field">
                    <label>Date</label>
                    <input type="date" name="complaint_date"
                        value="<?= htmlspecialchars($complaint['complaint_date'] ?? '') ?>">
                </div>

                <div class="field">
                    <label>Time</label>
                    <input type="time" name="complaint_time"
                        value="<?= htmlspecialchars($complaint['complaint_time'] ?? '') ?>">
                </div>

                <!-- ROW 5: Priority + Status -->
                <div class="field">
                    <label>Priority</label>
                    <select name="priority">
                        <option value="Low"    <?= $complaint['priority'] === 'Low'    ? 'selected' : '' ?>>Low</option>
                        <option value="Medium" <?= $complaint['priority'] === 'Medium' ? 'selected' : '' ?>>Medium</option>
                        <option value="High"   <?= $complaint['priority'] === 'High'   ? 'selected' : '' ?>>High</option>
                    </select>
                </div>

            <div class="field">
    <label>Status</label>
    <select name="status">
        <option value="open" <?= $complaint['status'] === 'open' ? 'selected' : '' ?>>Open</option>

        <option value="in_progress" <?= $complaint['status'] === 'in_progress' ? 'selected' : '' ?>>
            In Progress
        </option>

        <option value="resolved" <?= $complaint['status'] === 'resolved' ? 'selected' : '' ?>>
            Resolved
        </option>

        <option value="closed" <?= $complaint['status'] === 'closed' ? 'selected' : '' ?>>
            Closed
        </option>
    </select>
</div>
                <!-- Subject (full width) -->
                <div class="field full">
                    <label>Subject</label>
                    <input type="text" name="subject" value="<?= htmlspecialchars($complaint['subject'] ?? '') ?>">
                </div>

                <!-- Description (full width) -->
                <div class="field full">
                    <label>Description</label>
                    <textarea name="description" rows="4"><?= htmlspecialchars($complaint['description']) ?></textarea>
                </div>

                <!-- Footer (full width) -->
                <div class="field full">
                    <div class="modal-footer">
                        <a href="<?= BASE_URL ?>/receptionist/complaints" class="cancel-button">Cancel</a>
                        <button type="submit" class="save-button">Update Complaint</button>
                    </div>
                </div>

            </div><!-- /.grid -->

        </form>
    </div>
</div>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/editComplaint.js"></script>

</body>
</html>