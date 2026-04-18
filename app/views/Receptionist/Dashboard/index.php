<?php
$activePage = 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AutoNexus Dashboard</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/dashboard/dashboard.css">


</head>
<body>

<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

<div class="main">

<header class="dashboard-header">
<h1>Welcome, <?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></h1>
    <p class="subtitle">Here's an overview of your Receptionist dashboard</p>
  </header>

  <!-- Topbar -->
  <!--<div class="topbar">
    <input type="text" placeholder="Enter Vehicle Number">
    <div class="user">
      <span class="user-icon">👤</span>
      <span class="username">Ana Bell</span>
      <span class="bell">🔔</span>
    </div>
  </div>-->

  <!-- Dashboard Cards -->
  <div class="stat-cards-container">
    <div class="stat-card">
        <div class="stat-icon-wrapper">
            <i class="fas fa-tools stat-icon"></i>
        </div>
        <div class="stat-info">
            <p class="stat-label">Pending Services</p>
            <h2 class="stat-value"><?= $pendingCount ?? 0 ?></h2>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper">
            <i class="fas fa-cog fa-spin stat-icon"></i> </div>
        <div class="stat-info">
            <p class="stat-label">Ongoing Services</p>
            <h2 class="stat-value"><?= $ongoingCount ?? 0 ?></h2>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-wrapper">
            <i class="fas fa-calendar-day stat-icon"></i>
        </div>
        <div class="stat-info">
            <p class="stat-label">Appointments Today</p>
            <h2 class="stat-value"><?= $todayAppointments ?? 0 ?></h2>
        </div>
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
    <?= htmlspecialchars($activity['license_plate']) ?><br>
    <?= htmlspecialchars($activity['model']) ?>
</td>

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


    <div class="dashboard-quick-links-column">
  <div class="quick-links-card">
    <h3>Quick Links</h3>
    <div class="quick-links-grid">
    <a href="<?= BASE_URL ?>/receptionist/customers/new" class="q-link">
        <i class="fas fa-user-plus ql-icon"></i>
        <span class="ql-text">Add New Customer</span>
    </a>
    <a href="<?= BASE_URL ?>/receptionist/appointments/new" class="q-link">
        <i class="fas fa-calendar-plus ql-icon"></i>
        <span class="ql-text">Add New Appointment</span>
    </a>
    <a href="<?= BASE_URL ?>/receptionist/invoices/new" class="q-link">
        <i class="fas fa-file-invoice stat-icon ql-icon"></i>
        <span class="ql-text">Create Invoice</span>
    </a>
    <a href="<?= BASE_URL ?>/receptionist/complaints/new" class="q-link">
        <i class="fas fa-clipboard-list ql-icon"></i>
        <span class="ql-text">New Complaint</span>
    </a>
</div>
          </div></div>

  </div>
</div>

</body>
</html>
