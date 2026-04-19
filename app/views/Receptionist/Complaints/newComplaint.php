<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Complaint - AutoNexus</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/newAppointment.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">
    <div class="details-section">
        <h3>Complaint Details</h3>

        <form method="POST" action="<?= BASE_URL ?>/receptionist/complaints">

            <input type="hidden" name="customer_id" id="customer_id">
            <input type="hidden" name="vehicle_id"  id="vehicle_id">

            <div class="grid">

                <!-- ROW 1: Phone + Customer -->
                <div class="field">
                    <label>Phone</label>
                    <input type="text" name="phone" id="phone" placeholder="Enter phone number">
                </div>

                <div class="field">
                    <label>Customer</label>
                    <input type="text" name="customer_name" id="customer_name" readonly placeholder="Auto-filled">
                </div>

                <!-- ROW 2: Email + Vehicle -->
                <div class="field">
                    <label>Email</label>
                    <input type="email" name="email" id="email" readonly placeholder="Auto-filled">
                </div>

                <!-- Vehicle container — JS swaps the input for a select when vehicles load -->
                <div class="field">
                    <label>Vehicle</label>
                    <div id="vehicle-container">
                        <input type="text" name="vehicle" readonly placeholder="Auto-filled">
                    </div>
                </div>

                <!-- ROW 3: Vehicle Number + Related Appointment -->
                <div class="field">
                    <label>Vehicle number</label>
                    <input type="text" name="vehicle_number" id="vehicle_number" readonly placeholder="Auto-filled">
                </div>

                <div class="field">
                    <label>Related appointment <span style="color:#6b7280;font-weight:400">(optional)</span></label>
                    <select name="appointment_id" id="appointment_id">
                        <option value="">None</option>
                    </select>
                </div>

                <!-- ROW 4: Date + Time -->
                <div class="field">
                    <label>Date</label>
                    <input type="date" name="complaint_date" id="complaint_date">
                </div>

                <div class="field">
                    <label>Time</label>
                    <input type="time" name="complaint_time" id="complaint_time">
                </div>

                <!-- ROW 5: Priority + Status -->
                <div class="field">
                    <label>Priority</label>
                    <select name="priority">
                        <option>High</option>
                        <option selected>Medium</option>
                        <option>Low</option>
                    </select>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        <option>Open</option>
                        <option>In Progress</option>
                        <option>Resolved</option>
                        <option>Canceled</option>
                    </select>
                </div>

                <!-- Subject (full width) -->
                <div class="field full">
                    <label>Subject</label>
                    <input type="text" name="subject" placeholder="Enter complaint subject">
                </div>

                <!-- Description (full width) -->
                <div class="field full">
                    <label>Description</label>
                    <textarea name="description" rows="4" placeholder="Describe the complaint..."></textarea>
                </div>

                <!-- Footer (full width) -->
                <div class="field full">
                    <div class="modal-footer">
                        <button type="button" class="cancel-button" onclick="window.history.back()">Cancel</button>
                        <button type="submit" class="save-button">Save Complaint</button>
                    </div>
                </div>

            </div><!-- /.grid -->

        </form>
    </div>
</div>

<script>
    const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/y.js"></script>

</body>
</html>