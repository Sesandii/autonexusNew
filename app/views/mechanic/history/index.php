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
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<div class="main-content">
<div class="breadcrumb-text">
    Mechanic <span class="sep">&gt;</span> 
    Vehicle History <span class="sep"></span> 
  </div>
<header class="page-header">
  
      <h1>Vehicle History</h1>
      <p class="subtitle">Search and view previous services of a vehicle.</p>
      </header>

  <form method="GET" action="<?= $base ?>/mechanic/history/show" class="search-box">
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

  
</div>

</body>
</html>
