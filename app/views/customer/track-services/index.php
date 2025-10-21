<?php
$base = rtrim(BASE_URL, '/');
$initial = $services ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title ?? 'Track Services') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/track-services.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="main-content">
    <h2>Track Services</h2>
    <p class="subtitle">Monitor the progress of your vehicle services</p>
    
    <div class="search-filter">
      <input type="text" id="searchInput" placeholder="Enter service name / vehicle / date (YYYY-MM-DD)">
      <select id="statusFilter">
        <option value="All">All Statuses</option>
        <option value="Pending">Pending</option>
        <option value="In Progress">In Progress</option>
        <option value="Completed">Completed</option>
      </select>
      <button id="searchBtn">Search</button>
    </div>
    
    <div class="table-container">
      <table id="servicesTable">
        <thead>
          <tr>
            <th>Service Type</th>
            <th>Date Booked</th>
            <th>Status</th>
            <th>Est. Completion</th>
          </tr>
        </thead>
        <tbody><!-- rows inserted by JS --></tbody>
      </table>
    </div>
  </div>

  <script>
    const BASE_URL  = "<?= $base ?>";
    const LIST_URL  = BASE_URL + "/customer/track-services/list";
    const INITIAL_TRACK_DATA = <?= json_encode($initial, JSON_UNESCAPED_UNICODE) ?>;
  </script>
  <script src="<?= $base ?>/public/assets/js/customer/track-services.js"></script>
</body>
</html>
