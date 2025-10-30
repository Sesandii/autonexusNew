<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoNexus Dashboard</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/dashboard -Receptionist.css">
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
  <li class="active"><a href="/autonexus/receptionist/dashboard">Dashboard</a></li>
  <li><a href="/autonexus/receptionist/appointments">Appointments</a></li>
  <li><a href="/autonexus/receptionist/service">Service & Packages</a></li>
  <li><a href="/autonexus/receptionist/complaints">Complaints</a></li>
  <li><a href="/autonexus/receptionist/billing">Billing & Payments</a></li>
  <li><a href="/autonexus/receptionist/customers">Customer Profiles</a></li>
  <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
</ul>
 
</div>


  <div class="main">
    <div class="topbar">
      <input type="text" placeholder="Enter Vehicle Number">
      <div class="user">
        <span class="user-icon">👤</span>
        <span class="username">Ana Bell</span>
        <span class="bell">🔔</span>
      </div>
    </div>

    <div class="cards">
      <div class="card">
        <span class="icon">⚙️</span>
        <h4>Pending Services</h4>
        <h2>5</h2>
        <p class="green">14.5% form last month</p>
      </div>
      <div class="card">
        <span class="icon">⚙️</span>
        <h4>Ongoing Services</h4>
        <h2>10</h2>
        <p class="green">14.5% form last month</p>
      </div>
      <div class="card">
        <span class="icon">📅</span>
        <h4>Appointments Today</h4>
        <h2>12</h2>
      </div>
    </div>

    <div class="content">
      <div class="activities">
        <h3>Recent Activites</h3>
        <table>
          <thead>
            <tr>
              <th>Vehicle</th>
              <th>Service</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>CAB 1895 <br> Toyota Aqua</td>
              <td>Oil changing + Break inspection</td>
              <td class="pending">Pending</td>
            </tr>
            <tr>
              <td>NDB 7195 <br> Nissan Leaf</td>
              <td>Oil changing + Break inspection</td>
              <td class="progress">In Progress</td>
            </tr>
            <tr>
              <td>NDB 7195 <br> Nissan Leaf</td>
              <td>Oil changing + Break inspection</td>
              <td class="completed">Completed</td>
            </tr>
            <tr>
              <td>NDB 7195 <br> Nissan Leaf</td>
              <td>Oil changing + Break inspection</td>
              <td class="progress">In Progress</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="quick-links">
        <h3>Quick Links</h3>

        <div class="links">
        <a href="<?= BASE_URL ?>/receptionist/customers/new" class="link-block">
            <div class="link">👨‍🔧<br>Add New Customer</div>
        </a>
        
        <a href="<?= BASE_URL ?>/receptionist/appointments/new" class="link-block">
            <div class="link">⚙️<br>Add New Appointment</div>
        </a>
        
        <a href="<?= BASE_URL ?>/receptionist/invoices/new" class="link-block">
            <div class="link">📅<br>Create Invoice</div>
        </a>

        <a href="<?= BASE_URL ?>/receptionist/complaints/new" class="link-block">
            <div class="link">📊<br>New Complaint</div>
        </a>
      </div>

      </div>
    </div>
  </div>
</body>
</html>
