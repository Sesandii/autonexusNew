<?php $base = rtrim(BASE_URL, '/'); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Customer Feedback</title>
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/style-feedbacks.css"/>
</head>
<body>

<div class="sidebar">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto.png" alt="Logo" class="logo">
  </div>
  <h2>AUTONEXUS</h2>
  <a href="/autonexus/supervisor/dashboard"><img src="/autonexus/public/assets/img/dashboard.png"/>Dashboard</a>
  <a href="/autonexus/supervisor/workorders"><img src="/autonexus/public/assets/img/jobs.png"/>Work Orders</a>
  <a href="/autonexus/supervisor/assignedjobs"><img src="/autonexus/public/assets/img/assigned.png"/>Assigned</a>
  <a href="/autonexus/supervisor/history"><img src="/autonexus/public/assets/img/history.png"/>Vehicle History</a>
  <a href="/autonexus/supervisor/complaints"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
  <a href="/autonexus/supervisor/feedbacks" class="nav"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
  <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
</div>

<main class="main-content">
  <header>
    <input type="text" placeholder="Search feedback..." class="search" />
  </header>

  <section class="feedback-section">
    <h2>Customer Feedback</h2>

    <div class="feedback-cards">
      <?php if (!empty($feedbacks)): ?>
        <?php foreach ($feedbacks as $f): ?>
          <div class="card">
            <h3><?= htmlspecialchars($f['customer_name']) ?>
              <span class="rating <?= ($f['rating'] >= 4 ? 'good' : ($f['rating'] >= 2 ? 'avg' : 'bad')) ?>">
                <?= htmlspecialchars($f['rating']) ?>/5 ★
              </span>
            </h3>
            <p><strong>Service:</strong> <?= htmlspecialchars($f['service_name']) ?></p>
            <p><strong>Date:</strong> <?= htmlspecialchars($f['appointment_date']) ?></p>
            <p><?= htmlspecialchars($f['comment']) ?></p>
            <span class="reply <?= ($f['replied_status'] ? 'replied' : 'not-replied') ?>">
              <?= ($f['replied_status'] ? 'Replied' : 'Not replied yet') ?>
            </span>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No feedbacks found.</p>
      <?php endif; ?>
    </div>
  </section>
</main>

</body>
</html>
