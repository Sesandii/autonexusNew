<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaints Management</title>  
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/complaintsManager.css">
</head>

<body>
  <!-- Sidebar -->
  <?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

  <!-- Main content -->
  <div class="main">
    <header>
      <h1>Complaints Management</h1>
      <div class="filters">
        <input type="text" placeholder="Search complaints...">
        <select>
          <option>All Statuses</option>
          <option>Open</option>
          <option>In Progress</option>
          <option>Resolved</option>
          <option>Canceled</option>
        </select>
      </div>
    </header>

    <!-- Complaints List -->
    <div class="complaints-list">
      <?php if (!empty($complaints)): ?>
        <?php foreach ($complaints as $complaint): ?>
          <?php
            $statusClass = strtolower(str_replace(' ', '-', $complaint['status']));
            $title = htmlspecialchars(strlen($complaint['description']) > 50 ? substr($complaint['description'], 0, 50) . '...' : $complaint['description']);
            $description = htmlspecialchars($complaint['description']);
          ?>
          <div class="complaint <?= $statusClass ?>" 
               data-url="<?= BASE_URL ?>/manager/complaints/<?= $complaint['complaint_id'] ?>" 
               data-priority="<?= strtolower($complaint['priority']) ?>" 
               data-status="<?= strtolower($complaint['status']) ?>">

            <div class="complaint-info">
    <!-- Complaint title -->
            <h3>🚨 <?= htmlspecialchars($title) ?></h3>

            <!-- Complaint description -->
            <p><?= htmlspecialchars($description) ?></p>

            <!-- Customer name, vehicle, and date -->
            <?php
                $customerName = $complaint['customer_name'] ?? 'Unknown Customer';
                $vehicleName  = $complaint['vehicle'] ?? 'No Vehicle';
                $complaintDate = isset($complaint['complaint_date']) ? date('M d, Y', strtotime($complaint['complaint_date'])) : 'No Date';
                $assignedTo = $complaint['assigned_to'] ?? 'Unassigned';
            ?>
            <span class="meta">
                <?= htmlspecialchars($customerName) ?> · <?= htmlspecialchars($vehicleName) ?> · <?= $complaintDate ?>
            </span>

            <!-- Assigned to -->
            <div class="status assigned">Assigned: <?= htmlspecialchars($assignedTo) ?></div>
        </div>

           
            <div class="badge <?= $statusClass ?>"><?= htmlspecialchars($complaint['status']) ?></div>

            <!-- Actions: Delete icon -->
            <div class="actions">
  

</div>

          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No complaints found.</p>
      <?php endif; ?>
    </div>
  </div>

  <script src="<?= BASE_URL ?>/public/assets/js/manager/complaintsManager.js"></script>
</body>
</html>
