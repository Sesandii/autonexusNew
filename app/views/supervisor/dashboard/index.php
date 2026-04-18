<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Nexus Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<main class="main-content">
  <section class="welcome">
    <h1>Welcome, <?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></h1>
    <p>Here's an overview of your <span><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Supervisor') ?></span> dashboard</p>
  </section>
  <section class="dashboard-top">
  <div class="stat-cards-container">
    
    <div class="stat-card">
      <div class="stat-icon-box bg-light-orange">
        <i class="fa-solid fa-calendar-day icon-orange"></i>
      </div>
      <div class="stat-content">
        <span class="stat-label">Appointments Pending</span>
        <h2 class="stat-number"><?= (int)($stats['pending_appointments'] ?? 0) ?></h2>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-box bg-light-blue">
        <i class="fa-solid fa-user-check icon-blue"></i>
      </div>
      <div class="stat-content">
        <span class="stat-label">My Assigned Workorders</span>
        <h2 class="stat-number"><?= (int)($stats['my_assigned'] ?? 0) ?></h2>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-box bg-light-green">
        <i class="fa-solid fa-spinner fa-spin icon-green"></i>
      </div>
      <div class="stat-content">
        <span class="stat-label">In-Progress Workorders</span>
        <h2 class="stat-number"><?= (int)($stats['in_progress'] ?? 0) ?></h2>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-box bg-light-red">
        <i class="fa-solid fa-circle-pause icon-red"></i>
      </div>
      <div class="stat-content">
        <span class="stat-label">On-Hold Workorders</span>
        <h2 class="stat-number"><?= (int)($stats['on_hold'] ?? 0) ?></h2>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-box bg-light-purple">
        <i class="fa-solid fa-circle-check icon-purple"></i>
      </div>
      <div class="stat-content">
        <span class="stat-label">Completed Workorders</span>
        <h2 class="stat-number"><?= (int)($stats['completed'] ?? 0) ?></h2>
      </div>
    </div>

    <div class="stat-card">
      <div class="stat-icon-box bg-light-gray">
        <i class="fa-solid fa-list-check icon-gray"></i>
      </div>
      <div class="stat-content">
        <span class="stat-label">Total Workorders</span>
        <h2 class="stat-number"><?= (int)($stats['total'] ?? 0) ?></h2>
      </div>
    </div>

  </div>
</section>

  <section class="dashboard-grid-container">
    
  <div class="dashboard-tables-column">
  <div class="table-card">
    <div class="table-header">
      <div class="table-toggle">
        <button class="toggle-btn active" data-target="appointments">Today’s Appointments</button>
        <button class="toggle-btn" data-target="inprogress">In-Progress Vehicles</button>
      </div>
      
    </div>

    <div class="table-wrapper" id="appointments">
      <table>
        <thead>
          <tr>
            <th>Client</th>
            <th>Vehicle</th>
            <th>Time</th>
            <th>Service</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($appointments)): ?>
            <?php foreach ($appointments as $appt): ?>
              <tr>
                <td><img src="<?= $base ?>/public/assets/img/user2.png" class="user-icon" /> <?= htmlspecialchars($appt['customer_name']) ?></td>
                <td><img src="<?= $base ?>/public/assets/img/car.png" class="icon-car" /> <?= htmlspecialchars($appt['vehicle']) ?></td>
                <td><?= htmlspecialchars(date('h:i A', strtotime($appt['appointment_time']))) ?></td>
                <td><?= htmlspecialchars($appt['name']) ?></td>
                <td><span class="badge <?= strtolower($appt['status']) ?>"><?= htmlspecialchars($appt['status']) ?></span></td>
                <td>
                  <?php if (!empty($appt['work_order_id'])): ?>
                    <a class="btn btn-primary small" href="<?= $base ?>/supervisor/workorders/<?= (int)$appt['work_order_id'] ?>">View</a>
                  <?php else: ?>
                    <a class="btn btn-primary small" href="<?= $base ?>/supervisor/workorders/create?appointment_id=<?= (int)$appt['appointment_id'] ?>">Create</a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" style="text-align:center; padding: 20px; color: #888;">No appointments scheduled for today.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="table-wrapper hidden" id="inprogress">
      <table>
        <thead>
          <tr>
            <th>Vehicle</th>
            <th>Mechanic</th>
            <th>Started At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($inProgressJobs)): ?>
            <?php foreach ($inProgressJobs as $job): ?>
              <tr>
                <td><img src="<?= $base ?>/public/assets/img/car.png" class="icon-car" /> <?= htmlspecialchars($job['vehicle']) ?></td>
                <td><strong><?= htmlspecialchars($job['mechanic_code']) ?></strong></td>
                <td><?= htmlspecialchars(date('h:i A', strtotime($job['started_at']))) ?></td>
                <td>
                  <a class="btn btn-secondary small" href="<?= $base ?>/supervisor/workorders/<?= (int)$job['work_order_id'] ?>/edit">Edit</a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="4" style="text-align:center; padding: 20px; color: #888;">No vehicles currently in progress.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<div class="dashboard-quick-links-column">
  <div class="quick-links-card">
    <h3>Quick Links</h3>
    <div class="quick-links-grid">
      <a href="<?= $base ?>/supervisor/coordination" class="q-link">
        <i class="fa-solid fa-users-gear q-icon"></i>
        <span>Assign Job</span>
      </a>

      <a href="<?= $base ?>/supervisor/workorders/create" class="q-link">
        <i class="fa-solid fa-file-circle-plus q-icon"></i>
        <span>Create Workorder</span>
      </a>

      <a href="<?= $base ?>/supervisor/reports/indexp" class="q-link">
        <i class="fa-solid fa-car-side q-icon"></i>
        <span>Vehicle Report</span>
      </a>

      <a href="<?= $base ?>/supervisor/reports/daily-jobs" class="q-link">
        <i class="fa-solid fa-calendar-day q-icon"></i>
        <span>Daily Job Report</span>
      </a>

      <a href="<?= $base ?>/supervisor/reports/mechanic-activity" class="q-link">
        <i class="fa-solid fa-clipboard-user q-icon"></i>
        <span>Mechanic Report</span>
      </a>

      <a href="<?= $base ?>/supervisor/reports" class="q-link">
        <i class="fa-solid fa-chart-pie q-icon"></i>
        <span>View All Reports</span>
      </a>
    </div>
  </div>
</div>
 </section>

<div id="deleteModal" class="modal-overlay">
  <div class="modal-box">
    <h3>Confirm Deletion</h3>
    <p>Are you sure you want to delete this work order?</p>
    <div class="modal-actions">
      <button id="cancelDelete" class="btn small">Cancel</button>
      <button id="confirmDelete" class="btn small danger">Delete</button>
    </div>
  </div>
</div>

</main>

<script>
  window.weeklyAppointments = <?= json_encode($weeklyTrend) ?>;
  
</script>
<script src="/autonexus/public/assets/js/supervisor/script-dashboard.js"></script>
</body>
</html>