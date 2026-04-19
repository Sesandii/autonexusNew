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
      <li class="tab-item <?= $activeTab === 'all-orders'  ? 'active' : '' ?>" data-tab="all-orders">All Work Orders</li>
      <li class="tab-item <?= $activeTab === 'in-progress' ? 'active' : '' ?>" data-tab="in-progress">In Progress</li>
      <li class="tab-item <?= $activeTab === 'completed'   ? 'active' : '' ?>" data-tab="completed">Completed</li>
    </ul>
  </nav>

  <!-- ── All Work Orders Tab ── -->
  <section id="all-orders" class="tab-content <?= $activeTab === 'all-orders' ? 'active' : '' ?>">
    <div class="work-order-list">
      <div class="search-filter">
        <form method="GET" action="">
          <input type="hidden" name="tab" value="all-orders">
          <select class="filter-dropdown" name="status" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="open"        <?= $statusFilter === 'open'        ? 'selected' : '' ?>>Open</option>
            <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="on_hold"     <?= $statusFilter === 'on_hold'     ? 'selected' : '' ?>>On Hold</option>
            <option value="completed"   <?= $statusFilter === 'completed'   ? 'selected' : '' ?>>Completed</option>
          </select>
          <select class="filter-dropdown" name="date" onchange="this.form.submit()">
            <option value="all"   <?= $dateFilter === 'all'   ? 'selected' : '' ?>>All Time</option>
            <option value="today" <?= $dateFilter === 'today' ? 'selected' : '' ?>>Today</option>
            <option value="week"  <?= $dateFilter === 'week'  ? 'selected' : '' ?>>This Week</option>
            <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>This Month</option>
          </select>
        </form>
      </div>

      <table>
        <thead>
          <tr>
            <th>WO ID</th><th>Vehicle</th><th>Service</th><th>Mechanic</th>
            <th>Supervisor</th><th>Status</th><th>Cost</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($allFiltered)): ?>
            <?php foreach ($allFiltered as $wo): ?>
              <?php
                $status          = strtolower($wo['work_order_status'] ?? 'open');
                $statusClass     = 'status-' . str_replace('_', '-', $status);
                $statusDisplay   = match($status) {
                    'open' => 'Open', 'in_progress' => 'In Progress',
                    'on_hold' => 'On Hold', 'completed' => 'Completed',
                    default => ucfirst($status)
                };
                $vehicleDisplay  = ($wo['make'] ?? 'N/A') . ' ' . ($wo['model'] ?? '');
                $vehicleDetail   = '';
                if (!empty($wo['year']))          $vehicleDetail .= $wo['year'];
                if (!empty($wo['license_plate'])) $vehicleDetail .= ($vehicleDetail ? ' • ' : '') . $wo['license_plate'];
                if (!empty($wo['color']))         $vehicleDetail .= ($vehicleDetail ? ' • ' : '') . $wo['color'];
                $mechanicDisplay   = !empty($wo['mechanic_first_name'])   ? $wo['mechanic_first_name']   . ' ' . ($wo['mechanic_last_name']   ?? '') : 'Not Assigned';
                $supervisorDisplay = !empty($wo['supervisor_first_name']) ? $wo['supervisor_first_name'] . ' ' . ($wo['supervisor_last_name'] ?? '') : 'Not Assigned';
              ?>
              <tr>
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
                        <i class="fas fa-user" style="font-size:10px;"></i>
                        <?= htmlspecialchars($wo['customer_first_name'] . ' ' . ($wo['customer_last_name'] ?? '')) ?>
                      </span>
                    <?php endif; ?>
                  </div>
                </td>
                <td>
                  <?= htmlspecialchars($wo['service_name'] ?? 'No service assigned') ?>
                  <?php if (!empty($wo['service_code'])): ?>
                    <div class="vehicle-detail"><?= htmlspecialchars($wo['service_code']) ?></div>
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($mechanicDisplay) ?></td>
                <td><?= htmlspecialchars($supervisorDisplay) ?></td>
                <td><span class="status-badge <?= $statusClass ?>"><?= $statusDisplay ?></span></td>
                <td class="cost-column">
                  <?= !empty($wo['total_cost']) ? 'Rs. ' . number_format($wo['total_cost'], 2) : '<span style="color:#9ca3af;">—</span>' ?>
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

  <!-- ── In Progress Tab ── -->
  <section id="in-progress" class="tab-content <?= $activeTab === 'in-progress' ? 'active' : '' ?>">
    <div class="work-order-list">
      <div class="search-filter">
        <form method="GET" action="">
          <input type="hidden" name="tab" value="in-progress">
          <select class="filter-dropdown" name="date" onchange="this.form.submit()">
            <option value="all"   <?= $dateFilter === 'all'   ? 'selected' : '' ?>>All Time</option>
            <option value="today" <?= $dateFilter === 'today' ? 'selected' : '' ?>>Today</option>
            <option value="week"  <?= $dateFilter === 'week'  ? 'selected' : '' ?>>This Week</option>
            <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>This Month</option>
          </select>
        </form>
      </div>

      <table>
        <thead>
          <tr>
            <th>WO ID</th><th>Vehicle</th><th>Service</th>
            <th>Mechanic</th><th>Started</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($inProgressOrders)): ?>
            <?php foreach ($inProgressOrders as $wo): ?>
              <tr>
                <td>
                  <strong>#<?= htmlspecialchars($wo['work_order_id']) ?></strong>
                  <?php if (!empty($wo['appointment_date'])): ?>
                    <div class="vehicle-detail"><?= date('M d, Y', strtotime($wo['appointment_date'])) ?></div>
                  <?php endif; ?>
                </td>
                <td>
                  <?= htmlspecialchars(($wo['make'] ?? 'N/A') . ' ' . ($wo['model'] ?? '')) ?>
                  <div class="vehicle-detail"><?= htmlspecialchars($wo['license_plate'] ?? '') ?></div>
                </td>
                <td><?= htmlspecialchars($wo['service_name'] ?? '—') ?></td>
                <td><?= !empty($wo['mechanic_first_name']) ? htmlspecialchars($wo['mechanic_first_name'] . ' ' . ($wo['mechanic_last_name'] ?? '')) : 'Not Assigned' ?></td>
                <td><?= !empty($wo['started_at']) ? date('M d, Y h:i A', strtotime($wo['started_at'])) : '—' ?></td>
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
            <tr><td colspan="6" class="no-data">No work orders in progress</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>

  <!-- ── Completed Tab ── -->
  <section id="completed" class="tab-content <?= $activeTab === 'completed' ? 'active' : '' ?>">
    <div class="work-order-list">
      <div class="search-filter">
        <form method="GET" action="">
          <input type="hidden" name="tab" value="completed">
          <select class="filter-dropdown" name="date" onchange="this.form.submit()">
            <option value="all"   <?= $dateFilter === 'all'   ? 'selected' : '' ?>>All Time</option>
            <option value="today" <?= $dateFilter === 'today' ? 'selected' : '' ?>>Today</option>
            <option value="week"  <?= $dateFilter === 'week'  ? 'selected' : '' ?>>This Week</option>
            <option value="month" <?= $dateFilter === 'month' ? 'selected' : '' ?>>This Month</option>
          </select>
        </form>
      </div>

      <table>
        <thead>
          <tr>
            <th>WO ID</th><th>Vehicle</th><th>Service</th>
            <th>Completed</th><th>Cost</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($completedOrders)): ?>
            <?php foreach ($completedOrders as $wo): ?>
              <tr>
                <td><strong>#<?= htmlspecialchars($wo['work_order_id']) ?></strong></td>
                <td>
                  <?= htmlspecialchars(($wo['make'] ?? 'N/A') . ' ' . ($wo['model'] ?? '')) ?>
                  <div class="vehicle-detail"><?= htmlspecialchars($wo['license_plate'] ?? '') ?></div>
                </td>
                <td><?= htmlspecialchars($wo['service_name'] ?? '—') ?></td>
                <td><?= !empty($wo['completed_at']) ? date('M d, Y h:i A', strtotime($wo['completed_at'])) : '—' ?></td>
                <td class="cost-column">
                  <?= !empty($wo['total_cost']) ? 'Rs. ' . number_format($wo['total_cost'], 2) : '<span style="color:#9ca3af;">—</span>' ?>
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
            <tr><td colspan="6" class="no-data">No completed work orders</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<script>
document.querySelectorAll('.tab-item').forEach(tab => {
    tab.addEventListener('click', function () {
        document.querySelectorAll('.tab-item').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.getAttribute('data-tab')).classList.add('active');
    });
});
</script>

<style>
.no-data { text-align: center; padding: 40px !important; color: #9ca3af; }
</style>

</body>
</html>