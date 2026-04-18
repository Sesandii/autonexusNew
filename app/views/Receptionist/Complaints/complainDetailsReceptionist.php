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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/complainDetailsReceptionist.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  
<?php include APP_ROOT . '/views/layouts/receptionist-sidebar.php'; ?>

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
          <b><a href="<?= BASE_URL ?>/manager/complaints/history/<?= $complaint['customer_id'] ?>">View Customer History</a></b>
        </div>
      </div>

    <div class="info-cards">
<?php if (!empty($complaint['appointment_id'])): ?>
  <div class="card">
    <h3>📅 Appointment Details</h3>

    <p><strong>Date:</strong> 
      <?= htmlspecialchars($complaint['appointment_date']) ?>
    </p>

    <p><strong>Time:</strong> 
      <?= htmlspecialchars($complaint['appointment_time']) ?>
    </p>

    <p><strong>Status:</strong> 
      <?= htmlspecialchars($complaint['appointment_status']) ?>
    </p>

    <p><strong>Notes:</strong><br>
      <?= nl2br(htmlspecialchars($complaint['notes'] ?? 'N/A')) ?>
    </p>
  </div>
<?php else: ?>
  <div class="card">
    <h3>📅 Appointment Details</h3>
    <p>No related appointment</p>
  </div>
<?php endif; ?>

<?php if (!empty($complaint['work_order_id'])): ?>
  <div class="card">
    <h3>🛠 Work Order Details</h3>

    <p><strong>Work Order ID:</strong> 
      <?= htmlspecialchars($complaint['work_order_id']) ?>
    </p>

    <p><strong>Status:</strong> 
      <?= htmlspecialchars($complaint['work_order_status']) ?>
    </p>

    <p><strong>Service Summary:</strong><br>
      <?= nl2br(htmlspecialchars($complaint['service_summary'] ?? 'N/A')) ?>
    </p>

    <p><strong>Total Cost:</strong> 
      Rs. <?= htmlspecialchars($complaint['total_cost'] ?? '0.00') ?>
    </p>

    <p><strong>Started At:</strong> 
      <?= htmlspecialchars($complaint['started_at'] ?? '-') ?>
    </p>

    <p><strong>Completed At:</strong> 
      <?= htmlspecialchars($complaint['completed_at'] ?? '-') ?>
    </p>
  </div>
<?php else: ?>
  <div class="card">
    <h3>🛠 Work Order Details</h3>
    <p>No work order available</p>
  </div>
<?php endif; ?>
</div>

      <div class="complaint">
       <p class="date">Submitted on <strong><?= date('M d, Y', strtotime($complaint['created_at'])) ?></strong></p> </br>
       
       <h3>Description & Activities</h3>
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

   
     
    </div>
  </div>
</body>
</html>
