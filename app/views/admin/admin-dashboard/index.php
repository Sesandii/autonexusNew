<?php $current = 'dashboard'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>AutoNexus • Dashboard</title>

  <!-- Sidebar styles you already have -->
   <link rel="stylesheet" href="app/views/layouts/admin-sidebar/styles.css">

  <!-- Dashboard page styles (new) -->
 <link rel="stylesheet" href="<?= rtrim(BASE_URL,'/') ?>/public/assets/css/admin-dashboard.css?v=1">



  <!-- Font Awesome for icons (optional) -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>
  <?php include(__DIR__ . '/../../layouts/admin-sidebar/sidebar.php'); ?>

  <main class="main-content">
    <!-- Top bar -->
    <header class="topbar">
        <div>
          <h1 class="page-title">Admin Dashboard</h1>
          <p class="subtitle">Overview of your AutoNexus service station</p>
          <?php require APP_ROOT . '/views/partials/lang-switcher.php'; ?>
        </div>
        
     
        <a class="user-chip user-chip--link" href="<?= rtrim(BASE_URL,'/') ?>/admin/profile" aria-label="Open profile">
          <div class="avatar"><i class="fa-solid fa-user"></i></div>
          <span>Admin User</span>
        </a>


     
    </header>

    <section class="dash-wrap">
      

      <!-- KPI Cards -->
      <div class="kpi-grid">
        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-user-group"></i></div>
          <div class="kpi-meta">
            <h3>Total Customers</h3>
            <p class="kpi-value"><?= number_format($metrics['users'] ?? 0) ?></p>
            <span class="kpi-delta up">Live</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-regular fa-calendar-check"></i></div>
          <div class="kpi-meta">
            <h3>Total Appointments</h3>
            <p class="kpi-value"><?= number_format($metrics['appointments'] ?? 0) ?></p>
            <span class="kpi-delta up">Live</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-meta">
            <h3>Services Completed</h3>
            <p class="kpi-value"><?= number_format($metrics['completed'] ?? 0) ?></p>
            <span class="kpi-delta up">Live</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
          <div class="kpi-meta">
            <h3>Total Revenue</h3>
            <p class="kpi-value">
              <?= 'Rs.' . number_format((float)($metrics['revenue'] ?? 0), 2) ?>
              <!-- or '$' if you prefer -->
            </p>
            <span class="kpi-delta up">Live</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-regular fa-message"></i></div>
          <div class="kpi-meta">
            <h3>Feedback Count</h3>
            <p class="kpi-value"><?= number_format($metrics['feedback'] ?? 0) ?></p>
            <span class="kpi-delta up">Live</span>
          </div>
        </article>
      </div>


      <div class="content-grid">
        <!-- Recent Activity -->
        <section class="card panel">
          <div class="panel-head">
            <h2>Recent Activity</h2>
          </div>
          <div class="table-wrap">
            <table class="table">
              <thead>
                <tr>
                  <th>Activity</th>
                  <th>User</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Completed oil change service</td>
                  <td>John Doe (Mechanic)</td>
                </tr>
                <tr>
                  <td>Started brake inspection</td>
                  <td>Mike Johnson (Mechanic)</td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

      <!-- Quick Links -->
<aside class="quick-links card">
  <h2>Quick Links</h2>
  <div class="ql-grid">
    <a class="ql-card" data-e2e="ql-add-branch" href="<?= rtrim(BASE_URL,'/') ?>/admin/branches/create">
      <i class="fa-solid fa-building"></i>
      <span>Add Branch</span>
    </a>

    <a class="ql-card" data-e2e="ql-add-manager" href="<?= rtrim(BASE_URL,'/') ?>/admin/service-managers/create">
      <i class="fa-solid fa-user-plus"></i>
      <span>Register Manager</span>
    </a>

    <a class="ql-card" data-e2e="ql-add-service" href="<?= rtrim(BASE_URL,'/') ?>/admin/services/create">
      <i class="fa-solid fa-screwdriver-wrench"></i>
      <span>Add Service</span>
    </a>

    <a class="ql-card" data-e2e="ql-view-reports" href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewreports">
      <i class="fa-solid fa-chart-simple"></i>
      <span>View Reports</span>
    </a>
  </div>
</aside>

      </div>
    </section>
  </main>
</body>
</html>
