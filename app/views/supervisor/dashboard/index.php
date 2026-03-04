<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Nexus Dashboard</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-dashboard.css" />
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<main class="main-content">
  <!-- Welcome section -->
  <section class="welcome">
    <h1>Welcome, <?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></h1>
    <p>Here's an overview of your <span><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Supervisor') ?></span> dashboard</p>
  </section>

  <!-- Dashboard top: cards -->
  <section class="dashboard-top">
    <div class="dashboard-left">
      <div class="cards">
        <div class="card green">
          <div class="card-header"><h3>Appointments Pending</h3></div>
          <p><?= (int)($stats['pending_appointments'] ?? 0) ?></p>
        </div>
        <div class="card blue">
          <div class="card-header"><h3>My Assigned Workorders</h3></div>
          <p><?= (int)($stats['my_assigned'] ?? 0) ?></p>
        </div>
        <div class="card red">
          <div class="card-header"><h3>In-Progress Workorders</h3></div>
          <p><?= (int)($stats['in_progress'] ?? 0) ?></p>
        </div>
        <div class="card purple">
          <div class="card-header"><h3>On-Hold Workorders</h3></div>
          <p><?= (int)($stats['on_hold'] ?? 0) ?></p>
        </div>
        <div class="card purple">
          <div class="card-header"><h3>Completed Workorders</h3></div>
          <p><?= (int)($stats['completed'] ?? 0) ?></p>
        </div>
        <div class="card purple">
          <div class="card-header"><h3>Total Workorders</h3></div>
          <p><?= (int)($stats['total'] ?? 0) ?></p>
        </div>
      </div>
    </div>
  </section>

  <!-- Chart + toggle tables section -->
  <section class="dashboard-bottom">
    <div class="dashboard-bottom-left">
      <!-- Chart -->
      <div class="chart-container">
        <h3>Daily / Weekly Appointments Trend</h3>
        <canvas id="weekly-chart" width="500" height="210"></canvas>
      </div>
    </div>

    <div class="dashboard-bottom-right">
      <!-- Toggle buttons -->
      <div class="table-toggle">
        <button class="toggle-btn active" data-target="appointments">Today’s Appointments</button>
        <button class="toggle-btn" data-target="inprogress">In-Progress Vehicles</button>
      </div>

      <!-- Today’s Appointments -->
      <section class="appointments" id="appointments">
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
                <td><?= htmlspecialchars($appt['status']) ?></td>
                <td>
  <?php if (!empty($appt['work_order_id'])): ?>
    <div class="action-buttons">
      <a class="btn btn-primary"
         href="<?= $base ?>/supervisor/workorders/<?= (int)$appt['work_order_id'] ?>">
         View
      </a>

      <a class="btn btn-secondary"
         href="<?= $base ?>/supervisor/workorders/<?= (int)$appt['work_order_id'] ?>/edit">
         Edit
      </a>

      <form method="POST"
            action="<?= $base ?>/supervisor/workorders/<?= (int)$appt['work_order_id'] ?>/delete"
            class="delete-form">
        <button type="submit" class="btn btn-danger">
          Delete
        </button>
      </form>
    </div>
  <?php else: ?>
    <div class="action-buttons">
      <a class="btn btn-primary"
         href="<?= $base ?>/supervisor/workorders/create?appointment_id=<?= (int)$appt['appointment_id'] ?>">
         Create
      </a>
    </div>
  <?php endif; ?>
</td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="6" style="text-align:center;">No appointments today</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <!-- In-progress vehicles -->
      <section class="appointments hidden" id="inprogress">
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
                <td><?= htmlspecialchars($job['vehicle']) ?></td>
                <td><?= htmlspecialchars($job['mechanic_code']) ?></td>
                <td><?= htmlspecialchars(date('h:i A', strtotime($job['started_at']))) ?></td>
                <td><button class="btn btn-primary" onclick="location.href='/autonexus/supervisor/assignedjobs/<?= $job['work_order_id'] ?>'">Edit</button></td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr><td colspan="4" style="text-align:center;">No vehicles in progress</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>
    </div>
  </section>

</main>

<script>
  window.weeklyAppointments = <?= json_encode($weeklyTrend) ?>;
</script>
<script src="/autonexus/public/assets/js/supervisor/script-dashboard.js"></script>
</body>
</html>