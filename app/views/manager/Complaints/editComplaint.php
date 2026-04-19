<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Complaint</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newAppointment.css">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
    <div class="details-section">
        <h3>Edit Complaint</h3>

        <form action="<?= BASE_URL ?>/manager/complaints/update/<?= $complaint['complaint_id'] ?>" method="POST">

            <input type="hidden" name="customer_id" id="customer_id" value="<?= htmlspecialchars($complaint['customer_id']) ?>">
            <input type="hidden" name="vehicle_id"  id="vehicle_id"  value="<?= htmlspecialchars($complaint['vehicle_id'] ?? '') ?>">

            <div class="grid">

                <!-- ROW 1: Name + Phone -->
                <div class="field">
                    <label>Name</label>
                    <input type="text" id="customer_name" name="customer_name"
                        value="<?= htmlspecialchars($complaint['customer_name']) ?>" required>
                </div>

                <div class="field">
                    <label>Phone</label>
                    <input type="text" id="phone" name="phone"
                        value="<?= htmlspecialchars($complaint['phone']) ?>" required>
                </div>

                <!-- ROW 2: Email + Vehicle -->
                <div class="field">
                    <label>Email</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($complaint['email'] ?? '') ?>">
                </div>

                <div class="field">
                    <label>Vehicle</label>
                    <!-- JS populates this with a select -->
                    <div id="vehicle-container">
                        <input type="text" value="<?= htmlspecialchars($complaint['vehicle'] ?? '') ?>" readonly>
                    </div>
                </div>

                <!-- ROW 3: Vehicle Number + Assign Supervisor -->
                <div class="field">
                    <label>Vehicle number</label>
                    <input type="text" id="vehicle_number" name="vehicle_number"
                        value="<?= htmlspecialchars($complaint['vehicle_number'] ?? '') ?>" readonly>
                </div>

                <div class="field">
                    <label>Assign supervisor</label>
                    <select name="assigned_to" id="assigned_to">
                        <option value="">-- Select Supervisor --</option>
                        <?php foreach ($supervisors as $sup): ?>
                            <option value="<?= $sup['user_id'] ?>"
                                <?= ($complaint['assigned_to_user_id'] ?? '') == $sup['user_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($sup['first_name'] . ' ' . $sup['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
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
                        <option value="open"        <?= $complaint['status'] === 'open'        ? 'selected' : '' ?>>Open</option>
                        <option value="in_progress" <?= $complaint['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                        <option value="closed"      <?= $complaint['status'] === 'closed'      ? 'selected' : '' ?>>Closed</option>
                        <option value="resolved"    <?= $complaint['status'] === 'resolved'    ? 'selected' : '' ?>>Resolved</option>
                    </select>
                </div>

                <!-- Description (full width) -->
                <div class="field full">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?= htmlspecialchars($complaint['description']) ?></textarea>
                </div>

                <!-- Footer (full width) -->
                <div class="field full">
                    <div class="modal-footer">
                        <a href="<?= BASE_URL ?>/manager/complaints" class="cancel-button"
                            style="display:inline-block; text-decoration:none;">Cancel</a>
                        <button type="submit" class="save-button">Update Complaint</button>
                    </div>
                </div>

            </div><!-- /.grid -->

        </form>
    </div>
</div>

<script>
    const BASE_URL          = "<?= BASE_URL ?>";
    const COMPLAINT_VEHICLE = "<?= htmlspecialchars($complaint['vehicle'] ?? '') ?>";
    const COMPLAINT_PHONE   = "<?= htmlspecialchars($complaint['phone']   ?? '') ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/manager/editComplaint.js"></script>

</body>
</html>