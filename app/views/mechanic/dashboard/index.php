<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Auto Nexus Dashboard</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/mechanic/style-dashboard.css" />
</head>
<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>
<main class="main-content">
<section class="welcome">
  <h2>Welcome, <?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></h2>
  <p>Here's an overview of your <span><?= htmlspecialchars($_SESSION['user']['name'] ?? 'Mechanic') ?></span> dashboard</p>
  </section>
<section class="cards">

<div class="card green">
    <div class="card-header">
        <h3>Appointments Pending</h3>
    </div>
    <p><?= $branch_pending ?? 0 ?></p>
</div>

  <div class="card blue">
    <div class="card-header">
      <h3>Assigned Jobs</h3>
    </div>
    <p><?= $stats['assigned'] ?? 0?></p>
    <span class="change"></span>
  </div>

  <div class="card red">
    <div class="card-header">
      <h3>In-Progress Jobs</h3>
    </div>
    <p><?= $stats['ongoing'] ?? 0 ?></p>
    <span class="change"></span>
  </div>

  <div class="card purple">
    <div class="card-header">
      <h3>on-hold Jobs</h3>
    </div>
    <p><?= $stats['onhold'] ?? 0 ?></p>
    <span class="change"></span>
  </div>


  <div class="card green">
    <div class="card-header">
      <h3>Completed Jobs</h3>
    </div>
    <p><?= $stats['completed'] ?? 0 ?></p>
    <span class="change"></span>
  </div>

  
</section>

<section class="appointments">
  <h3>Today's Appointments</h3>
  <table>
    <thead>
      <tr>
        <th>Client</th>
        <th>Vehicle</th>
        <th>Time</th>
        <th>Service</th>
        <th>Status</th>
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
          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="5" style="text-align:center;">No appointments today</td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>
</section>

</main>
<script src="<?= $base ?>/public/assets/js/mechanic/script-dashboard.js"></script>
</body>
</html>
