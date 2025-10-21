<?php
// app/views/manager/performance/index.php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Team Performance</title>

  <!-- Remembered sidebar CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">

  <!-- Page CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/performance.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Remembered sidebar include -->
  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <!-- Main Content -->
  <div class="main">
    <header>
      <h1>Team Performance</h1>
      <div class="filters">
        <select>
          <option>This Month</option>
        </select>
        <select>
          <option>Completed Jobs</option>
        </select>
      </div>
    </header>

    <!-- Stats Cards -->
    <div class="stats-grid">
      <div class="card"><h3>Completed Jobs</h3><p class="value">287</p></div>
      <div class="card"><h3>Customer Satisfaction</h3><p class="value">92%</p></div>
      <div class="card"><h3>Avg. Service Time</h3><p class="value">84 min</p></div>
      <div class="card"><h3>Return Rate</h3><p class="value">5.2%</p></div>
      <div class="card revenue">
        <h3>Revenue</h3>
        <p class="value">$124,650</p>
        <p class="growth">▲ 5.5% from previous</p>
      </div>
    </div>

    <!-- Performance Chart & Team Performance Table -->
    <div class="content-grid">
      <!-- Chart Placeholder -->
      <div class="card chart">
        <h3>Performance Trend: Completed Jobs</h3>
        <div class="bar-chart">
          <div class="bar" style="height: 120px; background:#f39c12;">Mon</div>
          <div class="bar" style="height: 160px; background:#2980b9;">Tue</div>
          <div class="bar" style="height: 90px; background:#9b59b6;">Wed</div>
          <div class="bar" style="height: 110px; background:#8e44ad;">Thu</div>
          <div class="bar" style="height: 170px; background:#e74c3c;">Fri</div>
          <div class="bar" style="height: 70px; background:#34495e;">Sat</div>
          <div class="bar" style="height: 130px; background:#7f1d1d;">Sun</div>
        </div>
      </div>

      <!-- Team Member Performance -->
      <div class="card team-table" data-url="<?= $base ?>/manager/performance/team">
        <h3>Team Member Performance</h3>
        <table>
          <thead>
            <tr>
              <th>Team Member</th>
              <th>Role</th>
              <th>Completed Jobs</th>
              <th>Customer Satisfaction</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>David Lee</td><td>Technician</td><td>38</td><td>89%</td></tr>
            <tr><td>Emily Wilson</td><td>Customer Service</td><td>75</td><td>97%</td></tr>
            <tr><td>John Smith</td><td>Senior Mechanic</td><td>42</td><td>94%</td></tr>
            <tr><td>Maria Garcia</td><td>Service Advisor</td><td>56</td><td>96%</td></tr>
            <tr><td>Robert Chen</td><td>Diagnostic Expert</td><td>45</td><td>93%</td></tr>
            <tr><td>Sarah Johnson</td><td>Parts Specialist</td><td>31</td><td>91%</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Page JS -->
  <script src="<?= $base ?>/public/assets/sm_js/performance.js"></script>
  <script>
    // Optional: basic click-through if your JS isn't loaded
    document.querySelectorAll('.team-table').forEach(card => {
      card.addEventListener('click', () => {
        const url = card.getAttribute('data-url');
        if (url) window.location.href = url;
      });
    });
  </script>
</body>
</html>
