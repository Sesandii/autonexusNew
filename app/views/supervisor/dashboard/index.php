<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Nexus Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-dashboard.css" />
</head>
<body>
    <div class="sidebar">
     <div class="logo-container">
     <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
     </div>
      <h2>AUTONEXUS</h2>
      <a href="<?= $base ?>/supervisor/dashboard" class="nav">
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="<?= $base ?>/supervisor/workorders">
      <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
    </a>
    <a href="<?= $base ?>/supervisor/assignedjobs">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned 
    </a>
    <a href="<?= $base ?>/supervisor/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
    <a href="<?= $base ?>/supervisor/complaints">
      <img src="/autonexus/public/assets/img/Complaints.png"/>Complaints
     </a>
      <a href="<?= $base ?>/supervisor/feedbacks">
      <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
     </a>
      <a href="<?= $base ?>/supervisor/reports">
       <img src="/autonexus/public/assets/img/Inspection.png"/>Report
     </a>
    </div>

    <main class="main-content">
      <header>
        <input type="text" placeholder="Search..." class="search" />
        <div class="user-profile">
          <div class="user">
            <img src="/autonexus/public/assets/img/user.png" alt="User" class="user-img" />
            <div class="user-menu">
              <span id="user-name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Supervisor') ?></span>
              <ul id="dropdown" class="dropdown hidden">
              <li><a href="<?= $base ?>/supervisor/profile/edit">Edit Profile</a></li>
                <li><a href="<?= $base ?>/logout">Sign Out</a></li>
              </ul>
            </div>
          </div>
        </div>
      </header>

      <section class="welcome">
        <h2>Welcome, <?= htmlspecialchars($_SESSION['user']['name'] ?? 'Supervisor') ?></h2>
        <p>Here's an overview of your dashboard</p>
      </section>

      <section class="cards">
        <div class="card green">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/done.png" class="card-icon" />
            <h3>Workorders Done</h3>
          </div>
          <p><?= (int)($stats['completed'] ?? 0) ?></p>
        </div>

        <div class="card blue">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/assigned2.png" class="card-icon" />
            <h3>Assigned Jobs</h3>
          </div>
          <p><?= (int)($stats['total'] ?? 0) - (int)($stats['completed'] ?? 0) ?></p>
        </div>

        <div class="card red">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/ongoing.png" class="card-icon" />
            <h3>Ongoing Workorders</h3>
          </div>
          <p><?= (int)($stats['ongoing'] ?? 0) ?></p>
        </div>

        <div class="card purple">
          <div class="card-header">
            <img src="/autonexus/public/assets/img/total.png" class="card-icon" />
            <h3>Total Workorders</h3>
          </div>
          <p><?= (int)($stats['total'] ?? 0) ?></p>
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
            <?php if (empty($appointments)): ?>
              <tr>
                <td colspan="5">No appointments today</td>
              </tr>
            <?php else: ?>
              <?php foreach ($appointments as $a): ?>
              <tr>
                <td>
                  <img src="/autonexus/public/assets/img/user2.png" class="user-icon" /> 
                  <?= htmlspecialchars($a['client_name'] ?? 'N/A') ?>
                </td>
                <td>
                  <img src="/autonexus/public/assets/img/car.png" class="icon-car" /> 
                  <?= htmlspecialchars($a['vehicle'] ?? 'N/A') ?>
                </td>
                <td><?= htmlspecialchars($a['appointment_time'] ?? '') ?></td>
                <td><?= htmlspecialchars($a['service_name'] ?? 'N/A') ?></td>
                <td>
                  <span class="badge <?= strtolower($a['status'] ?? '') ?>">
                    <?= ucfirst($a['status'] ?? 'Unknown') ?>
                  </span>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </main>

  <script src="/autonexus/public/assets/js/supervisor/script-dashboard.js"></script>
</body>
</html>
