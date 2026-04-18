<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaints Management</title>  
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/complaintsReceptionist.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
  <!-- Sidebar -->
  <?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

  <!-- Main content -->
  <div class="main">
    <header>
      <h1>Complaints Management</h1>
      <div class="filters">
        <button class="add-btn" id="openModal" onclick="window.location.href='<?= BASE_URL ?>/receptionist/complaints/new'">
          + New Complaint
        </button>
      <form method="GET" action="<?= BASE_URL ?>/receptionist/complaints">

  <select name="status" onchange="this.form.submit()">
    <option value="">All Statuses</option>

    <option value="open" 
      <?= (($_GET['status'] ?? '') == 'open') ? 'selected' : '' ?>>
      Open
    </option>

    <option value="in_progress" 
      <?= (($_GET['status'] ?? '') == 'in_progress') ? 'selected' : '' ?>>
      In Progress
    </option>

    <option value="resolved" 
      <?= (($_GET['status'] ?? '') == 'resolved') ? 'selected' : '' ?>>
      Resolved
    </option>

    <option value="closed" 
      <?= (($_GET['status'] ?? '') == 'closed') ? 'selected' : '' ?>>
      Canceled
    </option>

  </select>

</form>
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
               data-url="<?= BASE_URL ?>/receptionist/complaints/<?= $complaint['complaint_id'] ?>" 
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
  <a href="<?= BASE_URL ?>/receptionist/complaints/delete/<?= $complaint['complaint_id'] ?>" 
   onclick="return confirm('Are you sure you want to delete this complaint?');">
   🗑️
</a>

</div>

          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No complaints found.</p>
      <?php endif; ?>
    </div>
  </div>
<script>
  const BASE_URL = "<?= BASE_URL ?>";
</script>
  <script src="<?= BASE_URL ?>/public/assets/js/receptionist/complaintsReceptionist.js"></script>
</body>
</html>
