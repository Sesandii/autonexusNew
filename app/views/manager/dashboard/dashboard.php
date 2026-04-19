<?php
$activePage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoNexus Dashboard</title>

  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/dashboard.css">

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main-content">

  <!-- Topbar -->
  <div class="topbar">
    
    <h1 class="page-title">Dashboard</h1>

    <div class="topbar-right">

      <div class="user-chip">
        <div class="avatar">A</div>
        <span>Manager</span>
      </div>

    </div>
  </div>

  <p class="subtitle">Welcome back, here’s what’s happening today</p>

  <!-- KPI CARDS -->
  <div class="kpi-grid">

    <div class="kpi-card">
      <div class="kpi-icon">
        <i class="fa-solid fa-gears"></i>
      </div>
      <div class="kpi-meta">
        <h3>Pending Services</h3>
        <div class="kpi-value"><?= $pendingCount ?? 0 ?></div>
        <div class="kpi-delta">Awaiting action</div>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon">
        <i class="fa-solid fa-screwdriver-wrench"></i>
      </div>
      <div class="kpi-meta">
        <h3>Ongoing Services</h3>
        <div class="kpi-value"><?= $ongoingCount ?? 0 ?></div>
        <div class="kpi-delta up">In progress</div>
      </div>
    </div>

    <div class="kpi-card">
      <div class="kpi-icon">
        <i class="fa-solid fa-calendar-day"></i>
      </div>
      <div class="kpi-meta">
        <h3>Appointments Today</h3>
        <div class="kpi-value"><?= $todayAppointments ?? 0 ?></div>
        <div class="kpi-delta">Scheduled today</div>
      </div>
    </div>

  </div>

  <!-- CONTENT GRID -->
  <div class="content-grid">

    <!-- Recent Activities -->
    <div class="card panel">

      <div class="panel-head">
        <h2>Recent Activities</h2>
      </div>

      <div class="table-wrap">
        <table class="table">

          <thead>
            <tr>
              <th>Vehicle</th>
              <th>Service</th>
              <th>Status</th>
            </tr>
          </thead>

          <tbody>
            <?php if (!empty($recentActivities)): ?>
              <?php foreach ($recentActivities as $activity): ?>
                <tr>
                  <td>
                    <strong><?= htmlspecialchars($activity['vehicle_number']) ?></strong><br>
                    <span style="color:#64748b;">
                      <?= htmlspecialchars($activity['model']) ?>
                    </span>
                  </td>

                  <td>
                    <?= htmlspecialchars($activity['service_summary']) ?>
                  </td>

                  <td class="<?= htmlspecialchars($activity['status']) ?>">
                    <?= ucfirst(str_replace('_', ' ', $activity['status'])) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="3" style="text-align:center; color:#999;">
                  No recent activities found.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>

        </table>
      </div>

    </div>

    <!-- Quick Links -->
    <div class="card quick-links">

      <h2>Quick Actions</h2>

      <div class="ql-grid">

        <a href="<?= BASE_URL ?>/manager/services/create" class="ql-card">
  <i class="fa-solid fa-screwdriver-wrench"></i>
  <span>Add Service</span>
</a>

        <a href="<?= BASE_URL ?>/manager/schedule/add-member" class="ql-card">
  <i class="fa-solid fa-user-plus"></i>
  <span>Add Team Member</span>
</a>

        <a href="http://localhost:8080/autonexus/manager/complaints" class="ql-card">
  <i class="fa-solid fa-triangle-exclamation"></i>
  <span>Complaints</span>
</a>

        <a href="<?= BASE_URL ?>/manager/reports" class="ql-card">
          <i class="fa-solid fa-file-lines"></i>
          <span>Reports</span>
        </a>

      </div>

    </div>

  </div>

</div>

</body>
</html>