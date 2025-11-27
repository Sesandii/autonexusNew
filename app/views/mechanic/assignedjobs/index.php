<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Shop Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/mechanic/style-assignedjobs.css"/>
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
    <a href="/autonexus/mechanic/assignedjobs" class="nav">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned Jobs
    </a>
    <a href="/autonexus/mechanic/history">
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
      <section class="job-section">
  <p>Overview of all ongoing jobs</p>
  <h2>Ongoing Jobs</h2>

  <div class="job-grid" id="job-grid">
    <?php if (!empty($workOrders)) : ?>
        <?php foreach ($workOrders as $job) : ?>
            <div class="job-card">
                <h3><?= htmlspecialchars($job['service_summary']) ?></h3>
                <div class="job-info"><span>Customer:</span> <?= htmlspecialchars($job['first_name'] . ' ' . $job['last_name']) ?></div>
                <div class="job-info"><span>Address:</span> <?= htmlspecialchars($job['street_address'] . ', ' . $job['city'] . ', ' . $job['state']) ?></div>
                <div class="job-info"><span>ETA:</span> <?= htmlspecialchars($job['started_at']) ?></div>
                <div class="job-info"><span>Mechanic:</span> <?= htmlspecialchars($job['mechanic_code']) ?></div>
                <div class="job-actions">
                    <button class="view-btn">View</button>
                    <button class="edit-btn">Edit</button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No assigned jobs yet.</p>
    <?php endif; ?>
</div>

</section>
    </main>
  <script src="/autonexus/public/assets/js/mechanic/script-assignedobs.js"></script>
</body>
</html>
