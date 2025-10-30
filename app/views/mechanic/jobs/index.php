<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-jobs.css"/>
</head>
<body>
  <div class="sidebar">
    <div class="logo-container">
      <img src="<?= $base ?>/public/assets/img/Auto.png" alt="Logo" class="logo">
    </div>
    <h2>AUTONEXUS</h2>
    <a href="<?= $base ?>/mechanic/dashboard"><img src="<?= $base ?>/public/assets/img/dashboard.png"/>Dashboard</a>
    <a href="<?= $base ?>/mechanic/jobs" class="nav"><img src="<?= $base ?>/public/assets/img/jobs.png"/>Jobs</a>
    <a href="<?= $base ?>/mechanic/assignedjobs"><img src="<?= $base ?>/public/assets/img/assigned.png"/>Assigned Jobs</a>
    <a href="<?= $base ?>/mechanic/history"><img src="<?= $base ?>/public/assets/img/history.png"/>Vehicle History</a>
  </div>

  <main class="main-content">
    <header>
      <input type="text" placeholder="Search..." class="search" />
      <!--<div class="user-profile">
        <img src="<?= $base ?>/public/assets/img/user.png" alt="User" class="avatar-img" />
        <span><?= htmlspecialchars($_SESSION['first_name'] ?? 'Mechanic') ?></span>
      </div>-->
    </header>

    <section class="job-section">
      <p>Overview of all ongoing jobs</p>
      <h2>Ongoing Jobs</h2>
      <table>
        <thead>
          <tr>
            <th>Customer</th><th>Vehicle</th><th>Service Type</th>
            <th>ETA</th><th>Mechanic</th><th>Supervisor</th><th>Actions</th>
          </tr>
        </thead>
        <tbody id="job-table-body"><!-- JS populates rows --></tbody>
      </table>
    </section>
  </main>

  <script src="<?= $base ?>/public/assets/js/mechanic/script-jobs.js"></script>
</body>
</html>
