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
  <script src="<?= $base ?>/public/assets/js/customer/rate-service.js"></script>
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <main class="main-content">

      <header class="page-header">
        <h1 class="page-title">Rate Your Service</h1>
        <p class="page-subtitle">
          Tell us how we did so we can keep improving every visit.
        </p>
      </header>

      <?php if (empty($appointments)): ?>
        <!-- Empty state: No services available to rate -->
        <div class="empty-state">
          <div class="empty-state-icon">
            <i class="fa-regular fa-calendar-check"></i>
          </div>
          <h2 class="empty-state-title">No Services Available to Rate</h2>
          <p class="empty-state-message">
            You don't have any completed services to rate at this time.<br>
            Once you've had a service completed, you'll be able to share your feedback here.
          </p>
          <a href="<?= $base ?>/customer/appointments" class="empty-state-btn">
            <i class="fa-solid fa-calendar-plus"></i>
            View Your Appointments
          </a>
        </div>
      <?php else: ?>
        <!-- Rating form: Shown when services are available to rate -->
        <form class="form-container" method="POST" action="<?= $base ?>/customer/rate-service">
          <!-- Appointment selector -->
          <div class="form-group full-width">
            <label for="appointment">Select Completed Appointment</label>
            <select id="appointment" name="appointment_id" required>
              <option value="">-- Choose an appointment --</option>
              <?php foreach ($appointments as $a): ?>
                <option value="<?= htmlspecialchars($a['appointment_id']) ?>">
                  <?= htmlspecialchars($a['vehicle_model'] . ' - ' . $a['service_name'] . ' - ' . date('d M Y', strtotime($a['service_date']))) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <!-- Auto-filled info -->
          <div class="form-group">
            <label for="vehicleNumber">Vehicle Number</label>
            <input type="text" id="vehicleNumber" readonly />
          </div>

          <div class="form-group">
            <label for="brandModel">Brand &amp; Model</label>
            <input type="text" id="brandModel" readonly />
          </div>

          <div class="form-group">
            <label for="serviceDate">Service Date</label>
            <input type="text" id="serviceDate" readonly />
          </div>

          <!-- Rating -->
          <div class="form-group full-width">
            <label>Rate Your Service</label>
            <div class="stars-wrapper">
              <div class="stars" id="ratingStars"></div>
              <span class="rating-hint">Tap a star to set your rating (1–5).</span>
            </div>
            <input type="hidden" name="rating" id="ratingInput" value="0">
          </div>

          <!-- Feedback -->
          <div class="form-group full-width">
            <label for="feedback">Your Feedback (optional)</label>
            <textarea id="feedback" name="feedback" placeholder="Share anything that stood out, good or bad..."></textarea>
          </div>

          <!-- Submit -->
          <div class="form-group full-width actions-row">
            <button type="submit" class="submit-btn">
              <i class="fa-regular fa-paper-plane"></i>
              Submit Review
            </button>
          </div>
        </form>
      <?php endif; ?>
    </main>
  </div>

  
</body>
</html>
