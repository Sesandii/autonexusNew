<?php
// Set the active page for the sidebar
$activePage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoNexus Dashboard</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/dashboard/dashboard.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>

  <!-- Include the Sidebar -->
  <?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

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
