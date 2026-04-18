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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/dashboard/dashboard.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

  <style>
    /* Status styling (if not already in CSS file) */
    .open {
        color: #ff9800;
        font-weight: 600;
    }

    .in_progress {
        color: #2196f3;
        font-weight: 600;
    }

    .completed {
        color: #4caf50;
        font-weight: 600;
    }

    .empty {
        text-align: center;
        color: #999;
        padding: 15px;
    }
  </style>
</head>
<body>

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">

  <!-- Topbar -->
   <!--<div class="topbar">
   <input type="text" placeholder="Enter Vehicle Number">
    <div class="user">
      <span class="user-icon">👤</span>
      <span class="username">Ana Bell</span>
      <span class="bell">🔔</span>
    </div>
</div-->

  <!-- Dashboard Cards -->
  <div class="cards">

    <div class="card">
      <span class="icon">⚙️</span>
      <h4>Pending Services</h4>
      <h2><?= $pendingCount ?? 0 ?></h2>
    </div>

    <div class="card">
      <span class="icon">⚙️</span>
      <h4>Ongoing Services</h4>
      <h2><?= $ongoingCount ?? 0 ?></h2>
    </div>

    <div class="card">
      <span class="icon">📅</span>
      <h4>Appointments Today</h4>
      <h2><?= $todayAppointments ?? 0 ?></h2>
    </div>

  </div>

  <!-- Main Content -->
  <div class="content">

    <!-- Recent Activities -->
    <div class="activities">
      <h3>Recent Activities</h3>

      <table>
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
                          <?= htmlspecialchars($activity['vehicle_number']) ?><br>
                          <?= htmlspecialchars($activity['model']) ?>
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
                  <td colspan="3" class="empty">No recent activities found.</td>
              </tr>
          <?php endif; ?>
        </tbody>

      </table>
    </div>

    <!-- Quick Links -->
    <div class="quick-links">
      <h3>Quick Links</h3>

      <div class="links">

        <a href="<?= BASE_URL ?>/receptionist/customers/new" class="link-block">
          <div class="link">👨‍🔧<br>Add New Customer</div>
        </a>

        <a href="<?= BASE_URL ?>/receptionist/appointments/new" class="link-block">
          <div class="link">⚙️<br>Add New Appointment</div>
        </a>

        <a href="<?= BASE_URL ?>/manager/complaints/new" class="link-block">
          <div class="link">📊<br>New Complaint</div>
        </a>

      </div>
    </div>

  </div>
</div>

</body>
</html>
