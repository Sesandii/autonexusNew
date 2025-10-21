<?php
// app/views/manager/dashboard/dashboard.php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoNexus • Manager Dashboard</title>

  <!-- Manager styles -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/dashboard.css">

   <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">
  
  
  <!-- (Optional) Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>
 

  <div class="main">
    <div class="topbar">
      
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
        <p class="green">14.5% from last month</p>
      </div>
      <div class="card">
        <span class="icon">⚙️</span>
        <h4>Ongoing Services</h4>
        <h2>10</h2>
        <p class="green">14.5% from last month</p>
      </div>
      <div class="card">
        <span class="icon">📅</span>
        <h4>Appointments Today</h4>
        <h2>12</h2>
      </div>
    </div>

    <div class="content">
      <div class="activities">
        <h3>Recent Activities</h3>
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
              <td>Oil changing + Brake inspection</td>
              <td class="pending">Pending</td>
            </tr>
            <tr>
              <td>NDB 7195 <br> Nissan Leaf</td>
              <td>Oil changing + Brake inspection</td>
              <td class="progress">In Progress</td>
            </tr>
            <tr>
              <td>NDB 7195 <br> Nissan Leaf</td>
              <td>Oil changing + Brake inspection</td>
              <td class="completed">Completed</td>
            </tr>
            <tr>
              <td>NDB 7195 <br> Nissan Leaf</td>
              <td>Oil changing + Brake inspection</td>
              <td class="progress">In Progress</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="quick-links">
        <h3>Quick Links</h3>
        <div class="links">
          <div class="link">👨‍🔧<br>Add Mechanic</div>
          <div class="link">⚙️<br>Add Services</div>
          <div class="link">📅<br>View Schedule</div>
          <div class="link">📊<br>View Performance</div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
