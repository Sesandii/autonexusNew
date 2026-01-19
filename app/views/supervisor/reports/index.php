<?php $base = rtrim(BASE_URL, '/'); ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Reports</title>
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
  <a href="/autonexus/supervisor/workorders">
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
  <a href="/autonexus/supervisor/reports" class="nav">
    <img src="/autonexus/public/assets/img/Inspection.png"/>Reports
  </a>
</div>

<div class="container">

  <div class="page-header">
    <div>
      <h1>Job Reports</h1>
      <p class="subtitle">Reports created for completed work orders.</p>
    </div>
      <a class="btn primary" href="<?= $base ?>/supervisor/reports/create">
  Create Report
</a>


  </div>

  <?php if (!empty($message)): ?>
    <div class="toast <?= htmlspecialchars($message['type']) ?>">
      <?= htmlspecialchars($message['text']) ?>
    </div>
  <?php endif; ?>

  <table class="workorders">
    <thead>
      <tr>
        <th>Report ID</th>
        <th>Work Order ID</th>
        <th>Vehicle</th>
        <th>Customer</th>
        <th>Mechanic</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>

    <tbody>
      <?php if (!empty($reports)): ?>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['report_id']) ?></td>
            <td><?= htmlspecialchars($r['work_order_id']) ?></td>
            <td><?= htmlspecialchars($r['license_plate'] ?? '-') ?></td>
            <td><?= htmlspecialchars(
        ($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')
    ) ?></td>
            <td><?= htmlspecialchars($r['mechanic_code'] ?? 'N/A') ?></td>

            <td>
              <span class="status <?= htmlspecialchars($r['status']) ?>">
                <?= htmlspecialchars(ucfirst($r['status'])) ?>
              </span>
            </td>

            <td><?= htmlspecialchars($r['created_at']) ?></td>
            <td>
    <a class="btn small edit"
       href="<?= rtrim(BASE_URL, '/') ?>/supervisor/reports/view/<?= $r['report_id'] ?>">
        View
    </a>

    <a class="btn small edit"
       href="<?= rtrim(BASE_URL, '/') ?>/supervisor/reports/edit/<?= $r['report_id'] ?>">
        Edit
    </a>

    <form method="post"
          action="<?= rtrim(BASE_URL, '/') ?>/supervisor/reports/delete/<?= $r['report_id'] ?>"
          style="display:inline"
          onsubmit="return confirm('Delete this report?')">
        <button type="submit" class="btn small danger">
            Delete
        </button>
    </form>
</td>

          </tr>
        <?php endforeach; ?>
      <?php else: ?>
        <tr>
          <td colspan="8" style="text-align:center;">
            No reports found.
          </td>
        </tr>
      <?php endif; ?>
    </tbody>
  </table>

</div>
</body>
</html>
