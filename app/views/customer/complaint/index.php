<?php
/**
 * Customer Complaint Form View
 * 
 * Asset URL Mapping Example:
 * - CSS files in public/assets/css/ are accessible via:
 *   1. <?= BASE_URL ?>/public/assets/css/... (explicit path)
 *   2. <?= BASE_URL ?>/assets/css/... (cleaner path)
 * 
 * Both patterns work due to .htaccess rewrite rules
 */

$base = rtrim(BASE_URL, '/');
$title = 'AutoNexus - Submit Complaint';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($title, ENT_QUOTES) ?></title>

  <!-- Example of correct asset linking -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/complaint.css">
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <main class="main-content">
    <div class="complaint-container">
      <h2><i class="fas fa-exclamation-circle"></i> Submit a Complaint</h2>
      
      <?php if (isset($_SESSION['complaint_success'])): ?>
        <div class="success-message">
          <?= htmlspecialchars($_SESSION['complaint_success'], ENT_QUOTES) ?>
          <?php unset($_SESSION['complaint_success']); ?>
        </div>
      <?php endif; ?>

      <form action="<?= $base ?>/customer/complaint" method="POST">
        <div class="form-group">
          <label for="subject">Subject <span class="required">*</span></label>
          <input type="text" id="subject" name="subject" required>
        </div>

        <div class="form-group">
          <label for="category">Category <span class="required">*</span></label>
          <select id="category" name="category" required>
            <option value="">Select a category</option>
            <option value="service_quality">Service Quality</option>
            <option value="billing">Billing Issue</option>
            <option value="staff_behavior">Staff Behavior</option>
            <option value="facility">Facility Issue</option>
            <option value="other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Description <span class="required">*</span></label>
          <textarea id="description" name="description" required 
            placeholder="Please provide details about your complaint..."></textarea>
        </div>

        <div class="form-group">
          <label for="appointment_id">Related Appointment (Optional)</label>
          <input type="text" id="appointment_id" name="appointment_id" 
            placeholder="Enter appointment ID if applicable">
        </div>

        <button type="submit" class="btn-submit">
          <i class="fas fa-paper-plane"></i> Submit Complaint
        </button>
      </form>
    </div>
  </main>

  <!-- JavaScript assets follow the same pattern -->
  <script src="<?= $base ?>/public/assets/js/customer-complaint.js" defer></script>

</body>
</html>
