<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Assigned Work Orders</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-assignedjobs.css"/>
</head>

<body>
<?php include __DIR__ . '/../partials/sidebar.php'; ?>

  <main class="main">

  <h1>Assigned Jobs</h1>
      <p>Here are all jobs already assigned to mechanics</p>

    <header>
    <input type="text" id="jobSearch" placeholder="Search by Work Order or Service..." class="search" />
    <div class="filters">
  <select id="serviceFilter">
    <option value="">All Services</option>
    <?php
      $services = array_unique(array_column($workOrders, 'service_name'));
      foreach ($services as $service):
    ?>
      <option value="<?= strtolower($service) ?>"><?= $service ?></option>
    <?php endforeach; ?>
  </select>

  <select id="mechanicFilter">
    <option value="">All Mechanics</option>
    <?php
      $mechanics = array_unique(array_column($workOrders, 'mechanic_code'));
      foreach ($mechanics as $mechanic):
    ?>
      <option value="<?= strtolower($mechanic) ?>"><?= $mechanic ?></option>
    <?php endforeach; ?>
  </select>

  <select id="statusFilter">
    <option value="">All Statuses</option>
    <option value="open">Open</option>
    <option value="in_progress">In Progress</option>
    <option value="completed">Completed</option>
  </select>
  <button id="resetFilters" class="btn small">Reset</button>
</div>

  </header>

    <section class="job-section">
      <div class="job-grid">
        <?php if (!empty($workOrders)): ?>
          <?php foreach ($workOrders as $job): ?>
            <div class="job-card"
     data-workorder="<?= $job['work_order_id'] ?>"
     data-service="<?= strtolower($job['service_name']) ?>"
     data-mechanic="<?= strtolower($job['mechanic_code']) ?>"
     data-status="<?= strtolower($job['status']) ?>">
              <h3>Work Order <?= $job['work_order_id'] ?></h3>
              <div class="job-info"><span>Service:</span> <?= $job['service_name'] ?></div>
              <div class="job-info"><span>Mechanic:</span> <?= $job['mechanic_code'] ?></p></div>
              <span class="status <?= strtolower($job['status']) ?>">
              <div class="job-info"><span>Status: </span><?= $job['status'] ?></div>
              </span>
              <div class="progress-wrapper">
  <div class="progress-label">
    Progress: <?= $job['progress'] ?>%
  </div>
  <div class="progress-bar">
    <div 
      class="progress-fill"
      style="width: <?= $job['progress'] ?>%">
    </div>
  </div>
</div>
              <button class="edit-btn" onclick="location.href='/autonexus/supervisor/assignedjobs/<?= $job['work_order_id'] ?>'">Edit</button>
            </div>
          <?php endforeach; ?>

        <?php else: ?>
          <p class="no-jobs">No assigned jobs available.</p>
        <?php endif; ?>
      </div>
    </section>
  </main>
  <script src="/autonexus/public/assets/js/supervisor/script-assignedjobs.js"></script>
</body>
</html>
