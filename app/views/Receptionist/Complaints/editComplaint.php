<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Complaint</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/editComplaint.css?v=1.1">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  </head>
  
<body>
  
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

  <div class="main">
    <div class="details-section">
      <h3>Edit Complaint</h3>
      <form action="<?= BASE_URL ?>/receptionist/complaints/update/<?= $complaint['complaint_id'] ?>" method="POST">
        
        <input type="hidden" name="customer_id" id="customer_id" value="<?= $complaint['customer_id'] ?>">
        
        
        <label for="customer_name">Name:</label>
        <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($complaint['customer_name']) ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($complaint['phone']) ?>" required>

        <label for="email">Email:</label>
       
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($complaint['email']) ?>">

<label> Vehicle:</label>
<div id="vehicle-container" data-complaint-vehicle-id="<?= $complaint['vehicle_id'] ?>"></div>
<input type="hidden" name="vehicle_id" id="vehicle_id" value="<?= $complaint['vehicle_id'] ?>">

<label for="vehicle_number">Vehicle Number:</label>
<input type="text" id="vehicle_number" name="vehicle_number" value="<?= htmlspecialchars($complaint['vehicle_number']) ?>" readonly>

<label>Appointment:</label>
<input type="text" 
       value="<?= htmlspecialchars($complaint['appointment_display'] ?? 'No appointment') ?>" 
       readonly>

<input type="hidden" name="appointment_id" value="<?= $complaint['appointment_id'] ?>">

<label>Date:</label>
  <input type="date" name="complaint_date">

  <label>Time:</label>
  <input type="time" name="complaint_time">

  <label>Subject:</label>
<input type="text" name="subject" value="<?= $complaint['subject'] ?>">

        <label for="description">Description:</label>
        <textarea id="description" name="description" required><?= htmlspecialchars($complaint['description']) ?></textarea>

        <label for="priority">Priority:</label>
        <select id="priority" name="priority">
          <option value="Low" <?= $complaint['priority'] == 'Low' ? 'selected' : '' ?>>Low</option>
          <option value="Medium" <?= $complaint['priority'] == 'Medium' ? 'selected' : '' ?>>Medium</option>
          <option value="High" <?= $complaint['priority'] == 'High' ? 'selected' : '' ?>>High</option>
        </select>

        <label for="status">Status:</label>
        <select id="status" name="status">
  <option value="open" <?= $complaint['status'] == 'open' ? 'selected' : '' ?>>Open</option>

  <option value="in_progress" <?= $complaint['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>

  <option value="resolved" <?= $complaint['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>

  <option value="closed" <?= $complaint['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
</select>

      
        <div class="modal-footer">
          <a href="<?= BASE_URL ?>/receptionist/complaints" class="cancel-button">Cancel</a>
          <button type="submit" class="save-button"> Update </button>
        </div>
      </form>
    </div>
  </div>

 <script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/receptionist/editComplaint.js"></script>
</body>
</html>
