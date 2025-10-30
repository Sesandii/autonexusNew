<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>New Complaint - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/newComplaint.css">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  <div class="sidebar">
    <div class="logo">
      <img src="<?= BASE_URL ?>/public/assets/img/Auto.png" alt="AutoNexus Logo">
      <h2>AUTONEXUS</h2>
      <p>VEHICLE SERVICE</p>
    </div>
    <ul class="menu">
  <li><a href="/autonexus/receptionist/dashboard">Dashboard</a></li>
  <li><a href="/autonexus/receptionist/appointments">Appointments</a></li>
  <li><a href="/autonexus/receptionist/service">Service & Packages</a></li>
  <li class="active"><a href="/autonexus/receptionist/complaints">Complaints</a></li>
  <li><a href="/autonexus/receptionist/billing">Billing & Payments</a></li>
  <li><a href="/autonexus/receptionist/customers">Customer Profiles</a></li>
  <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
</ul>
  </div>

  <div class="main">
    <div class="details-section">
      <h3>Complaint Details</h3>

      <form method="POST" action="<?= BASE_URL ?>/receptionist/complaints">

    
  <label>Customer:</label>
  <input type="text" name="customer_name" required>

  <label>Phone:</label>
  <input type="text" name="phone">

  <label>Email:</label>
  <input type="email" name="email">

  <label>Vehicle:</label>
  <input type="text" name="vehicle">

  <label>Vehicle Number:</label>
  <input type="text" name="vehicle_number">

  <label>Date:</label>
  <input type="date" name="complaint_date">

  <label>Time:</label>
  <input type="time" name="complaint_time">

  <label>Description:</label>
  <textarea name="description" rows="4"></textarea>

  <label>Priority:</label>
  <select name="priority">
    <option>High</option>
    <option selected>Medium</option>
    <option>Low</option>
  </select>

  <label>Status:</label>
  <select name="status">
    <option>Open</option>
    <option>In Progress</option>
    <option>Resolved</option>
    <option>Canceled</option>
  </select>

  <label>Assigned to:</label>
  <select name="assigned_to">
    <option>Mike Johnson</option>
    <option>John Smith</option>
    <option>Maria Gracia</option>
    <option>David Lee</option>
  </select>

  <div class="modal-footer">
    <button type="button" class="cancel-button" onclick="window.history.back()">Cancel</button>
    <button type="submit" class="save-button">Save</button>
  </div>


      </form>

    </div>
  </div>
<script src="<?= BASE_URL ?>/public/assets/r_js/complaintsReceptionist.js"></script>
</body>
</html>
