<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Assigned Work Orders</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-assignedjobs.css"/>
</head>

<body>
  <div class="sidebar">
    <div class="logo-container">
      <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
    </div>

    <h2>AUTONEXUS</h2>

    <a href="/autonexus/supervisor/dashboard">
      <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
    </a>
    <a href="/autonexus/supervisor/workorders">
      <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
    </a>
    <a href="/autonexus/supervisor/assignedjobs" class="nav active">
      <img src="/autonexus/public/assets/img/assigned.png"/>Assigned
    </a>
    <a href="/autonexus/supervisor/history">
      <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
    </a>
    <a href="/autonexus/supervisor/complaints">
      <img src="/autonexus/public/assets/img/Complaints.png"/>Complaints
    </a>
    <a href="/autonexus/supervisor/feedbacks">
      <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
    </a>
    <a href="/autonexus/supervisor/reports">
      <img src="/autonexus/public/assets/img/Inspection.png"/>Report
    </a>
  </div>

  <main class="main">
    <header>
      <input type="text" placeholder="Search..." class="search" />
    </header>

    <section class="job-section">
      <p>Here are all jobs already assigned to mechanics</p>
      <h2>Assigned Jobs</h2>

      <div class="job-grid">

        <?php if (!empty($workOrders)): ?>
          <?php foreach ($workOrders as $job): ?>
            <div class="job-card">
              
              <h3>Work Order <?= $job['work_order_id'] ?></h3>

              <div class="job-info"><span>Vehicle:</span> <?= $job['make']?> <?= $job['model']?> </div>
              <div class="job-info"><span>Created:</span> <?= $job['started_at'] ?></div>
              <div class="job-info"><span>Date:</span> <?= $job['appointment_date'] ?></div>
              <div class="job-info"><span>Time:</span> <?= $job['appointment_time'] ?></div>
              <div class="job-info"><span>Service:</span> <?= $job['service_name'] ?></div>

              <div class="job-info"><span>Mechanic:</span> <?= $job['mechanic_code'] ?></p></div>

              <span class="status <?= strtolower($job['status']) ?>">
              <div class="job-info"><span>Status: </span><?= $job['status'] ?></div>
              </span>
              <button class="edit-btn" onclick="location.href='/autonexus/supervisor/assignedjobs/<?= $job['work_order_id'] ?>'">Edit</button>
            </div>
          <?php endforeach; ?>

        <?php else: ?>
          <p class="no-jobs">No assigned jobs available.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>

  <script src="/autonexus/public/assets/js/supervisor/script-assignedjobs.js"></script>

</body>
</html>
