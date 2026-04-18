<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle ?? 'Work Order Details') ?> - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/workOrderDetail.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

<div class="main">
  <!-- Header -->
  <header class="header-bar">
    <div class="header-left">
      <a href="<?= BASE_URL ?>/manager/work-orders" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back
      </a>
      <h1>Work Order #<?= htmlspecialchars($workOrder['work_order_id']) ?></h1>
      <?php
        $status = strtolower($workOrder['status'] ?? 'open');
        $statusClass = 'status-' . str_replace('_', '-', $status);
        $statusDisplay = match($status) {
            'open' => 'Open',
            'in_progress' => 'In Progress',
            'on_hold' => 'On Hold',
            'completed' => 'Completed',
            default => ucfirst($status)
        };
      ?>
      <span class="status-badge <?= $statusClass ?>"><?= $statusDisplay ?></span>
    </div>
  </header>

  <!-- Main Content Grid -->
  <div class="detail-grid">
    
    <!-- Left Column -->
    <div class="detail-column">
      
      <!-- Customer Card -->
      <div class="info-card">
        <div class="card-header">
          <i class="fas fa-user"></i>
          <h3>Customer Information</h3>
        </div>
        <div class="card-body">
          <div class="info-row">
            <span class="label">Name</span>
            <span class="value"><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']) ?></span>
          </div>
          <div class="info-row">
            <span class="label">Customer ID</span>
            <span class="value">#<?= htmlspecialchars($customer['customer_code'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Email</span>
            <span class="value"><?= htmlspecialchars($customer['email'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Phone</span>
            <span class="value"><?= htmlspecialchars($customer['phone'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Address</span>
            <span class="value"><?= htmlspecialchars(($customer['street_address'] ?? '') . ', ' . ($customer['city'] ?? '') . ', ' . ($customer['state'] ?? '')) ?></span>
          </div>
        </div>
      </div>

      <!-- Vehicle Card -->
      <div class="info-card">
        <div class="card-header">
          <i class="fas fa-car"></i>
          <h3>Vehicle Information</h3>
        </div>
        <div class="card-body">
          <div class="info-row">
            <span class="label">Vehicle</span>
            <span class="value"><?= htmlspecialchars($vehicle['year'] . ' ' . $vehicle['make'] . ' ' . $vehicle['model']) ?></span>
          </div>
          <div class="info-row">
            <span class="label">License Plate</span>
            <span class="value"><?= htmlspecialchars($vehicle['license_plate'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">VIN</span>
            <span class="value"><?= htmlspecialchars($vehicle['vin'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Color</span>
            <span class="value"><?= htmlspecialchars($vehicle['color'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Current Mileage</span>
            <span class="value"><?= number_format($vehicle['current_mileage'] ?? 0) ?> km</span>
          </div>
          <div class="info-row">
            <span class="label">Last Service</span>
            <span class="value"><?= number_format($vehicle['last_service_mileage'] ?? 0) ?> km</span>
          </div>
        </div>
      </div>

      <!-- Service Card -->
      <div class="info-card">
        <div class="card-header">
          <i class="fas fa-wrench"></i>
          <h3>Service Details</h3>
        </div>
        <div class="card-body">
          <div class="info-row">
            <span class="label">Service</span>
            <span class="value"><?= htmlspecialchars($service['name'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Service Code</span>
            <span class="value"><?= htmlspecialchars($service['service_code'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Description</span>
            <span class="value"><?= htmlspecialchars($service['description'] ?? 'No description') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Est. Duration</span>
            <span class="value"><?= htmlspecialchars($service['base_duration_minutes'] ?? 0) ?> minutes</span>
          </div>
          <div class="info-row">
            <span class="label">Default Price</span>
            <span class="value">Rs. <?= number_format($service['default_price'] ?? 0, 2) ?></span>
          </div>
        </div>
      </div>
    </div>

    <!-- Right Column -->
    <div class="detail-column">
      
      <!-- Appointment Card -->
      <div class="info-card">
        <div class="card-header">
          <i class="fas fa-calendar"></i>
          <h3>Appointment Details</h3>
        </div>
        <div class="card-body">
          <div class="info-row">
            <span class="label">Appointment ID</span>
            <span class="value">#<?= htmlspecialchars($appointment['appointment_id'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Date</span>
            <span class="value"><?= date('F d, Y', strtotime($appointment['appointment_date'] ?? 'now')) ?></span>
          </div>
          <div class="info-row">
            <span class="label">Time</span>
            <span class="value"><?= htmlspecialchars($appointment['appointment_time'] ?? 'N/A') ?></span>
          </div>
          <div class="info-row">
            <span class="label">Status</span>
            <span class="value"><?= htmlspecialchars(ucfirst($appointment['status'] ?? 'N/A')) ?></span>
          </div>
          <div class="info-row">
            <span class="label">Notes</span>
            <span class="value"><?= htmlspecialchars($appointment['notes'] ?? 'No notes') ?></span>
          </div>
        </div>
      </div>

      <!-- Team Assignment Card -->
      <div class="info-card">
        <div class="card-header">
          <i class="fas fa-users"></i>
          <h3>Team Assignment</h3>
        </div>
        <div class="card-body">
          <div class="sub-section">
            <h4><i class="fas fa-user-cog"></i> Mechanic</h4>
            <div class="info-row">
              <span class="label">Name</span>
              <span class="value"><?= htmlspecialchars(($mechanic['first_name'] ?? '') . ' ' . ($mechanic['last_name'] ?? '')) ?: 'Not Assigned' ?></span>
            </div>
            <div class="info-row">
              <span class="label">Code</span>
              <span class="value"><?= htmlspecialchars($mechanic['mechanic_code'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
              <span class="label">Specialization</span>
              <span class="value"><?= htmlspecialchars($mechanic['specialization'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
              <span class="label">Experience</span>
              <span class="value"><?= htmlspecialchars($mechanic['experience_years'] ?? 0) ?> years</span>
            </div>
            <div class="info-row">
              <span class="label">Contact</span>
              <span class="value"><?= htmlspecialchars($mechanic['email'] ?? 'N/A') ?><br><?= htmlspecialchars($mechanic['phone'] ?? '') ?></span>
            </div>
          </div>
          
          <div class="sub-section">
            <h4><i class="fas fa-clipboard-check"></i> Supervisor</h4>
            <div class="info-row">
              <span class="label">Name</span>
              <span class="value"><?= htmlspecialchars(($supervisor['first_name'] ?? '') . ' ' . ($supervisor['last_name'] ?? '')) ?: 'Not Assigned' ?></span>
            </div>
            <div class="info-row">
              <span class="label">Code</span>
              <span class="value"><?= htmlspecialchars($supervisor['supervisor_code'] ?? 'N/A') ?></span>
            </div>
            <div class="info-row">
              <span class="label">Contact</span>
              <span class="value"><?= htmlspecialchars($supervisor['email'] ?? 'N/A') ?></span>
            </div>
          </div>
        </div>
      </div>

      <!-- Work Order Progress Card -->
      <div class="info-card">
        <div class="card-header">
          <i class="fas fa-tasks"></i>
          <h3>Work Progress</h3>
        </div>
        <div class="card-body">
          <div class="info-row">
            <span class="label">Job Start</span>
            <span class="value"><?= $workOrder['job_start_time'] ? date('M d, Y h:i A', strtotime($workOrder['job_start_time'])) : 'Not Started' ?></span>
          </div>
          <div class="info-row">
            <span class="label">Started At</span>
            <span class="value"><?= $workOrder['started_at'] ? date('M d, Y h:i A', strtotime($workOrder['started_at'])) : 'Pending' ?></span>
          </div>
          <div class="info-row">
            <span class="label">Completed At</span>
            <span class="value"><?= $workOrder['completed_at'] ? date('M d, Y h:i A', strtotime($workOrder['completed_at'])) : '—' ?></span>
          </div>
          <div class="info-row">
            <span class="label">Total Cost</span>
            <span class="value highlight">Rs. <?= number_format($workOrder['total_cost'] ?? 0, 2) ?></span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Complaints Section -->
  <?php if (!empty($complaints)): ?>
  <div class="info-card full-width">
    <div class="card-header">
      <i class="fas fa-exclamation-triangle"></i>
      <h3>Customer Complaints</h3>
    </div>
    <div class="card-body">
      <?php foreach ($complaints as $complaint): ?>
      <div class="complaint-item">
        <div class="complaint-header">
          <span class="complaint-subject"><?= htmlspecialchars($complaint['subject']) ?></span>
          <span class="priority-badge priority-<?= strtolower($complaint['priority'] ?? 'medium') ?>">
            <?= htmlspecialchars($complaint['priority'] ?? 'Medium') ?>
          </span>
        </div>
        <p class="complaint-desc"><?= htmlspecialchars($complaint['description']) ?></p>
        <div class="complaint-meta">
          <span><i class="far fa-clock"></i> <?= date('M d, Y', strtotime($complaint['complaint_created_at'])) ?></span>
          <?php if ($complaint['assigned_first_name']): ?>
          <span><i class="far fa-user"></i> Assigned to: <?= htmlspecialchars($complaint['assigned_first_name'] . ' ' . $complaint['assigned_last_name']) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- Final Report Section -->
  <?php if (!empty($report)): ?>
  <div class="info-card full-width">
    <div class="card-header">
      <i class="fas fa-file-alt"></i>
      <h3>Final Report</h3>
    </div>
    <div class="card-body">
      <div class="report-section">
        <h4>Service Summary</h4>
        <p><?= nl2br(htmlspecialchars($workOrder['service_summary'] ?? 'No summary provided')) ?></p>
      </div>
      <div class="report-section">
        <h4>Report Details</h4>
        <p><?= nl2br(htmlspecialchars($report['report_details'] ?? 'No details')) ?></p>
      </div>
      <div class="report-section">
        <h4>Recommendations</h4>
        <p><?= nl2br(htmlspecialchars($report['recommendations'] ?? 'No recommendations')) ?></p>
      </div>
      <div class="report-meta">
        <span><i class="far fa-calendar-check"></i> Reported on: <?= date('F d, Y h:i A', strtotime($report['created_at'])) ?></span>
      </div>
    </div>
  </div>
  <?php endif; ?>
</div>
  </div>
</body>
</html>