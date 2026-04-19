<?php
// Filter logic - must be at the very top before any HTML
$filter = $_GET['filter'] ?? 'today';

$filtered = array_filter($workOrders, function($order) use ($filter) {
    $date = date('Y-m-d', strtotime($order['job_start_time']));
    $today = date('Y-m-d');

    return match($filter) {
        'today' => $date === $today,
        'week'  => $date >= date('Y-m-d', strtotime('monday this week')) 
                && $date <= date('Y-m-d', strtotime('sunday this week')),
        'month' => date('Y-m', strtotime($date)) === date('Y-m'),
        'all'   => true,
        default => true
    };
});

$scheduled  = array_filter($filtered, fn($wo) => $wo['status'] === 'open');
$inProgress = array_filter($filtered, fn($wo) => $wo['status'] === 'in_progress');
$completed  = array_filter($filtered, fn($wo) => $wo['status'] === 'completed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $pageTitle ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/personalSchedule.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <!-- Add this in the head or at the top of body -->
<meta name="base-url" content="<?= BASE_URL ?>">

</head>
<body>

<input type="hidden" id="branchId" value="<?= $employee['branch_id'] ?>">

<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
  <div class="personal-schedule">
    
    <!-- Back button -->
    <a href="<?= BASE_URL ?>/manager/schedule" class="back-button">← Back to Team Overview</a>
    
    <!-- Header with employee info -->
    <header class="header">
      <div class="profile">
        <div class="avatar">
          <?= strtoupper(substr($employee['first_name'], 0, 1) . substr($employee['last_name'], 0, 1)) ?>
        </div>
        <div class="profile-info">
          <h1 class="name"><?= htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']) ?></h1>
          <p class="role-branch">
            <?= ucfirst($employee['role']) ?> · Branch <?= $employee['branch_id'] ?>
          </p>
        </div>
      </div>

  
      <!-- Date Filter Section -->

  <form method="GET" action="">
    <input type="hidden" name="id" value="<?= $employee['user_id'] ?>">
    <select class="filter-dropdown" name="filter" onchange="this.form.submit()">
        <option value="today" <?= ($filter === 'today') ? 'selected' : '' ?>>Today</option>
        <option value="week"  <?= ($filter === 'week')  ? 'selected' : '' ?>>Week</option>
        <option value="month" <?= ($filter === 'month') ? 'selected' : '' ?>>Month</option>
        <option value="all"   <?= ($filter === 'all')   ? 'selected' : '' ?>>All Time</option>
    </select>
</form>

    </header>

    <!-- Kanban Board Columns -->
    <section class="schedule-columns">
      
      <!-- Scheduled Column -->
<div class="column scheduled">
    <div class="column-header">
        <h2>Scheduled</h2>
        <div class="count"><?= count($scheduled) ?></div>
    </div>
    <div class="cards-list">
        <?php if (empty($scheduled)): ?>
            <div class="empty-column">No scheduled appointments</div>
        <?php else: foreach ($scheduled as $order): ?>
            <div class="card">
                <div class="card-header">
                    <div class="tag scheduled-tag">
                        <?= htmlspecialchars($order['service_name'] ?? 'Service') ?>
                    </div>
                </div>
                <div class="card-name">
                    <?= htmlspecialchars(($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? '')) ?>
                </div>
                <div class="card-details">
                    <span class="icon">🚗</span>
                    <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '') . ' (' . ($order['year'] ?? 'N/A') . ')') ?>
                </div>
                <div class="card-details">
                    <span class="icon">⏰</span>
                    <?= date('h:i A', strtotime($order['job_start_time'])) ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- In Progress Column -->
<div class="column in-progress">
    <div class="column-header">
        <h2>In Progress</h2>
        <div class="count"><?= count($inProgress) ?></div>
    </div>
    <div class="cards-list">
        <?php if (empty($inProgress)): ?>
            <div class="empty-column">No appointments in progress</div>
        <?php else: foreach ($inProgress as $order): ?>
            <div class="card">
                <div class="card-header">
                    <div class="tag scheduled-tag">
                        <?= htmlspecialchars($order['service_name'] ?? 'Service') ?>
                    </div>
                </div>
                <div class="card-name">
                    <?= htmlspecialchars(($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? '')) ?>
                </div>
                <div class="card-details">
                    <span class="icon">🚗</span>
                    <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '') . ' (' . ($order['year'] ?? 'N/A') . ')') ?>
                </div>
                <div class="card-details">
                    <span class="icon">⏰</span>
                    <?= date('h:i A', strtotime($order['job_start_time'])) ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>

<!-- Completed Column -->
<div class="column completed">
    <div class="column-header">
        <h2>Completed</h2>
        <div class="count"><?= count($completed) ?></div>
    </div>
    <div class="cards-list">
        <?php if (empty($completed)): ?>
            <div class="empty-column">No completed appointments</div>
        <?php else: foreach ($completed as $order): ?>
            <div class="card">
                <div class="card-header">
                    <div class="tag scheduled-tag">
                        <?= htmlspecialchars($order['service_name'] ?? 'Service') ?>
                    </div>
                </div>
                <div class="card-name">
                    <?= htmlspecialchars(($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? '')) ?>
                </div>
                <div class="card-details">
                    <span class="icon">🚗</span>
                    <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '') . ' (' . ($order['year'] ?? 'N/A') . ')') ?>
                </div>
                <div class="card-details">
                    <span class="icon">⏰</span>
                    <?= date('h:i A', strtotime($order['job_start_time'])) ?>
                </div>
            </div>
        <?php endforeach; endif; ?>
    </div>
</div>
      
    </section>
  </div>

 <!-- Reassign Modal -->
<!--<div id="reassignModal" class="modal" style="display: none;">
  <div class="modal-content">
    <div class="modal-header">
      <h3>Reassign Work Order</h3>
      <button class="close-btn" onclick="closeReassignModal()">&times;</button>
    </div>
    <div class="modal-body">
      <form id="reassignForm" method="POST" action="<?= BASE_URL ?>/manager/schedule/reassignWorkOrder">
        <input type="hidden" name="work_order_id" id="workOrderId">
        
        <div class="form-group">
          <label for="newMechanic">Select New Mechanic:</label>
          <select name="new_mechanic_id" id="newMechanic" required>
            <option value="">Loading mechanics...</option>
          </select>
        </div>
        
        <div class="modal-actions">
          <button type="button" class="btn-cancel" onclick="closeReassignModal()">Cancel</button>
          <button type="submit" class="btn-confirm">Reassign</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="modalOverlay" class="modal-overlay" style="display: none;" onclick="closeReassignModal()"></div>-->

</div>
<script src="<?= BASE_URL ?>/public/assets/js/manager/personalSchedule.js"></script>
</body>
</html>