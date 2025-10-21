<?php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title ?? 'Rate Your Service') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/rate-service.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <main class="main-content">
      <h1 class="page-title">⭐ We Value Your Feedback!</h1>

      <form class="form-container" method="POST" action="<?= $base ?>/customer/rate-service">
        <div class="form-group full-width">
          <label for="appointment">Select Completed Appointment:</label>
          <select id="appointment" name="appointment_id" required>
            <option value="">-- Choose an Appointment --</option>
            <?php foreach ($appointments as $a): ?>
              <option value="<?= htmlspecialchars($a['appointment_id']) ?>">
                <?= htmlspecialchars($a['vehicle_model'] . ' - ' . $a['service_name'] . ' - ' . date('d M Y', strtotime($a['service_date']))) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label for="vehicleNumber">Vehicle Number:</label>
          <input type="text" id="vehicleNumber" readonly />
        </div>

        <div class="form-group">
          <label for="brandModel">Brand & Model:</label>
          <input type="text" id="brandModel" readonly />
        </div>

        <div class="form-group">
          <label for="serviceDate">Service Date:</label>
          <input type="text" id="serviceDate" readonly />
        </div>

        <div class="form-group full-width">
          <label>Rate Your Service:</label>
          <div class="stars" id="ratingStars"></div>
          <input type="hidden" name="rating" id="ratingInput" value="0">
        </div>

        <div class="form-group full-width">
          <label for="feedback">Your Feedback:</label>
          <textarea id="feedback" name="feedback" placeholder="Write your comments here..."></textarea>
        </div>

        <div class="form-group full-width">
          <button type="submit" class="submit-btn">Submit Review</button>
        </div>
      </form>
    </main>
  </div>

  <script src="<?= $base ?>/public/assets/js/customer/rate-service.js"></script>
</body>
</html>
