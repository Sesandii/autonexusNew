<?php $base = rtrim(BASE_URL, '/'); ?>
<?php 
$currentMechanicId = $_SESSION['user']['mechanic_id'] ?? null; 
?>

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
  <h1>Welcome, <?= htmlspecialchars(($_SESSION['user']['first_name'] ?? '') . ' ' . ($_SESSION['user']['last_name'] ?? '')) ?></h1>
    <p>Here's an overview of your dashboard</p>
  </section>
  <div class="dashboard-layout">
    <div class="tiles-grid">
        <div class="stat-card">
        <div class="stat-icon-box bg-light-orange">
        <i class="fa-solid fa-calendar-day icon-orange"></i>
      </div>
        <div class="stat-content">
        <span class="stat-label">Appointments Pending</span>
        <h2 class="stat-number"><?= $branch_pending ?? 0 ?></h2>
        </div>
        </div>
        
        <div class="stat-card">
      <div class="stat-icon-box bg-light-blue">
        <i class="fa-solid fa-user-check icon-blue"></i>
      </div>
      <div class="stat-content">
        <span class="stat-label">My Assigned Workorders</span>
        <h2 class="stat-number"><?= (int)($stats['assigned'] ?? 0) ?></h2>
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
    </div>
    <div class="table-container">
    <div class="table-header">
        <h3>Today's Appointments</h3>
        <select id="assignmentFilter" class="filter-select">
            <option value="all">All Jobs</option>
            <option value="mine">Assigned to Me</option>
            <option value="others">Assigned to Others</option>
        </select>
    </div>
    <div class="table-scroll-area">
        <table>
            <thead>
                <tr>
                    <th>Client</th>
                    <th>Vehicle</th>
                    <th>Service</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($appointments as $appt): ?>
                    <?php 
                        $myId = (int)$mechanic_id; 
                        $jobId = (int)($appt['mechanic_id'] ?? 0);
                        $assignmentType = ($jobId === $myId) ? 'mine' : 'others';
                    ?>
                    <tr class="appointment-row" data-assignment="<?= $assignmentType ?>">
                        <td><?= htmlspecialchars($appt['customer_name']) ?></td>
                        <td><?= htmlspecialchars($appt['vehicle']) ?></td>
                        <td><?= htmlspecialchars($appt['name']) ?></td>
                        <td>
                            <a href="<?= $base ?>/mechanic/jobs/view/<?= $appt['work_order_id'] ?>" class="view-btn">View</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
    <section class="details-column">
    <div class="active-job-card">
    <?php if ($active_job): ?>
        <div class="job-content">
            <span class="badge-status">IN PROGRESS</span>
            <h4><?= htmlspecialchars($active_job['service_name']) ?></h4>
            <p><?= htmlspecialchars($active_job['vehicle']) ?></p>
        </div>
        <a href="<?= $base ?>/mechanic/jobs/view/<?= $active_job['work_order_id'] ?>" class="btn-edit-black">
             Edit
        </a>
    <?php else: ?>
        <p class="no-job-text">No active job in progress.</p>
    <?php endif; ?>
</div>    
    </section>
  </div>
</main>
<script src="<?= $base ?>/public/assets/js/mechanic/script-dashboard.js"></script>
</body>
</html>
