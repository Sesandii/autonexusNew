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
      <h3>Invoice</h3>

      <form method="POST" action="<?= BASE_URL ?>/receptionist/complaints">

  
  <label>Name:</label>
  <input type="text" name="customer_name" required>

  <label>Phone:</label>
  <input type="text" name="phone">

  <label>Email:</label>
  <input type="email" name="email">

  <label>Vehicle:</label>
  <input type="text" name="vehicle">

  <label>Vehicle Number:</label>
  <input type="text" name="vehicle_number">

  <label>Date</label>
  <input type="date" name="complaint_date">

  <label>Time:</label>
  <input type="time" name="complaint_time">

  <label>Service:</label>
  <input name="Service" rows="4"></input>

  <label>Price:</label>
<div class="price-field">
  <span class="currency">Rs.</span>
  <input type="number" name="Price" placeholder="0.00">
</div>



  
  <div class="modal-footer">
    <button type="button" class="cancel-button" onclick="window.history.back()">Cancel</button>
    <button type="submit" class="save-button">Generate</button>
  </div>


      </form>

    </div>
  </div>
<script src="<?= BASE_URL ?>/public/assets/r_js/complaintsReceptionist.js"></script>
</body>
</html>
