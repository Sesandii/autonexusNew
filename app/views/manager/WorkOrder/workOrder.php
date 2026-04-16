<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Work Orders - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/workOrder.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
  <header class="header-bar">
    <h1>Work Orders</h1>
  </header>

  <nav class="tab-nav">
    <ul class="tab-list">
      <li class="tab-item active" data-tab="all-orders">All Work Orders</li>
      <li class="tab-item" data-tab="in-progress">In Progress</li>
      <li class="tab-item" data-tab="completed">Completed</li>
    </ul>
  </nav>

  <!-- All Work Orders Tab -->
  <section id="all-orders" class="tab-content active">
    <div class="work-order-list">
      <div class="search-filter">
        <input type="text" placeholder="Search..." class="search-bar" id="searchWorkOrders">
        <select id="statusFilter">
          <option value="">All Status</option>
          <option value="open">Open</option>
          <option value="in_progress">In Progress</option>
          <option value="on_hold">On Hold</option>
          <option value="completed">Completed</option>
        </select>
      </div>
      
      <table>
        <thead>
          <tr>
            <th>WO ID</th>
            <th>Vehicle</th>
            <th>Service</th>
            <th>Mechanic</th>
            <th>Supervisor</th>
            <th>Status</th>
            <th>Cost</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="allOrdersTable">
          <?php if(!empty($workOrders)): ?>
            <?php foreach($workOrders as $wo): ?>
              <?php
                $status = strtolower($wo['work_order_status'] ?? 'open');
                $statusClass = 'status-' . str_replace('_', '-', $status);
                $statusDisplay = match($status) {
                    'open' => 'Open',
                    'in_progress' => 'In Progress',
                    'on_hold' => 'On Hold',
                    'completed' => 'Completed',
                    default => ucfirst($status)
                };
                
                $vehicleDisplay = ($wo['make'] ?? 'N/A') . ' ' . ($wo['model'] ?? '');
                $vehicleDetail = '';
                if (!empty($wo['year'])) $vehicleDetail .= $wo['year'];
                if (!empty($wo['license_plate'])) $vehicleDetail .= ($vehicleDetail ? ' • ' : '') . $wo['license_plate'];
                if (!empty($wo['color'])) $vehicleDetail .= ($vehicleDetail ? ' • ' : '') . $wo['color'];
                
                $mechanicDisplay = !empty($wo['mechanic_first_name']) ? $wo['mechanic_first_name'] . ' ' . ($wo['mechanic_last_name'] ?? '') : 'Not Assigned';
                $supervisorDisplay = !empty($wo['supervisor_first_name']) ? $wo['supervisor_first_name'] . ' ' . ($wo['supervisor_last_name'] ?? '') : 'Not Assigned';
              ?>
              <tr data-status="<?= $status ?>">
                <td>
                  <strong>#<?= htmlspecialchars($wo['work_order_id']) ?></strong>
                  <?php if (!empty($wo['appointment_date'])): ?>
                    <div class="vehicle-detail"><?= date('M d, Y', strtotime($wo['appointment_date'])) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <div class="vehicle-info">
                    <span><?= htmlspecialchars($vehicleDisplay) ?></span>
                    <?php if ($vehicleDetail): ?>
                      <span class="vehicle-detail"><?= htmlspecialchars($vehicleDetail) ?></span>
                    <?php endif; ?>
                    <?php if (!empty($wo['customer_first_name'])): ?>
                      <span class="vehicle-detail">
                        <i class="fas fa-user" style="font-size: 10px;"></i> 
                        <?= htmlspecialchars($wo['customer_first_name'] . ' ' . ($wo['customer_last_name'] ?? '')) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <?= htmlspecialchars($wo['service_name'] ?? 'No service assigned') ?>
                  <?php if (!empty($wo['service_code'])): ?>
                    <div class="vehicle-detail"><?= $wo['service_code'] ?></div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($mechanicDisplay) ?></td>
                <td><?= htmlspecialchars($supervisorDisplay) ?></td>
                <td>
                  <span class="status-badge <?= $statusClass ?>"><?= $statusDisplay ?></span>
                </td>
                <td class="cost-column">
                  <?= !empty($wo['total_cost']) ? 'Rs. ' . number_format($wo['total_cost'], 2) : '<span style="color: #9ca3af;">—</span>' ?>
                </td>
                <td>
                  <div class="action-icons">
                    <a href="<?= BASE_URL ?>/manager/work-orders/detail/<?= $wo['work_order_id'] ?>" title="View Details">
                      <i class="fas fa-eye"></i>
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="8" class="no-data">No work orders found</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- In Progress Tab -->
  <section id="in-progress" class="tab-content">
    <div class="work-order-list">
      <div class="search-filter">
        <input type="text" placeholder="Search in progress..." class="search-bar" id="searchInProgress">
      </div>
      <table>
        <thead>
          <tr>
            <th>WO ID</th>
            <th>Vehicle</th>
            <th>Service</th>
            <th>Mechanic</th>
            <th>Started</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="inProgressTable">
          <!-- Populated by JavaScript -->
        </tbody>
      </table>
    </div>
  </section>

  <!-- Completed Tab -->
  <section id="completed" class="tab-content">
    <div class="work-order-list">
      <div class="search-filter">
        <input type="text" placeholder="Search completed..." class="search-bar" id="searchCompleted">
      </div>
      <table>
        <thead>
          <tr>
            <th>WO ID</th>
            <th>Vehicle</th>
            <th>Service</th>
            <th>Completed</th>
            <th>Cost</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="completedTable">
          <!-- Populated by JavaScript -->
        </tbody>
      </table>
    </div>
  </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  // Get all rows from the main table
  const allRows = document.querySelectorAll('#allOrdersTable tr[data-status]');
  
  // Function to populate a table with filtered rows
  function populateTable(targetTableId, statusFilter) {
    const targetTable = document.getElementById(targetTableId);
    if (!targetTable) return;
    
    let html = '';
    let hasRows = false;
    
    allRows.forEach(row => {
      const status = row.getAttribute('data-status');
      const cells = row.querySelectorAll('td');
      
      if (status === statusFilter) {
        hasRows = true;
        
        if (targetTableId === 'inProgressTable') {
          // Format for in-progress table
          html += `<tr>`;
          html += `<td>${cells[0].innerHTML}</td>`; // WO ID
          html += `<td>${cells[1].innerHTML}</td>`; // Vehicle
          html += `<td>${cells[2].innerHTML}</td>`; // Service
          html += `<td>${cells[3].innerHTML}</td>`; // Mechanic
          
          // Get started time from the row's data attribute or use placeholder
          const startedTime = row.querySelector('.started-time')?.innerHTML || '—';
          html += `<td>${startedTime}</td>`;
          
          html += `<td>${cells[5].innerHTML}</td>`; // Status
          html += `<td>${cells[7].innerHTML}</td>`; // Actions
          html += `</tr>`;
          
        } else if (targetTableId === 'completedTable') {
          // Format for completed table
          html += `<tr>`;
          html += `<td>${cells[0].innerHTML}</td>`; // WO ID
          html += `<td>${cells[1].innerHTML}</td>`; // Vehicle
          html += `<td>${cells[2].innerHTML}</td>`; // Service
          
          // Get completed date
          const completedDate = row.querySelector('.completed-date')?.innerHTML || 
                               cells[0].querySelector('.vehicle-detail')?.innerHTML || '—';
          html += `<td>${completedDate}</td>`;
          
          html += `<td>${cells[6].innerHTML}</td>`; // Cost
          html += `<td>${cells[7].innerHTML}</td>`; // Actions
          html += `</tr>`;
        }
      }
    });
    
    if (!hasRows) {
      const colSpan = targetTableId === 'inProgressTable' ? 7 : 6;
      html = `<tr><td colspan="${colSpan}" class="no-data">No work orders found</td></tr>`;
    }
    
    targetTable.innerHTML = html;
  }
  
  // Populate the filtered tables on page load
  populateTable('inProgressTable', 'in_progress');
  populateTable('completedTable', 'completed');
  
  // Tab switching
  const tabs = document.querySelectorAll('.tab-item');
  const contents = document.querySelectorAll('.tab-content');
  
  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      const targetId = this.getAttribute('data-tab');
      
      tabs.forEach(t => t.classList.remove('active'));
      contents.forEach(c => c.classList.remove('active'));
      
      this.classList.add('active');
      document.getElementById(targetId).classList.add('active');
    });
  });
  
  // Status filter for All Orders tab
  const statusFilter = document.getElementById('statusFilter');
  if (statusFilter) {
    statusFilter.addEventListener('change', function() {
      const filterValue = this.value.toLowerCase();
      const rows = document.querySelectorAll('#allOrdersTable tr[data-status]');
      
      rows.forEach(row => {
        if (!filterValue) {
          row.style.display = '';
        } else {
          const status = row.getAttribute('data-status');
          row.style.display = status === filterValue ? '' : 'none';
        }
      });
    });
  }
  
  // Search functionality for All Orders
  const searchAll = document.getElementById('searchWorkOrders');
  if (searchAll) {
    searchAll.addEventListener('keyup', function() {
      const term = this.value.toLowerCase();
      const rows = document.querySelectorAll('#allOrdersTable tr[data-status]');
      
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(term) ? '' : 'none';
      });
    });
  }
});
</script>

<style>
.no-data {
  text-align: center;
  padding: 40px !important;
  color: #9ca3af;
}
</style>

</body>
</html>