<?php
require_once __DIR__ . '/../../../../config/config.php'; // ✅ path to your DB config file

// Fetch complaints
$query = "SELECT * FROM complaints ORDER BY created_at DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Customer Complaints</title>
  <link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/style-complaints.css">
</head>
<body>
  <!-- SIDEBAR -->
  <div class="sidebar">
    <h2 class="logo">AUTONEXUS</h2>
    <ul>
      <li>Dashboard</li>
      <li>Jobs</li>
      <li>Assigned Jobs</li>
      <li>Vehicle History</li>
      <li class="active">Complaints</li>
      <li>Feedbacks</li>
      <li>Report</li>
    </ul>
  </div>

  <!-- MAIN CONTENT -->
  <div class="main-content">
    <div class="header">
      <input type="text" id="search" placeholder="Search...">
      <div class="filters">
        <select id="statusFilter">
          <option value="All">All Statuses</option>
          <option value="Open">Open</option>
          <option value="In Progress">In Progress</option>
          <option value="Resolved">Resolved</option>
        </select>

        <select id="priorityFilter">
          <option value="All">All Priorities</option>
          <option value="High">High</option>
          <option value="Medium">Medium</option>
          <option value="Low">Low</option>
        </select>
      </div>
      <div class="user-info">John Doe</div>
    </div>

    <h2 class="title">Customer Complaints</h2>

    <div id="complaintsList">
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="complaint-card" 
             data-status="<?= htmlspecialchars($row['status']) ?>" 
             data-priority="<?= htmlspecialchars($row['priority']) ?>">

          <div class="complaint-header">
            <strong><?= htmlspecialchars($row['description']) ?></strong>
            <span class="status <?= strtolower(str_replace(' ', '-', $row['status'])) ?>">
              <?= htmlspecialchars($row['status']) ?>
            </span>
          </div>

          <p>
            <?= htmlspecialchars($row['customer_name']) ?> reported an issue about 
            <?= htmlspecialchars($row['description']) ?>.
          </p>
          <small><?= htmlspecialchars($row['vehicle']) ?> — <?= htmlspecialchars($row['vehicle_number']) ?></small>

          <div class="meta">
            <span>Assigned to: <?= htmlspecialchars($row['assigned_to'] ?: 'Unassigned') ?></span>
            <span class="date"><?= date('M d, Y', strtotime($row['complaint_date'])) ?></span>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  </div>

  <script src="/autonexus/public/assets/js/supervisor/script-complaints.js"></script>
</body>
</html>
