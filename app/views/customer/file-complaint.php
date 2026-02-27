<?php
/**
 * file-complaint.php
 * 
 * Customer Complaint Form View
 * 
 * This page allows customers to file complaints about completed appointments.
 * 
 * How it works:
 * 1. Customer selects a completed appointment from dropdown
 * 2. Appointment details auto-fill (vehicle, service, date) using JavaScript
 * 3. Customer writes their complaint in the textarea
 * 4. Form submits via POST to /customer/complaints/submit
 * 
 * Variables available in this view:
 * - $title: Page title (passed from controller)
 * - $appointments: Array of completed appointments (passed from controller)
 */

// Get the base URL (removes trailing slash for consistency)
$base = rtrim(BASE_URL, '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($title ?? 'File a Complaint') ?> - AutoNexus</title>

  <!-- CSS Files -->
  <!-- complaint.css contains styles specific to this complaint form -->
  <link rel="stylesheet" href="<?= $base ?>/public/css/complaint.css" />
  <!-- sidebar.css styles the navigation sidebar on the left -->
  <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
  <!-- Font Awesome provides icons (e.g., the warning icon) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <?php 
  /**
   * Include the customer sidebar navigation
   * APP_ROOT is a constant that points to the app/ directory
   * This sidebar appears on all customer pages for consistent navigation
   */
  include APP_ROOT . '/views/layouts/customer-sidebar.php'; 
  ?>

  <div class="container">
    <main class="main-content">

      <!-- Page Header: Title and description -->
      <header class="page-header">
        <h1 class="page-title">File a Complaint</h1>
        <p class="page-subtitle">
          We value your feedback. Let us know about any issues with your service.
        </p>
      </header>

      <?php if (isset($_SESSION['flash'])): ?>
        <!-- Flash message: Shows success/error messages after form submission -->
        <div class="flash-message">
          <?= htmlspecialchars($_SESSION['flash']) ?>
          <?php unset($_SESSION['flash']); // Clear message after displaying ?>
        </div>
      <?php endif; ?>

      <!-- 
        Complaint Form
        method="POST" - Sends data securely (not visible in URL)
        action - Where form data is sent when submitted
      -->
      <form class="form-container" method="POST" action="<?= $base ?>/customer/complaints/submit">
        
        <!-- Appointment Selector Dropdown -->
        <div class="form-group full-width">
          <label for="appointment">Select Appointment to Complain About</label>
          <select id="appointment" name="appointment_id" required>
            <option value="">-- Choose a completed appointment --</option>
            <?php 
            /**
             * Loop through all appointments and create dropdown options
             * Each appointment shows: Vehicle Model - Service Name - Date
             */
            foreach ($appointments as $a): 
            ?>
              <option 
                value="<?= htmlspecialchars((string)$a['appointment_id']) ?>"
                data-vehicle="<?= htmlspecialchars($a['license_plate'] ?? '') ?>"
                data-model="<?= htmlspecialchars(($a['make'] ?? '') . ' ' . ($a['model'] ?? '')) ?>"
                data-service="<?= htmlspecialchars($a['service_name'] ?? '') ?>"
                data-date="<?= htmlspecialchars($a['appointment_date'] ?? '') ?>"
                data-branch="<?= htmlspecialchars($a['branch_name'] ?? '') ?>"
              >
                <?= htmlspecialchars(
                  ($a['model'] ?? 'Unknown') . ' - ' . 
                  ($a['service_name'] ?? 'Unknown') . ' - ' . 
                  date('d M Y', strtotime($a['appointment_date']))
                ) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <span class="field-hint">Select the appointment you want to file a complaint about.</span>
        </div>

        <!-- Auto-filled Appointment Details (readonly - cannot be edited) -->
        <div class="form-group">
          <label for="vehicleNumber">Vehicle Number</label>
          <!-- readonly means user cannot type in this field -->
          <input type="text" id="vehicleNumber" readonly />
        </div>

        <div class="form-group">
          <label for="brandModel">Brand &amp; Model</label>
          <input type="text" id="brandModel" readonly />
        </div>

        <div class="form-group">
          <label for="serviceType">Service Type</label>
          <input type="text" id="serviceType" readonly />
        </div>

        <div class="form-group">
          <label for="serviceDate">Service Date</label>
          <input type="text" id="serviceDate" readonly />
        </div>

        <div class="form-group">
          <label for="branchName">Branch</label>
          <input type="text" id="branchName" readonly />
        </div>

        <!-- Complaint Description -->
        <div class="form-group full-width">
          <label for="description">Describe Your Complaint</label>
          <!-- 
            textarea - Multi-line text input for longer descriptions
            required - Browser won't submit form if this is empty
            rows="6" - Shows 6 lines of text by default
          -->
          <textarea 
            id="description" 
            name="description" 
            rows="6"
            required
            placeholder="Please describe the issue in detail. What went wrong? When did it happen? Any other relevant information..."
          ></textarea>
          <span class="field-hint">Be as specific as possible to help us resolve your issue quickly.</span>
        </div>

        <!-- Submit Button -->
        <div class="form-group full-width actions-row">
          <button type="submit" class="submit-btn">
            <i class="fa-solid fa-paper-plane"></i>
            Submit Complaint
          </button>
        </div>
      </form>

    </main>
  </div>

  <!-- JavaScript for auto-filling appointment details -->
  <script>
    /**
     * This script makes the form interactive
     * When user selects an appointment, it automatically fills in the details
     */
    
    // Wait for page to fully load before running JavaScript
    document.addEventListener('DOMContentLoaded', function() {
      // Get references to form elements
      const appointmentSelect = document.getElementById('appointment');
      const vehicleNumber = document.getElementById('vehicleNumber');
      const brandModel = document.getElementById('brandModel');
      const serviceType = document.getElementById('serviceType');
      const serviceDate = document.getElementById('serviceDate');
      const branchName = document.getElementById('branchName');

      /**
       * Event listener: Runs when user changes the dropdown selection
       * 'change' event fires when a new option is selected
       */
      appointmentSelect.addEventListener('change', function() {
        // Get the selected option element
        const selectedOption = this.options[this.selectedIndex];
        
        // If user selected the placeholder "-- Choose..." option, clear all fields
        if (!this.value) {
          vehicleNumber.value = '';
          brandModel.value = '';
          serviceType.value = '';
          serviceDate.value = '';
          branchName.value = '';
          return;
        }

        /**
         * Fill in the readonly fields with data from the selected option
         * getAttribute() gets the value of HTML data attributes
         * (the data-vehicle, data-model, etc. we set in PHP above)
         */
        vehicleNumber.value = selectedOption.getAttribute('data-vehicle') || '';
        brandModel.value = selectedOption.getAttribute('data-model') || '';
        serviceType.value = selectedOption.getAttribute('data-service') || '';
        branchName.value = selectedOption.getAttribute('data-branch') || '';
        
        // Format the date nicely (e.g., "25 Jan 2024")
        const dateStr = selectedOption.getAttribute('data-date');
        if (dateStr) {
          const date = new Date(dateStr);
          serviceDate.value = date.toLocaleDateString('en-GB', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric' 
          });
        }
      });
    });
  </script>

</body>
</html>
