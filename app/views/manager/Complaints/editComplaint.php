<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Complaint</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/editComplaint.css">
  </head>
  
<body>
  
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

  <div class="main">
    <div class="details-section">
      <h3>Edit Complaint</h3>
      <form action="<?= BASE_URL ?>/manager/complaints/update/<?= $complaint['complaint_id'] ?>" method="POST">
        
        <input type="hidden" name="customer_id" id="customer_id" value="<?= $complaint['customer_id'] ?>">
        <input type="hidden" name="user_id" id="user_id" value="<?= $complaint['user_id'] ?>">
        
        <label for="customer_name">Name:</label>
        <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($complaint['customer_name']) ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($complaint['phone']) ?>" required>

        <label for="email">Email:</label>
       
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($complaint['email']) ?>">

<!-- Vehicle Dropdown -->
<label>Vehicle:</label>
<div id="vehicle-container" data-complaint-vehicle-id="<?= $complaint['vehicle_id'] ?>"></div>
<input type="hidden" name="vehicle_id" id="vehicle_id" value="<?= $complaint['vehicle_id'] ?>">

<label for="vehicle_number">Vehicle Number:</label>
<input type="text" id="vehicle_number" name="vehicle_number" value="<?= htmlspecialchars($complaint['vehicle_number']) ?>" readonly>

<label>Date:</label>
  <input type="date" name="complaint_date">

  <label>Time:</label>
  <input type="time" name="complaint_time">

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
  <option value="closed" <?= $complaint['status'] == 'closed' ? 'selected' : '' ?>>Closed</option>
  <option value="resolved" <?= $complaint['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
</select>

<label for="assigned_to">Assign Supervisor:</label>
<select name="assigned_to" id="assigned_to">
    <option value="">-- Select Supervisor --</option>
    <?php foreach ($supervisors as $sup): ?>
        <option value="<?= $sup['user_id'] ?>" <?= $complaint['assigned_to_user_id'] == $sup['user_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($sup['first_name'] . ' ' . $sup['last_name']) ?>
        </option>
    <?php endforeach; ?>
</select>

      
        <div class="modal-footer">
          <a href="<?= BASE_URL ?>/manager/complaints" class="cancel-button">Cancel</a>
          <button type="submit" class="save-button"> Update </button>
        </div>
      </form>
    </div>
  </div>

 <script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/js/manager/editComplaint.js"></script>
</body>
</html>
