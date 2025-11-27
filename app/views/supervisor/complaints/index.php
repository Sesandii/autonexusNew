<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Customer Complaints</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-complaints.css"/>
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
    <a href="/autonexus/supervisor/complaints" class="nav"><img src="/autonexus/public/assets/img/Complaints.png"/>Complaints</a>
    <a href="/autonexus/supervisor/feedbacks"><img src="/autonexus/public/assets/img/Feedbacks.png"/>Feedbacks</a>
    <a href="/autonexus/supervisor/reports"><img src="/autonexus/public/assets/img/Inspection.png"/>Report</a>
  </div>

  <main class="main-content">
    <header>
      <input type="text" placeholder="Search complaints..." class="search" id="searchInput" />
    </header>

    <section class="complaints-section">
      <h2>Customer Complaints</h2>
      <div class="complaints-container">
  <?php if (!empty($complaints)): ?>
    <?php foreach ($complaints as $complaint): ?>
      <div class="complaint-row">
        <div class="complaint-header">
          <div>
            <h3><?= htmlspecialchars($complaint['customer_name']); ?></h3>
            <p class="meta">
              <strong>Vehicle:</strong> <?= htmlspecialchars($complaint['vehicle']); ?> (<?= htmlspecialchars($complaint['vehicle_number']); ?>)
              &nbsp; | &nbsp;
              <strong>Date:</strong> <?= htmlspecialchars($complaint['complaint_date']); ?> <?= htmlspecialchars($complaint['complaint_time']); ?>
            </p>
          </div>
          <span class="priority <?= strtolower($complaint['priority']); ?>">
            <?= htmlspecialchars($complaint['priority']); ?>
          </span>
        </div>

        <p class="description"><?= htmlspecialchars($complaint['description']); ?></p>

        <div class="complaint-footer">
          <p><strong>Status:</strong> <span class="status"><?= htmlspecialchars($complaint['status']); ?></span></p>
          <p><strong>Assigned To:</strong> <?= htmlspecialchars($complaint['assigned_to']); ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  <?php else: ?>
    <p class="no-data">No complaints found.</p>
  <?php endif; ?>
</div>

    </section>
  </main>
</body>
</html>
