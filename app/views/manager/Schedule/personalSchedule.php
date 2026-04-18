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
<div class="filter-section">
  <div class="filter-buttons">
    <button class="filter-btn active" data-filter="today" onclick="filterWorkOrders('today')">Today</button>
    <button class="filter-btn" data-filter="week" onclick="filterWorkOrders('week')">This Week</button>
    <button class="filter-btn" data-filter="month" onclick="filterWorkOrders('month')">This Month</button>
    <button class="filter-btn" data-filter="year" onclick="filterWorkOrders('year')">This Year</button>
    <button class="filter-btn" data-filter="all" onclick="filterWorkOrders('all')">All Time</button>
  </div>
  <div class="date-range-display" id="dateRangeDisplay"></div>
</div>
    </header>

    <!-- Kanban Board Columns -->
    <section class="schedule-columns">
      
      <!-- Scheduled Column -->
      <div class="column scheduled">
        <div class="column-header">
          <h2>Scheduled</h2>
          <div class="count"><?= count(array_filter($workOrders, fn($wo) => $wo['status'] === 'open')) ?></div>
        </div>
        <div class="cards-list">
          <?php 
          $hasScheduled = false;
          foreach ($workOrders as $order): 
            if ($order['status'] !== 'open') continue;
            $hasScheduled = true;
          ?>
             <div class="card">
  <div class="card-header">
    <div class="tag scheduled-tag">
      <?= htmlspecialchars($order['service_name'] ?? 'Service') ?>
    </div>
 
           </div>
  <div class="card-name">
    <?= htmlspecialchars(($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? $order['license_plate'] ?? 'N/A')) ?>
  </div>
  <div class="card-details">
    <span class="icon">🚗</span> 
    <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '') . ' (' . ($order['year'] ?? 'N/A') . ')') ?>
  </div>
  <div class="card-details">
    <span class="icon">⏰</span> 
    <?= date('h:i A', strtotime($order['job_start_time'] ?? 'now')) ?>
  </div>
</div>
<?php endforeach; ?>
          
          <?php if (!$hasScheduled): ?>
            <div class="empty-column">No scheduled work orders</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- In Progress Column -->
      <div class="column in-progress">
        <div class="column-header">
          <h2>In Progress</h2>
          <div class="count"><?= count(array_filter($workOrders, fn($wo) => $wo['status'] === 'in_progress')) ?></div>
        </div>
        <div class="cards-list">
          <?php 
          $hasInProgress = false;
          foreach ($workOrders as $order): 
            if ($order['status'] !== 'in_progress') continue;
            $hasInProgress = true;
          ?>
          <div class="card">
  <div class="card-header">
    <div class="tag scheduled-tag">
      <?= htmlspecialchars($order['service_name'] ?? 'Service') ?>
    </div>
   
  </div>
  <div class="card-name">
    <?= htmlspecialchars(($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? $order['license_plate'] ?? 'N/A')) ?>
  </div>
  <div class="card-details">
    <span class="icon">🚗</span> 
    <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '') . ' (' . ($order['year'] ?? 'N/A') . ')') ?>
  </div>
  <div class="card-details">
    <span class="icon">⏰</span> 
    <?= date('h:i A', strtotime($order['job_start_time'] ?? 'now')) ?>
  </div>
</div>
          <?php endforeach; ?>
          
          <?php if (!$hasInProgress): ?>
            <div class="empty-column">No work orders in progress</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Completed Column -->
      <div class="column completed">
        <div class="column-header">
          <h2>Completed</h2>
          <div class="count"><?= count(array_filter($workOrders, fn($wo) => $wo['status'] === 'completed')) ?></div>
        </div>
        <div class="cards-list">
          <?php 
          $hasCompleted = false;
          foreach ($workOrders as $order): 
            if ($order['status'] !== 'completed') continue;
            $hasCompleted = true;
          ?>
            <div class="card">
  <div class="card-header">
    <div class="tag scheduled-tag">
      <?= htmlspecialchars($order['service_name'] ?? 'Service') ?>
    </div>
    <!--<button class="reassign-btn" 
            onclick="openReassignModal(<?= $order['work_order_id'] ?>, <?= $order['mechanic_id'] ?? 0 ?>)"
            title="Reassign work order">
  
    </button>-->
  </div>
  <div class="card-name">
    <?= htmlspecialchars(($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? $order['license_plate'] ?? 'N/A')) ?>
  </div>
  <div class="card-details">
    <span class="icon">🚗</span> 
    <?= htmlspecialchars(($order['make'] ?? '') . ' ' . ($order['model'] ?? '') . ' (' . ($order['year'] ?? 'N/A') . ')') ?>
  </div>
  <div class="card-details">
    <span class="icon">⏰</span> 
    <?= date('h:i A', strtotime($order['job_start_time'] ?? 'now')) ?>
  </div>
</div>
          <?php endforeach; ?>
          
          <?php if (!$hasCompleted): ?>
            <div class="empty-column">No completed work orders</div>
          <?php endif; ?>
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