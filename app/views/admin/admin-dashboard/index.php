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
        
     
        <div class="user-chip">
          <div class="avatar"><i class="fa-solid fa-user"></i></div>
          <span>Admin User</span>
        </div>

     
    </header>

    <section class="dash-wrap">
      

      <!-- KPI Cards -->
      <div class="kpi-grid">
        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-user-group"></i></div>
          <div class="kpi-meta">
            <h3>Total Users</h3>
            <p class="kpi-value">2,543</p>
            <span class="kpi-delta up">+12.5% from last month</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-regular fa-calendar-check"></i></div>
          <div class="kpi-meta">
            <h3>Total Appointments</h3>
            <p class="kpi-value">148</p>
            <span class="kpi-delta up">+4.3% from last month</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-circle-check"></i></div>
          <div class="kpi-meta">
            <h3>Services Completed</h3>
            <p class="kpi-value">1,257</p>
            <span class="kpi-delta up">+8.2% from last month</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-solid fa-sack-dollar"></i></div>
          <div class="kpi-meta">
            <h3>Total Revenue</h3>
            <p class="kpi-value">$84,325</p>
            <span class="kpi-delta up">+15.6% from last month</span>
          </div>
        </article>

        <article class="kpi-card">
          <div class="kpi-icon"><i class="fa-regular fa-message"></i></div>
          <div class="kpi-meta">
            <h3>Feedback Count</h3>
            <p class="kpi-value">342</p>
            <span class="kpi-delta up">+6.8% from last month</span>
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
            <button class="ql-card">
              <i class="fa-regular fa-calendar-plus"></i>
              <span>Add Appointment</span>
            </button>
            <button class="ql-card">
              <i class="fa-solid fa-user-plus"></i>
              <span>Register User</span>
            </button>
            <button class="ql-card">
              <i class="fa-solid fa-screwdriver-wrench"></i>
              <span>Add Service</span>
            </button>
            <button class="ql-card">
              <i class="fa-regular fa-file-lines"></i>
              <span>Create Invoice</span>
            </button>
          </div>
        </aside>
      </div>
    </section>
  </main>
</body>
</html>
