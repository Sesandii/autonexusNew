<?php
// Base URL without trailing slash for consistency with other customer views
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title ?? 'File a Complaint') ?> - AutoNexus</title>
  
  <!-- 
    CSS Asset Path Explanation:
    - BASE_URL is defined in config.php (e.g., http://localhost/autonexus)
    - The path /public/assets/css/customer/complaint.css is served by Apache
    - .htaccess in root rewrites to public/ directory
    - This ensures CSS loads at: /autonexus/public/assets/css/customer/complaint.css
  -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/complaint.css" />
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>

  <div class="container">
    <main class="main-content">

      <header class="page-header">
        <h1 class="page-title">File a Complaint</h1>
        <p class="page-subtitle">
          Let us know about any issues with your service or experience at AutoNexus.
        </p>
      </header>

      <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert <?= strpos($_SESSION['flash'], 'success') !== false ? 'alert-success' : 'alert-error' ?>">
          <?= htmlspecialchars($_SESSION['flash']) ?>
        </div>
        <?php unset($_SESSION['flash']); ?>
      <?php endif; ?>

      <form class="form-container" method="POST" action="<?= $base ?>/customer/complaint">
        
        <!-- Vehicle Selection -->
        <div class="form-group full-width">
          <label for="vehicle">Select Vehicle <span class="required">*</span></label>
          <select id="vehicle" name="vehicle_id" required>
            <option value="">-- Choose a vehicle --</option>
            <?php if (isset($vehicles) && is_array($vehicles)): ?>
              <?php foreach ($vehicles as $v): ?>
                <option value="<?= htmlspecialchars($v['vehicle_id'] ?? '') ?>" 
                        data-number="<?= htmlspecialchars($v['vehicle_number'] ?? '') ?>"
                        data-model="<?= htmlspecialchars($v['brand'] . ' ' . $v['model'] ?? '') ?>">
                  <?= htmlspecialchars(($v['brand'] ?? '') . ' ' . ($v['model'] ?? '') . ' - ' . ($v['vehicle_number'] ?? '')) ?>
                </option>
              <?php endforeach; ?>
            <?php endif; ?>
          </select>
        </div>

        <!-- Auto-filled Vehicle Details -->
        <div class="form-group">
          <label for="vehicleNumber">Vehicle Number</label>
          <input type="text" id="vehicleNumber" readonly placeholder="Select a vehicle above" />
        </div>

        <div class="form-group">
          <label for="vehicleModel">Brand &amp; Model</label>
          <input type="text" id="vehicleModel" readonly placeholder="Select a vehicle above" />
        </div>

        <!-- Complaint Type -->
        <div class="form-group full-width">
          <label for="complaintType">Complaint Type <span class="required">*</span></label>
          <select id="complaintType" name="complaint_type" required>
            <option value="">-- Select type --</option>
            <option value="service_quality">Service Quality</option>
            <option value="staff_behavior">Staff Behavior</option>
            <option value="billing">Billing Issue</option>
            <option value="facility">Facility/Cleanliness</option>
            <option value="delay">Service Delay</option>
            <option value="parts">Parts/Quality Issue</option>
            <option value="other">Other</option>
          </select>
        </div>

        <!-- Priority -->
        <div class="form-group">
          <label for="priority">Priority <span class="required">*</span></label>
          <select id="priority" name="priority" required>
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
            <option value="urgent">Urgent</option>
          </select>
        </div>

        <!-- Date of Incident -->
        <div class="form-group">
          <label for="incidentDate">Date of Incident <span class="required">*</span></label>
          <input type="date" id="incidentDate" name="incident_date" required 
                 max="<?= date('Y-m-d') ?>" />
        </div>

        <!-- Complaint Description -->
        <div class="form-group full-width">
          <label for="description">Complaint Details <span class="required">*</span></label>
          <textarea id="description" name="description" required 
                    placeholder="Please describe your complaint in detail..."
                    minlength="20"></textarea>
          <span class="form-hint">Minimum 20 characters</span>
        </div>

        <!-- Contact Preference -->
        <div class="form-group full-width">
          <label for="contactMethod">Preferred Contact Method <span class="required">*</span></label>
          <select id="contactMethod" name="contact_method" required>
            <option value="email">Email</option>
            <option value="phone">Phone</option>
            <option value="both">Both Email & Phone</option>
          </select>
        </div>

        <!-- Submit Actions -->
        <div class="form-group full-width actions-row">
          <button type="button" class="cancel-btn" onclick="window.history.back()">
            <i class="fa-solid fa-xmark"></i>
            Cancel
          </button>
          <button type="submit" class="submit-btn">
            <i class="fa-regular fa-paper-plane"></i>
            Submit Complaint
          </button>
        </div>
      </form>
    </main>
  </div>

  <script src="<?= $base ?>/public/assets/js/customer/complaint.js"></script>
</body>
</html>
