<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Schedule - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/newCustomer.css">
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
  <li><a href="/autonexus/receptionist/complaints">Complaints</a></li>
  <li><a href="/autonexus/receptionist/billing">Billing & Payments</a></li>
  <li class="active"><a href="/autonexus/receptionist/customers">Customer Profiles</a></li>
  <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
</ul>
  </div>
  
<div class="main">
    <div class="details-section">
      <h3>Customer Details</h3>
          <label>Customer:</label>
          <input type="text" id="customer">

          <label>Phone:</label>
          <input type="text" id="phone">

          <label>Email:</label>
          <input type="email" id="email">

          <label>ID:</label>
          <input type="text" id="id">

          <label>Vehicles:</label>
              <div id="vehicles-container">
                <div class="vehicle-entry">
                  <input type="text" name="vehicle[]" placeholder="Vehicle Name">
                  <input type="text" name="vehicle-number[]" placeholder="Vehicle Number">
                  <button type="button" class="remove-vehicle">❌</button>
                </div>
              </div>
                  <button type="button" id="add-vehicle">+ Add Vehicle</button>


          <label>Date:</label>
          <input type="date" id="Date">


       <div class="modal-footer">
      <button class="cancel-button">Cancel</button>
      <button class="save-button">Save</button>
    </div>
  </div>

    </div>
<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/r_js/newCustomer.js"></script>

  </body>
    </html>