<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaints Management</title>

  <!-- Remembered sidebar CSS -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/sidebar.css">
  <!-- Page CSS (changed from sm_css to css/manager) -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/manager/complaints.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/managersidebar.php'; ?>

  <div class="main">
    <header>
      <h1>Complaints Management</h1>
      <div class="filters">
        <input type="text" placeholder="Search complaints...">
        <select>
          <option>All Statuses</option>
          <option>Open</option>
          <option>In Progress</option>
          <option>Resolved</option>
        </select>
        <select>
          <option>All Priorities</option>
          <option>High</option>
          <option>Medium</option>
          <option>Low</option>
        </select>
      </div>
    </header>

    <div class="complaints-list">

      <div class="complaint open" data-url="<?= $base ?>/manager/complaints/1">
        <div class="complaint-info">
          <h3>🚨 Engine noise after service</h3>
          <p>Customer reports unusual engine noise after the 30,000 mile service was completed yesterday.</p>
          <span class="meta">James Wilson · 2019 Honda Civic · Jul 28, 2025</span>
        </div>
        <div class="status assigned">Assigned: Mike Johnson</div>
        <div class="badge open">Open</div>
      </div>

      <div class="complaint progress" data-url="<?= $base ?>/manager/complaints/2">
        <div class="complaint-info">
          <h3>⚠️ AC not cooling properly</h3>
          <p>Customer complains that the air conditioning is not cooling effectively after the recent AC service.</p>
          <span class="meta">Tom Hawk · 2018 Ford F-150 · Jul 26, 2025</span>
        </div>
        <div class="status assigned">Assigned: Mike Johnson</div>
        <div class="badge progress">In Progress</div>
      </div>

      <div class="complaint resolved" data-url="<?= $base ?>/manager/complaints/3">
        <div class="complaint-info">
          <h3>🚨 Brake squeaking</h3>
          <p>Customer reports loud squeaking noise when braking. Brakes were replaced at our shop 2 weeks ago.</p>
          <span class="meta">Lisa Chen · 2020 Toyota Premio · Jul 25, 2025</span>
        </div>
        <div class="status assigned">Assigned: Mike Johnson</div>
        <div class="badge resolved">Resolved</div>
      </div>

      <div class="complaint resolved" data-url="<?= $base ?>/manager/complaints/4">
        <div class="complaint-info">
          <h3>🔄 Tire pressure warning</h3>
          <p>Customer reports tire pressure warning light comes on intermittently since last service.</p>
          <span class="meta">Lisa Chen · 2022 Nissan Leaf · Jul 25, 2025</span>
        </div>
        <div class="status assigned">Assigned: Mike Johnson</div>
        <div class="badge resolved">Resolved</div>
      </div>

    </div>
  </div>

  <script>
    // Simple click-through to details (stub)
    document.querySelectorAll('.complaint').forEach(c => {
      c.addEventListener('click', () => {
        const url = c.getAttribute('data-url');
        if (url) window.location.href = url;
      });
    });
  </script>
</body>
</html>
