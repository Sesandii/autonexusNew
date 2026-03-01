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
      <header>
        <div class="user-profile">
          <div class="user">
            <img src="/autonexus/public/assets/img/user.png" alt="User" class="user-img" />
            <div class="user-menu">
              <span id="user-name"><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Supervisor') ?></span>
              <ul id="dropdown" class="dropdown hidden">
              <li><a href="<?= $base ?>/supervisor/profile/edit">Edit Profile</a></li>
                <li><a href="<?= $base ?>/logout">Sign Out</a></li>
              </ul>
            </div>
          </div>
        </div>
      </header>

      <section class="welcome">
      <h1>Welcome, <?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></h1>
        <p>Here's an overview of your dashboard</p>
      </section>
      <section class="dashboard-top">
  <div class="dashboard-left">
    <div class="cards">
        <div class="card green">
          <div class="card-header">
            <h3>Appointments Pending</h3>
          </div>
          <p><?= (int)($stats['pending_appointments'] ?? 0) ?></p>
        </div>

        <div class="card blue">
          <div class="card-header">
            <h3>My Assigned Workorders</h3>
          </div>
          <p><?= (int)($stats['my_assigned'] ?? 0) ?></p>
        </div>

        <div class="card red">
          <div class="card-header">
            <h3>In-Progress Workorders</h3>
          </div>
          <p><?= (int)($stats['in_progress'] ?? 0) ?></p>
        </div>

        <div class="card purple">
          <div class="card-header">
            <h3>Total Workorders</h3>
          </div>
          <p><?= (int)($stats['total'] ?? 0) ?></p>
        </div>
        </div>
        </div>
        <div class="dashboard-right">
        </div>
      </section>

      <section class="dashboard-bottom">
      <div class="table-toggle">
  <button class="toggle-btn active" data-target="appointments">Today’s Appointments</button>
  <button class="toggle-btn" data-target="inprogress">In-Progress Vehicles</button>
</div>
<section class="appointments" id="appointments">
      <div class="table-header">
  <h3>Today's Appointments</h3>
</div>
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
                <!-- ✅ Pass appointment_id via GET -->
                <?php if (!empty($appt['work_order_id'])): ?>
    
    <a class="btn-primary"
       href="<?= $base ?>/supervisor/workorders/<?= (int)$appt['work_order_id'] ?>">
       View
    </a>

    <a class="btn-primary"
       href="<?= $base ?>/supervisor/workorders/<?= (int)$appt['work_order_id'] ?>/edit">
       Edit
    </a>

    <form method="POST"
      action="<?= $base ?>/supervisor/workorders/<?= (int)$appt['work_order_id'] ?>/delete"
      style="display:inline"
      onsubmit="return confirm('Delete this work order?')">

    <button type="submit" class="btn-danger">
        Delete
    </button>
</form>

<?php else: ?>

    <a class="btn-primary"
       href="<?= $base ?>/supervisor/workorders/create?appointment_id=<?= (int)$appt['appointment_id'] ?>">
       Create
    </a>

<?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="6" style="text-align:center;">No appointments today</td>
    </tr>
<?php endif; ?>
    </tbody>
        </table>
      </section>
      <section class="appointments hidden" id="inprogress">
  <h3>Vehicles In Progress</h3>

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
            <td>
            <button class="btn-primary" onclick="location.href='/autonexus/supervisor/assignedjobs/<?= $job['work_order_id'] ?>'">Edit</button>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="4" style="text-align:center;">No vehicles in progress</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>
</section>
    </main>
  <script src="/autonexus/public/assets/js/supervisor/script-dashboard.js"></script>
</body>
</html>
