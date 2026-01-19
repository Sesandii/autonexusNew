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
  <a href="/autonexus/mechanic/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="/autonexus/mechanic/jobs"><img src="/autonexus/public/assets/img/jobs.png"/>Jobs</a>
  <a href="/autonexus/mechanic/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned</a>
  <a href="/autonexus/mechanic/history" class="nav"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
</div>

<div class="container">
  <div class="page-header">
    <div>
      <h1>Vehicle History</h1>
      <p class="subtitle">Search and view previous services of a vehicle.</p>
    </div>
  </div>

  <form method="GET" action="<?= $base ?>/mechanic/history/show" class="search-box">
    <div>
      <label>License Plate</label><br>
      <input type="text" name="license_plate" placeholder="Enter vehicle license" required>
    </div>
    <div>
      <label>From Date</label><br>
      <input type="date" name="fromDate">
    </div>
    <div>
      <label>To Date</label><br>
      <input type="date" name="toDate">
    </div>
    <div>
      <button type="submit" class="btn primary">Search</button>
    </div>
  </form>

  <p class="subtitle" style="text-align:center; margin-top:30px;">Enter a license plate to view its service history.</p>
</div>

</body>
</html>
