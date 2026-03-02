<?php
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title ?? 'Rate Your Service') ?> - AutoNexus</title>

  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/rate-service.css?v=<?= time() ?>" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="<?= $base ?>/public/assets/js/customer/rate-service.js?v=<?= time() ?>" defer></script>
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <main class="main-content">

      <header class="page-header">
        <div class="header-left">
          <h1 class="page-title">
            <i class="fa-solid fa-star"></i>
            Rate Your Service
          </h1>
          <p class="page-subtitle">
            Tell us how we did so we can keep improving every visit.
          </p>
        </div>
      </header>

      <!-- Flash Messages -->
      <?php if (isset($_SESSION['flash'])): ?>
        <div class="flash-message">
          <i class="fa-solid fa-circle-check"></i>
          <?= htmlspecialchars($_SESSION['flash']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
      <?php endif; ?>

      <?php if (empty($appointments)): ?>
        <div class="empty-appointments">
          <i class="fa-regular fa-calendar-xmark"></i>
          <h3>No Services to Rate</h3>
          <p>You don't have any completed services without feedback yet.</p>
          <a href="<?= $base ?>/customer/service-history" class="btn-secondary">
            <i class="fa-solid fa-clock-rotate-left"></i>
            View Service History
          </a>
        </div>
      <?php else: ?>

      <form class="form-container" method="POST" action="<?= $base ?>/customer/rate-service" id="ratingForm">
        
        <!-- Step 1: Select Service -->
        <div class="form-section full-width">
          <h2 class="section-title">
            <span class="step-badge">1</span>
            Select Completed Service
          </h2>
          
          <div class="form-group">
            <label for="appointment">Choose a service to rate</label>
            <select id="appointment" name="appointment_id" required>
              <option value="">-- Select an appointment --</option>
              <?php foreach ($appointments as $a): ?>
                <option 
                  value="<?= htmlspecialchars($a['appointment_id']) ?>"
                  data-vehicle="<?= htmlspecialchars($a['vehicle_license_plate'] ?? 'N/A') ?>"
                  data-model="<?= htmlspecialchars(($a['vehicle_make'] ?? '') . ' ' . ($a['vehicle_model'] ?? '')) ?>"
                  data-service="<?= htmlspecialchars($a['service_name'] ?? 'N/A') ?>"
                  data-date="<?= htmlspecialchars($a['service_date'] ?? '') ?>"
                  data-time="<?= htmlspecialchars($a['service_time'] ?? '') ?>"
                >
                  <?= htmlspecialchars(
                    date('M d, Y', strtotime($a['service_date'])) . ' - ' . 
                    $a['service_name'] . ' - ' . 
                    $a['vehicle_license_plate']
                  ) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <!-- Service Details (auto-filled) -->
        <div class="form-section full-width" id="serviceDetails" style="display: none;">
          <h2 class="section-title">
            <span class="step-badge">2</span>
            Service Details
          </h2>
          
          <div class="details-grid">
            <div class="detail-box">
              <i class="fa-solid fa-car"></i>
              <div class="detail-content">
                <span class="detail-label">Vehicle</span>
                <span class="detail-value" id="vehicleDisplay">-</span>
              </div>
            </div>
            
            <div class="detail-box">
              <i class="fa-solid fa-tag"></i>
              <div class="detail-content">
                <span class="detail-label">License Plate</span>
                <span class="detail-value" id="licensePlateDisplay">-</span>
              </div>
            </div>
            
            <div class="detail-box">
              <i class="fa-solid fa-wrench"></i>
              <div class="detail-content">
                <span class="detail-label">Service Type</span>
                <span class="detail-value" id="serviceDisplay">-</span>
              </div>
            </div>
            
            <div class="detail-box">
              <i class="fa-regular fa-calendar"></i>
              <div class="detail-content">
                <span class="detail-label">Service Date</span>
                <span class="detail-value" id="dateDisplay">-</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Rating Section -->
        <div class="form-section full-width" id="ratingSection" style="display: none;">
          <h2 class="section-title">
            <span class="step-badge">3</span>
            Your Rating
          </h2>
          
          <div class="form-group">
            <label>How would you rate this service? <span class="required">*</span></label>
            <div class="stars-wrapper">
              <div class="stars" id="ratingStars"></div>
              <span class="rating-text" id="ratingText">Select a rating</span>
            </div>
            <input type="hidden" name="rating" id="ratingInput" value="0" required>
            <span class="validation-msg" id="ratingError"></span>
          </div>
        </div>

        <!-- Feedback Section -->
        <div class="form-section full-width" id="feedbackSection" style="display: none;">
          <h2 class="section-title">
            <span class="step-badge">4</span>
            Your Feedback
          </h2>
          
          <div class="form-group">
            <label for="feedback">Tell us more about your experience (optional)</label>
            <textarea 
              id="feedback" 
              name="feedback" 
              placeholder="What did you like? What could we improve? Any specific comments about the service, technician, or branch?"
              rows="6"
            ></textarea>
            <div class="char-counter">
              <span id="charCount">0</span> / 500 characters
            </div>
          </div>
        </div>

        <!-- Submit Button -->
        <div class="form-group full-width actions-row" id="submitSection" style="display: none;">
          <button type="button" class="btn-secondary" onclick="window.location.href='<?= $base ?>/customer/dashboard'">
            <i class="fa-solid fa-xmark"></i>
            Cancel
          </button>
          <button type="submit" class="submit-btn" id="submitBtn">
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
