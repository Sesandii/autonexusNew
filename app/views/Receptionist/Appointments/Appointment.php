<?php /** @var array $appointments */ ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Appointments -AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/Appointments.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/calender.css">
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
  <li class="active"><a href="/autonexus/receptionist/appointments">Appointments</a></li>
  <li><a href="/autonexus/receptionist/service">Service & Packages</a></li>
  <li><a href="/autonexus/receptionist/complaints">Complaints</a></li>
  <li><a href="/autonexus/receptionist/billing">Billing & Payments</a></li>
  <li><a href="/autonexus/receptionist/customers">Customer Profiles</a></li>
  <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
</ul>

  </div>

  <div class="main">
    <div class="header">
  <h2>Appointments</h2>

  <div class="top-actions">
    <!-- Create New Appointment Button -->
 <button class="add-btn" 
        onclick="window.location.href='<?= BASE_URL ?>/public/receptionist/appointments/new'">
  + Create New Appointment
</button>


    <!-- Search Appointment Bar -->
    <div class="search-bar">
      <input type="text" id="searchInput" placeholder="Search appointment..." />
      <button id="searchBtn">🔍</button>
    </div>
  </div>
</div>

<div class="main-content">
    <div class="calendar">
    <div class="calendar-header">
      <button id="prev">◀</button>
      <h2 id="month-year"></h2>
      <button id="next">▶</button>
    </div>
    <div class="calendar-grid" id="calendar-grid">
      <div class="day-name">Sun</div>
      <div class="day-name">Mon</div>
      <div class="day-name">Tue</div>
      <div class="day-name">Wed</div>
      <div class="day-name">Thu</div>
      <div class="day-name">Fri</div>
      <div class="day-name">Sat</div>
    </div>
  </div>

 

        <div class="schedule">
          <h4>Team Schedule -Today</h4>
          <div class="task">
            <p><b>John Smith</b> <br><span>Senior Mechanic</span><br>07:30 - 16:00</p>
          </div>
          <div class="task">
            <p><b>Maria Garcia</b> <br><span>Service Advisor</span><br>09:00 - 18:00</p>
          </div>
          <div class="task">
            <p><b>David Lee</b> <br><span>Technician</span><br>08:00 - 16:30</p>
          </div>
          <div class="task">
            <p><b>Sarah Johnson</b> <br><span>Parts Specialist</span><br>10:00 - 19:00</p>
          </div>
        </div>
      </div>

  </div>   
    
    </div>

      
  </div>

<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
<script src="<?= BASE_URL ?>/public/assets/r_js/calender.js"></script>
</body>
</html>
