<?php
// complainDetailsReceptionist.php
// Receives $complaint array from ComplaintController::show($id)
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complaint Details - AutoNexus</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/r_css/complaintsReceptionist.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <style>/** {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
  font-family: 'Inter', sans-serif;
}



.sidebar .logo img {
  width: 80%;      
  max-width: 200px; 
  height: auto;    
  display: block;
  margin: 0 auto;  
}

.sidebar {
  position: fixed;
  top: 0;        /* stick to very top */
 /* left: 0;
  width: 280px;
  background: #000;
  color: #fff;
  padding: 0px;
  min-height: 100vh;
}

.sidebar .logo {
  text-align: center;
  margin-bottom: 30px;
}

.sidebar .logo h2 {
  color: #ffffff;
  margin: 5px 0 0;
}

.sidebar .logo p {
  font-size: 12px;
  letter-spacing: 1px;
}

.sidebar .menu {
  list-style: none;
  margin-top: 20px;
}

.sidebar .menu li {
  padding: 20px 40px;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 20px;
  color: #ccc;
  border-radius: 0px;
}

.sidebar .menu li.active,
.sidebar .menu li:hover {
  background: #2a2d34;
}

.sidebar .menu li a {
  color: inherit;        /* matches li text color */
 /* text-decoration: none; /* removes underline */
 /* display: flex;
  align-items: center;
  gap: 10px;
  width: 100%;
}


.main {
  flex: 1;
  padding: 30px 30px 30px 30px; /* top, right, bottom, left */
 /* display: flex;
  flex-direction: column;
  overflow-y: auto;
  margin-left: 280px;
}*/

.header-bar {
  display: flex;
  justify-content: space-between; /* pushes h1 left, button right */
  align-items: center;
  margin-bottom: 20px;
}

/* Header title styling (optional) */
.header-bar h1 {
  font-size: 24px;
  font-weight: 600;
  margin: 0;
}

/* Create Invoice button styling */
.create-btn {
  background-color: red;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.create-btn:hover {
  background-color: red;
}


.container {
  background: #fff;
  border-radius: 8px;
  padding: 40px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
  margin: 15px auto;
  max-width: 1000px;
  width: 100%;
  box-sizing: border-box;
}


h2 {
  margin-bottom: 20px;
}

.info-cards {
  display: flex;
  gap: 30px;
  margin-bottom: 30px;
}

.card {
  flex: 1;
  background-color: #f3f4f7;
  padding: 20px;
  border-radius: 8px;
}

.card h3 {
  margin-top: 0;
}

.complaint {
  background: #fefefe;
  border-left: 4px solid #ddd;
  padding: 15px;
  margin-bottom: 20px;
}

.tags {
  display: flex;
  gap: 20px;
  align-items: center;
  margin-top: 10px;
}

select {
  padding: 5px;
}

.notes {
  margin-bottom: 25px;
}


.timestamp {
  font-size: 0.85em;
  color: #777;
}

.create-btn {
  background-color: red;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: background-color 0.2s ease;
}

.create-btn:hover {
  background-color: red; /* stays red on hover */
}
</style>
</head>
<body>
  <!-- Sidebar -->
  <div class="sidebar">
    <div class="logo">
      <img src="<?= BASE_URL ?>/public/assets/img/Auto.png" alt="AutoNexus Logo" width="240">
      <h2>AUTONEXUS</h2>
    </div>
    <ul class="menu">
      <li><a href="<?= BASE_URL ?>/receptionist/dashboard">Dashboard</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/appointments">Appointments</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/service">Service & Packages</a></li>
      <li class="active"><a href="<?= BASE_URL ?>/receptionist/complaints">Complaints</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/billing">Billing & Payments</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/customers">Customer Profiles</a></li>
      <li><a href="<?= BASE_URL ?>/receptionist/team-schedule">Team Schedule</a></li>
      <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout">Sign Out</a></li>
    </ul>
  </div>

  <!-- Main content -->
  <div class="main">




    <div class="container">
      <h2><?= htmlspecialchars($complaint['description']) ?></h2>

      <!-- Customer & Vehicle Info -->
      <div class="info-cards">
        <div class="card">
          <h3>👤 Customer Information</h3>
          <p><strong>Name:</strong> <?= htmlspecialchars($complaint['customer_name']) ?></p>
          <p><strong>Phone:</strong> <?= htmlspecialchars($complaint['phone']) ?></p>
          <p><strong>Email:</strong> <?= htmlspecialchars($complaint['email']) ?></p>
        </div>

        <div class="card">
          <h3>🚗 Vehicle Information</h3>
          <p><strong>Vehicle:</strong> <?= htmlspecialchars($complaint['vehicle']) ?></p>
          <p><strong>License:</strong> <?= htmlspecialchars($complaint['vehicle_number']) ?></p><br/><br/>
          <b><a href="<?= BASE_URL ?>/receptionist/complaints/history/<?= urlencode($complaint['customer_name']) ?>">View Customer History</a></b>
        </div>
      </div>

      <!-- Complaint Details -->
      <div class="complaint">
        <p class="date">Submitted on <strong><?= date('M d, Y', strtotime($complaint['complaint_date'])) ?></strong></p>
        <p><?= nl2br(htmlspecialchars($complaint['description'])) ?></p>

        <div class="tags">
          <p><strong>Priority:</strong> <?= htmlspecialchars($complaint['priority']) ?></p>
          <p><strong>Status:</strong> <?= htmlspecialchars($complaint['status']) ?></p>
          <p><strong>Assigned to:</strong> <?= htmlspecialchars($complaint['assigned_to']) ?></p>
        </div>
      </div>

      <!-- Update Button -->
      <!-- Update Button -->
<div class="update-btn-container">
  <button class="create-btn" 
          onclick="window.location.href='<?= BASE_URL ?>/receptionist/complaints/edit/<?= $complaint['complaint_id'] ?>'">
    Update Complaint
  </button>
</div>

      <!-- Notes & Activity -->
      <div class="notes">
        <h4>Notes & Activity</h4>
        <?php if(!empty($complaint['notes'] ?? [])): ?>
          <?php foreach($complaint['notes'] as $note): ?>
            <div class="note">
              <p><strong><?= htmlspecialchars($note['author']) ?></strong></p>
              <p><?= nl2br(htmlspecialchars($note['content'])) ?></p>
              <p class="timestamp"><?= date('M d, Y \a\t h:i A', strtotime($note['created_at'])) ?></p>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No activity recorded.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>
