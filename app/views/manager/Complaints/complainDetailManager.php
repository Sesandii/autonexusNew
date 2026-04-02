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
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/sidebar.css">
  <link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/manager/complainDetailManager.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>
<body>
  
<?php include APP_ROOT . '/views/layouts/manager-sidebar.php'; ?>

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
          <b><a href="<?= BASE_URL ?>/manager/complaints/history/<?= urlencode($complaint['customer_name']) ?>">View Customer History</a></b>
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
