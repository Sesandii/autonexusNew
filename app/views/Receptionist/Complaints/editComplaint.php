<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Complaint</title>
  
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/editComplaint.css?v=1.1">

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <img src="<?= BASE_URL ?>/public/assets/img/Auto.png" alt="AutoNexus Logo">
      <h2>AUTONEXUS</h2>
      <p>VEHICLE SERVICE</p>
    </div>
    <ul class="menu">
      <li><a href="<?= BASE_URL ?>/receptionist/dashboard">Dashboard</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/appointments">Appointments</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/service">Service & Packages</a></li>
      <li class="active"><a href="<?= BASE_URL ?>/receptionist/complaints">Complaints</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/billing">Billing & Payments</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/customers">Customer Profiles</a></li>
      <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
    </ul>
  </div>

  <div class="main">
    <div class="details-section">
      <h3>Edit Complaint</h3>
      <form action="<?= BASE_URL ?>/receptionist/complaints/update/<?= $complaint['complaint_id'] ?>" method="POST">

        <label for="customer_name">Name:</label>
        <input type="text" id="customer_name" name="customer_name" value="<?= htmlspecialchars($complaint['customer_name']) ?>" required>

        <label for="phone">Phone:</label>
        <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($complaint['phone']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($complaint['email']) ?>">

        <label for="vehicle">Vehicle:</label>
        <input type="text" id="vehicle" name="vehicle" value="<?= htmlspecialchars($complaint['vehicle']) ?>">

        <label for="vehicle_number">License:</label>
        <input type="text" id="vehicle_number" name="vehicle_number" value="<?= htmlspecialchars($complaint['vehicle_number']) ?>">

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
          <option value="Open" <?= $complaint['status'] == 'Open' ? 'selected' : '' ?>>Open</option>
          <option value="In Progress" <?= $complaint['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress</option>
          <option value="Resolved" <?= $complaint['status'] == 'Resolved' ? 'selected' : '' ?>>Resolved</option>
          <option value="Canceled" <?= $complaint['status'] == 'Canceled' ? 'selected' : '' ?>>Canceled</option>
        </select>

        <label for="assigned_to">Assigned To:</label>
        <input type="text" id="assigned_to" name="assigned_to" value="<?= htmlspecialchars($complaint['assigned_to']) ?>">

        <div class="modal-footer">
          <a href="<?= BASE_URL ?>/receptionist/complaints" class="cancel-button">Cancel</a>
          <button type="submit" class="save-button">💾 Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</body>
</html>
