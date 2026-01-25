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
