<?php $base = rtrim(BASE_URL, '/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Vehicle History</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-history.css">
</head>
<body>

<div class="sidebar">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>

  <h2>AUTONEXUS</h2>
  <a href="/autonexus/supervisor/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="/autonexus/supervisor/workorders"><img src="/autonexus/public/assets/img/jobs.png"/>Work Orders</a>
  <a href="/autonexus/supervisor/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned</a>
  <a href="/autonexus/supervisor/history" class="nav"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
  <a href="/autonexus/supervisor/complaints"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
  <a href="/autonexus/supervisor/feedbacks"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
  <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
</div>

<div class="container">
  <div class="page-header">
    <div>
      <h1>Vehicle History</h1>
    </div>
  </div>

  <form method="GET" action="<?= $base ?>/supervisor/history/show" class="search-box">
    <div>
      <label>License Plate</label><br>
      <input type="text" name="license_plate" placeholder="Enter vehicle license" required>
    </div>
    <div>
      <label>From Date</label><br>
      <input type="date" name="fromDate" required>
    </div>
    <div>
      <label>To Date</label><br>
      <input type="date" name="toDate" required>
    </div>
    <div>
      <button type="submit" class="btn primary">Search</button>
    </div>
  </form>

  <p class="subtitle">Search and view previous services of a vehicle.</p>
</div>

</body>
</html>
