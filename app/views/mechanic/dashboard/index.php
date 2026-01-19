<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Nexus Dashboard</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-dashboard.css" />
</head>
<body>
<div class="sidebar">
  <div class="logo-container">
    <img src="<?= $base ?>/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>
  <h2>AUTONEXUS</h2>
  <a href="<?= $base ?>/mechanic/dashboard" class="nav">
    <img src="<?= $base ?>/public/assets/img/dashboard.png"/>Dashboard
  </a>
  <a href="<?= $base ?>/mechanic/jobs">
    <img src="<?= $base ?>/public/assets/img/jobs.png"/>Jobs
  </a>
  <a href="<?= $base ?>/mechanic/assignedjobs">
    <img src="<?= $base ?>/public/assets/img/assigned.png"/>Assigned Jobs
  </a>
  <a href="<?= $base ?>/mechanic/history">
    <img src="<?= $base ?>/public/assets/img/history.png"/>Vehicle History
  </a>
</div>

<main class="main-content">
<header>
  <input type="text" placeholder="Search..." class="search" />
  <div class="user-profile">
    <div class="user">
      <img src="<?= $base ?>/public/assets/img/user.png" alt="User" class="user-img" />
      <div class="user-menu">
        <span id="user-name"><?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'User') ?></span>
        <ul id="dropdown" class="dropdown hidden">
          <li><a href="#">Edit Profile</a></li>
          <li><a href="<?= $base ?>/logout">Sign Out</a></li>
        </ul>
      </div>
    </div>
  </div>
</header>

<section class="welcome">
  <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['first_name'] ?? 'Mechanic') ?></h2>
  <p>Here's an overview of your dashboard</p>
</section>

<section class="cards">
  <div class="card green">
    <div class="card-header">
      <img src="<?= $base ?>/public/assets/img/done.png" class="card-icon" />
      <h3>Jobs Done</h3>
    </div>
    <p><?= $stats['jobs_done'] ?? 0 ?></p>
    <span class="change"></span>
  </div>

  <div class="card blue">
    <div class="card-header">
      <img src="<?= $base ?>/public/assets/img/assigned2.png" class="card-icon" />
      <h3>Assigned Jobs</h3>
    </div>
    <p><?= $stats['assigned_jobs'] ?? 0 ?></p>
    <span class="change"></span>
  </div>

  <div class="card red">
    <div class="card-header">
      <img src="<?= $base ?>/public/assets/img/ongoing.png" class="card-icon" />
      <h3>Ongoing Appointments</h3>
    </div>
    <p><?= $stats['ongoing'] ?? 0 ?></p>
    <span class="change"></span>
  </div>

  <div class="card purple">
    <div class="card-header">
      <img src="<?= $base ?>/public/assets/img/total.png" class="card-icon" />
      <h3>Total Jobs</h3>
    </div>
    <p><?= $stats['total_jobs'] ?? 0 ?></p>
    <span class="change"></span>
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
      <?php if (!empty($appointments)): ?>
        <?php foreach ($appointments as $appt): ?>
          <tr>
            <td><img src="<?= $base ?>/public/assets/img/user2.png" class="user-icon" /> <?= htmlspecialchars($appt['customer_name']) ?></td>
            <td><img src="<?= $base ?>/public/assets/img/car.png" class="icon-car" /> <?= htmlspecialchars($appt['license_plate']) ?></td>
            <td><?= htmlspecialchars(date('h:i A', strtotime($appt['time_slot']))) ?></td>
            <td><?= htmlspecialchars($appt['service_name']) ?></td>
            <td><span class="badge <?= strtolower($appt['status']) ?>"><?= ucfirst($appt['status']) ?></span></td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align:center;">No appointments today</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>

</main>
<script src="<?= $base ?>/public/assets/js/mechanic/script-dashboard.js"></script>
</body>
</html>
