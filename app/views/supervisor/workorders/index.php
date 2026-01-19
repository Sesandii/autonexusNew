<?php $base = rtrim(BASE_URL,'/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Work Orders</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/forms.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-workorders.css">
</head>
<body>

<div class="sidebar">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>
  <h2>AUTONEXUS</h2>

  <a href="/autonexus/supervisor/dashboard">
    <img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard
  </a>
  <a href="/autonexus/supervisor/workorders" class="nav">
    <img src="/autonexus/public/assets/img/jobs.png"/>Work Orders
  </a>
  <a href="/autonexus/supervisor/assignedjobs">
    <img src="/autonexus/public/assets/img/assigned.png"/>Assigned
  </a>
  <a href="/autonexus/supervisor/history">
    <img src="/autonexus/public/assets/img/history.png"/>Vehicle History
  </a>
  <a href="/autonexus/supervisor/complaints">
    <img src="/autonexus/public/assets/img/Complaints.png"/>Complaints
  </a>
  <a href="/autonexus/supervisor/feedbacks">
    <img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks
  </a>
  <a href="/autonexus/supervisor/reports">
    <img src="/autonexus/public/assets/img/Inspection.png"/>Report
  </a>
</div>

<div class="container">
  <div class="page-header">
    <div>
      <h1>Work Orders</h1>
      <p class="subtitle">All work orders with their appointment's service.</p>
    </div>
    <a class="btn primary" href="<?= $base ?>/supervisor/workorders/create">Add Work Order</a>
  </div>

  <?php if (!empty($message)): ?>
    <div class="toast <?= htmlspecialchars($message['type']) ?>">
      <?= htmlspecialchars($message['text']) ?>
    </div>
  <?php endif; ?>

  <table class="workorders">
    <thead>
      <tr>
        <th>ID</th>
        <th>Appointment</th>
        <th>Service</th>
        <th>Mechanic</th>
        <th>Expected Completion</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($workOrders as $w): ?>
        <?php
          // Calculate expected completion using DateTime + DateInterval
          $expectedEnd = '-';
          if (!empty($w['started_at']) && !empty($w['base_duration_minutes'])) {
              $dt = new \DateTime($w['started_at']); // DB value as-is
              $dt->add(new \DateInterval('PT' . (int)$w['base_duration_minutes'] . 'M'));
              $expectedEnd = $dt->format('Y-m-d H:i:s'); // full timestamp
          }
        ?>
        <tr>
          <td><?= htmlspecialchars($w['work_order_id']) ?></td>
          <td><?= htmlspecialchars(($w['appointment_date'] ?? '') . ' ' . ($w['appointment_time'] ?? '')) ?></td>
          <td><?= htmlspecialchars($w['service_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($w['mechanic_code'] ?? 'Unassigned') ?></td>
          <td class="countdown" data-end="<?= htmlspecialchars($expectedEnd) ?>">
    <?= $expectedEnd === '-' ? '-' : 'Loading...' ?>
</td>

          <td><span class="status <?= htmlspecialchars($w['status']) ?>"><?= htmlspecialchars($w['status']) ?></span></td>
          <td>
            <a class="btn small" href="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>">View</a>
            <a class="btn small edit" href="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>/edit">Edit</a>
            <form method="post" action="<?= $base ?>/supervisor/workorders/<?= $w['work_order_id'] ?>/delete" style="display:inline" onsubmit="return confirm('Delete this work order?')">
              <button type="submit" class="btn small danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</div>
<script>
// Countdown timer for each row
function updateTimers() {
    const timers = document.querySelectorAll('.countdown');
    const now = new Date();

    timers.forEach(td => {
        const endTimeStr = td.dataset.end;
        if (!endTimeStr || endTimeStr === '-') {
            td.textContent = '-';
            return;
        }

        const endTime = new Date(endTimeStr);
        let diff = Math.floor((endTime - now) / 1000); // in seconds

        if (diff <= 0) {
            td.textContent = "00:00";
        } else {
            const hours = Math.floor(diff / 3600);
            diff %= 3600;
            const minutes = Math.floor(diff / 60);
            const seconds = diff % 60;

            td.textContent = 
                (hours > 0 ? hours + 'h ' : '') + 
                (minutes > 0 ? minutes + 'm ' : '') + 
                seconds + 's';
        }
    });
}

// Update timers every second
setInterval(updateTimers, 1000);
updateTimers(); // initial call
</script>

</body>
</html>
