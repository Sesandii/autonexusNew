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

      <?php if (!empty($flash)): ?>
        <div class="flash-message"><?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <?php if (empty($appointments)): ?>
        <div class="empty-state">
          <i class="fa-solid fa-circle-check fa-3x" style="color:#aaa;margin-bottom:.75rem;"></i>
          <p>You have no completed services waiting for a review.</p>
          <p>Once a service is marked complete, it will appear here.</p>
        </div>
      <?php else: ?>
      <form class="form-container" method="POST" action="<?= $base ?>/customer/rate-service">
        <!-- Appointment selector -->
        <div class="form-group full-width">
          <label for="appointment">Select Completed Appointment</label>
          <select id="appointment" name="appointment_id" required>
            <option value="">-- Choose an appointment --</option>
            <?php foreach ($appointments as $a): ?>
              <option value="<?= htmlspecialchars((string)$a['appointment_id']) ?>"
                      data-vehicle="<?= htmlspecialchars($a['license_plate'] ?? '') ?>"
                      data-model="<?= htmlspecialchars($a['vehicle_model'] ?? '') ?>"
                      data-date="<?= htmlspecialchars($a['service_date'] ?? '') ?>">
                <?php
                  $dateLabel = !empty($a['service_date']) ? date('d M Y', strtotime($a['service_date'])) : '—';
                ?>
                <?= htmlspecialchars(($a['vehicle_model'] ?? 'Vehicle') . ' — ' . ($a['service_name'] ?? 'Service') . ' — ' . $dateLabel) ?>
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
