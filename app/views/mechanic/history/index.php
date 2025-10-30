<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Fleet Management</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/mechanic/style-history.css">
</head>
<body>
  <div class="sidebar">
   <div class="logo-container">
     <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
    <h2>AUTONEXUS</h2>
    <a href="/autonexus/mechanic/dashboard" >
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="/autonexus/mechanic/jobs">
      <img src="/autonexus/public/assets/img/jobs.png"/>Jobs
    </a>
    <a href="/autonexus/mechanic/assignedjobs">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs
    </a>
    <a href="/autonexus/mechanic/history" class="nav">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
  </div>
    
    <main class="main">
     <header>
        <input type="text" placeholder="Search..." class="search" />
        <!--<div class="user-profile">
          <img src="/autonexus/public/assets/img/user.png" alt="User" class="avatar-img" />
          <span>John Doe</span>
        </div>-->
      </header>
      <h2>Vehicle History</h2>
      <div class="search-box">
        <div>
          <label>Vehicle<br>Number</label><br>
          <input type="text" id="vehicleNumber" placeholder="Enter vehicle number">
        </div>
        <div>
          <label>From<br>Date</label><br>
          <input type="date" id="fromDate">
        </div>
        <div>
          <label>To<br>Date</label><br>
          <input type="date" id="toDate">
        </div>
        <div>
          <button onclick="search()">Search</button>
        </div>
      </div>

      <div class="result" id="resultText">
        <a href="#">Search for a vehicle to view its history.</a>
      </div>
    </main>
  

  <script src="/autonexus/public/assets/js/mechanic/style-history.js"></script>
</body>
</html>
