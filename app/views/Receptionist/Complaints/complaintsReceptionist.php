<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaints Management</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/complaintsReceptionist.css">
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">
      <img src="<?= BASE_URL ?>/public/assets/img/Auto.png" alt="AutoNexus Logo">
      <h2>AUTONEXUS</h2>
      <p>VEHICLE SERVICE</p>
    </div>
    <ul class="menu">
      <li><a href="<?= BASE_URL ?>/receptionist/dashboard">Dashboard</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/appointments">Appointments</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/service">Service & Packages</a></li>
      <li class="active"><a href="<?= BASE_URL ?>/receptionist/complaints">Complaints</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/billing">Billing & Payments</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/customers">Customer Profiles</a></li>
      <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
    </ul>
  </div>

  <!-- Main content -->
  <div class="main">
    <header>
      <h1>Complaints Management</h1>
      <div class="filters">
        <button class="add-btn" id="openModal" onclick="window.location.href='<?= BASE_URL ?>/receptionist/complaints/new'">
          + New Complaint
        </button>
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
               data-url="<?= BASE_URL ?>/receptionist/complaints/<?= $complaint['complaint_id'] ?>" 
               data-priority="<?= strtolower($complaint['priority']) ?>" 
               data-status="<?= strtolower($complaint['status']) ?>">

            <div class="complaint-info">
              <h3>🚨 <?= $title ?></h3>
              <p><?= $description ?></p>
              <span class="meta">
                <?= htmlspecialchars($complaint['customer_name']) ?> · 
                <?= htmlspecialchars($complaint['vehicle']) ?> · 
                <?= date('M d, Y', strtotime($complaint['complaint_date'])) ?>
              </span>
            </div>

            <div class="status assigned">Assigned: <?= htmlspecialchars($complaint['assigned_to']) ?></div>
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

  <script src="<?= BASE_URL ?>/public/assets/r_js/complaintsReceptionist.js"></script>
</body>
</html>
