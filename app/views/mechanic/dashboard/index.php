<?php $base = rtrim(BASE_URL, '/'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Nexus Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/mechanic/style-dashboard.css" />
</head>
<body>
    <div class="sidebar">
     <div class="logo-container">
     <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
      <h2>AUTONEXUS</h2>
    <a href="/autonexus/mechanic/dashboard" class="nav">
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="/autonexus/mechanic/jobs">
      <img src="/autonexus/public/assets/img/jobs.png"/>Jobs
    </a>
    <a href="/autonexus/mechanic/assignedjobs">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs
    </a>
    <a href="/autonexus/mechanic/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
    </div>

    <main class="main-content">
  <header>
    <input type="text" placeholder="Search..." class="search" />
    
    <div class="user-profile">
      <!-- User Menu -->
      <div class="user">
        <img src="/autonexus/public/assets/img/user.png" alt="User" class="user-img" />
        <div class="user-menu">
          <span id="user-name">John Doe</span>
          <ul id="dropdown" class="dropdown hidden">
            <li><a href="#">Edit Profile</a></li>
            <li><a href="<?= $base ?>/logout">Sign Out</a></li>
            <!-- <li><a href="..\Signin\index.html">Sign Out</a></li> -->
          </ul>
        </div>
      </div>
    </div>
  </header>


      <section class="welcome">
        <h2>Welcome, John Doe</h2>
        <p>Here's an overview of your dashboard</p>
      </section>

      <section class="cards">
        <div class="card green">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/done.png" class="card-icon" />
            <h3>Jobs Done</h3>
          </div>
          <p>128</p>
          <span class="change">+12.5% vs last month</span>
        </div>

        <div class="card blue">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/assigned2.png" class="card-icon" />
            <h3>Assigned Jobs</h3>
          </div>
          <p>45</p>
          <span class="change">+8.2% vs last month</span>
        </div>

        <div class="card red">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/ongoing.png" class="card-icon" />
            <h3>Ongoing Appointments</h3>
          </div>
          <p>32</p>
          <span class="change">-1.8% vs last month</span>
        </div>

        <div class="card purple">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/total.png" class="card-icon" />
            <h3>Total Jobs</h3>
          </div>
          <p>205</p>
          <span class="change">+6.4% vs last month</span>
        </div>
      </section>

      <section class="appointments">
        <h3>Today's Appointments</h3>
        <table>
          <thead>
            <tr>
              <th>Client</th>
              <th>Vehicle</th>
              <th>Time</th>
              <th>Service</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><img src="/autonexus/public/assets/img/user2.png" class="user-icon" /> Mike Wilson</td>
              <td><img src="/autonexus/public/assets/img/car.png" class="icon-car" /> Ford Focus (2019)</td>
              <td>12:00 AM</td>
              <td>General service</td>
              <td><span class="badge upcoming">Waiting</span></td>
            </tr>
            <tr>
              <td><img src="/autonexus/public/assets/img/user2.png" class="user-icon" /> Willy walker</td>
              <td><img src="/autonexus/public/assets/img/car.png" class="icon-car" /> Honda Civic (2020)</td>
              <td>11:30 AM</td>
              <td>Wheel alignment</td>
              <td><span class="badge upcoming">Confirmed</span></td>
            </tr>
            <tr>
              <td><img src="/autonexus/public/assets/img/user2.png" class="user-icon" /> Robert Brown</td>
              <td><img src="/autonexus/public/assets/img/car.png" class="icon-car" /> Ford Raptor (2018)</td>
              <td>11:45 AM</td>
              <td>Suspension Inspection</td>
              <td><span class="badge confirmed">Upcoming</span></td>
            </tr>
            <tr>
              <td><img src="/autonexus/public/assets/img/user2.png" class="user-icon" /> Ben johnson</td>
              <td><img src="/autonexus/public/assets/img/car.png" class="icon-car" /> Chevrolet Bronco (2021)</td>
              <td>2:15 PM</td>
              <td>Full Inspection</td>
              <td><span class="badge confirmed">Waiting</span></td>
            </tr>
            <tr>
              <td><img src="/autonexus/public/assets/img/user2.png" class="user-icon" /> Michael Thompson</td>
              <td><img src="/autonexus/public/assets/img/car.png" class="icon-car" /> Nissan 370z (2017)</td>
              <td>4:00 PM</td>
              <td>Engine Diagnostics</td>
              <td><span class="badge waiting">Confirmed</span></td>
            </tr>
          </tbody>
        </table>
      </section>
    </main>
  <script src="/autonexus/public/assets/js/mechanic/script-dashboard.js"></script>
</body>
</html>
