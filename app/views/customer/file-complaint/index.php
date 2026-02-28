<?php
/**
 * File: app/views/customer/file-complaint/index.php
 * Shows the File a Complaint UI. Must receive $appointments as array from controller.
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base URL - adjust if your BASE_URL constant is defined elsewhere
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/autonexus');
}
$base = rtrim(BASE_URL, '/');

// Check if user is logged in
$isLoggedIn = isset($_SESSION['customer_id']) || isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File a Complaint - AutoNexus</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/complaint.css">
    <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php 
// Include sidebar if APP_ROOT is defined
if (defined('APP_ROOT') && file_exists(APP_ROOT . '/views/layouts/customer-sidebar.php')) {
    include APP_ROOT . '/views/layouts/customer-sidebar.php'; 
}
?>

<div class="complaint-container">
    <h2>File a Complaint</h2>
    <p>We're sorry to hear about your experience. Please select the relevant appointment and tell us what went wrong.</p>
    
    <?php
    // Display success message
    if (isset($_SESSION['complaint_success'])) {
        echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['complaint_success']) . '</div>';
        unset($_SESSION['complaint_success']);
    }
    
    // Display error message
    if (isset($_SESSION['complaint_error'])) {
        echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['complaint_error']) . '</div>';
        unset($_SESSION['complaint_error']);
    }
    ?>
    
    <!-- Complaint Form -->
    <form id="complaintForm" action="<?= $base ?>/customer/file-complaint/submit" method="POST" autocomplete="off">
        <label for="appointment">Select Appointment</label>
        <select id="appointment" name="appointment_id" required>
            <option value="">Select an appointment…</option>
            <?php if (isset($appointments) && is_array($appointments) && count($appointments) > 0): ?>
                <?php foreach ($appointments as $a): ?>
                    <?php 
                        $service = htmlspecialchars($a['service_name'] ?? 'Unknown Service');
                        $date = htmlspecialchars($a['appointment_date'] ?? '');
                        $time = htmlspecialchars($a['appointment_time'] ?? '');
                        $vehicle = htmlspecialchars(($a['make'] ?? '') . ' ' . ($a['model'] ?? '') . ' (' . ($a['license_plate'] ?? '') . ')');
                        $status = htmlspecialchars($a['status'] ?? 'completed');
                    ?>
                    <option 
                        value="<?= (int)$a['appointment_id'] ?>"
                        data-service="<?= $service ?>"
                        data-date="<?= $date ?> <?= $time ?>"
                        data-vehicle="<?= $vehicle ?>"
                        data-status="<?= $status ?>"
                    ><?= $service ?> — <?= $date ?></option>
                <?php endforeach; ?>
            <?php else: ?>
                <option value="" disabled>No completed appointments available</option>
            <?php endif; ?>
        </select>

        <div class="appointment-details" id="appointmentDetails" style="display: none;">
            <div class="row">
                <div>
                    <span class="icon">🛠️</span>
                    <span class="label">Service Performed</span><br>
                    <span id="serviceName"></span>
                </div>
                <div>
                    <span class="icon">📅</span>
                    <span class="label">Date</span><br>
                    <span id="serviceDate"></span>
                </div>
            </div>
            <div class="row">
                <div>
                    <span class="icon">🚗</span>
                    <span class="label">Vehicle</span><br>
                    <span id="vehicle"></span>
                </div>
                <div>
                    <span id="appointmentStatus"></span>
                </div>
            </div>
        </div>

        <label for="complaintText">Describe Your Complaint</label>
        <textarea 
            id="complaintText" 
            name="complaint" 
            maxlength="1000" 
            rows="4" 
            required 
            placeholder="Please describe your complaint in detail..."
            oninput="document.getElementById('chars').textContent=this.value.length;"
        ></textarea>
        <div class="complaint-meta">
            <span id="chars">0</span> / 1000
        </div>
        <div class="complaint-hint">
            <span>ⓘ</span> Please be as specific as possible to help us resolve your issue quickly.
        </div>
        
        <div class="button-container">
            <button class="submit-btn" type="submit">
                <i class="fa-solid fa-paper-plane"></i>
                Submit Complaint
            </button>
        </div>
    </form>
    
    <div class="complaint-support-line">
        Need immediate assistance? Call our support line at <span class="phone">(555) 123-4567</span>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const appointmentSelect = document.getElementById('appointment');
    const appointmentDetails = document.getElementById('appointmentDetails');
    const form = document.getElementById('complaintForm');
    
    if (appointmentSelect) {
        appointmentSelect.addEventListener('change', function() {
            const sel = this.options[this.selectedIndex];
            
            if (this.value) {
                document.getElementById('serviceName').textContent = sel.getAttribute('data-service') || '';
                document.getElementById('serviceDate').textContent = sel.getAttribute('data-date') || '';
                document.getElementById('vehicle').textContent = sel.getAttribute('data-vehicle') || '';
                
                const status = sel.getAttribute('data-status');
                const statusEl = document.getElementById('appointmentStatus');
                if (status) {
                    const statusClass = status.toLowerCase().replace(/\s+/g, '-');
                    statusEl.innerHTML = `<span class="badge status-${statusClass}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`;
                } else {
                    statusEl.innerHTML = '';
                }
                
                appointmentDetails.style.display = 'block';
            } else {
                appointmentDetails.style.display = 'none';
            }
        });
    }
    
    // Form validation before submit
    if (form) {
        form.addEventListener('submit', function(e) {
            const appointmentId = document.getElementById('appointment').value;
            const complaint = document.getElementById('complaintText').value.trim();
            
            if (!appointmentId) {
                e.preventDefault();
                alert('Please select an appointment');
                return false;
            }
            
            if (complaint.length < 10) {
                e.preventDefault();
                alert('Please provide a more detailed complaint (at least 10 characters)');
                return false;
            }
            
            // Disable submit button to prevent double submission
            const submitBtn = form.querySelector('.submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';
            }
            
            return true;
        });
    }
});
</script>

</body>
</html>