<?php
$base = rtrim(BASE_URL, '/');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$sidebarCssVersion = @filemtime(dirname(APP_ROOT) . '/public/assets/css/customer/sidebar.css') ?: time();
function isActive($path, $current) { return strpos($current, $path) === 0 ? ' class="active"' : ''; }
?>
<link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/sidebar.css?v=<?= (int)$sidebarCssVersion ?>">
<aside class="sidebar">
  <div class="logo">
    <img src="<?= $base ?>/public/assets/img/logo.jpg" alt="AutoNexus Logo">
    <span class="brand-name">AutoNexus</span>
  </div>

  <ul class="menu">
    <!-- Registered Home -->
    <li><a<?= isActive('/customer/home', $currentPath) ?> href="<?= $base ?>/customer/home"><i class="fa fa-home"></i> Home</a></li>

    <!-- Dashboard -->
    <li><a<?= isActive('/customer/dashboard', $currentPath) ?> href="<?= $base ?>/customer/dashboard"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>

    

    <!-- View Appointment -->
    <li><a<?= isActive('/customer/appointments', $currentPath) ?> href="<?= $base ?>/customer/appointments"><i class="fa-regular fa-calendar-days"></i> View Appointment</a></li>

    <!-- Track Services -->
    <li><a<?= isActive('/customer/track-services', $currentPath) ?> href="<?= $base ?>/customer/track-services"><i class="fa-solid fa-location-crosshairs"></i> Track Service</a></li>

    <!-- Service History -->
    <li><a<?= isActive('/customer/service-history', $currentPath) ?> href="<?= $base ?>/customer/service-history"><i class="fa-solid fa-clock-rotate-left"></i> Service History</a></li>

    <!-- Service Reminder -->
    <li><a<?= isActive('/customer/service-reminder', $currentPath) ?> href="<?= $base ?>/customer/service-reminder"><i class="fa-solid fa-bell"></i> Service Reminder</a></li>

    <!-- Reviews / Feedback -->
    <li><a<?= isActive('/customer/rate-service', $currentPath) ?> href="<?= $base ?>/customer/rate-service"><i class="fa-regular fa-message"></i> Reviews</a></li>

    <!-- File a Complaint -->
    <li><a<?= isActive('/customer/file-complaint', $currentPath) ?> href="<?= $base ?>/customer/file-complaint"><i class="fa-solid fa-triangle-exclamation"></i> File a Complaint</a></li>
    <!-- Profile -->
    <li><a<?= isActive('/customer/profile', $currentPath) ?> href="<?= $base ?>/customer/profile"><i class="fa fa-user"></i> Profile</a></li>

    <!-- Payments -->
    <li>
    <a href="<?= rtrim(BASE_URL, '/') ?>/customer/payments">
        <i class="fa-solid fa-credit-card"></i>
        <span>Payments</span>
    </a>
</li>

    <!-- Logout -->
   <li><a href="<?= BASE_URL ?>/logout" id="logout-link">
        <i class="fa-solid fa-right-from-bracket"></i> 
        <span class="link-text">Sign Out</span>
    </a></li>

  </ul>
  <div id="logout-modal" class="modal hidden">
  <div class="modal-content">
    <h3>Confirm Logout</h3>
    <p>Are you sure you want to log out?</p>
    <div class="modal-buttons">
      <button id="cancel-logout" class="btn btn-cancel">Cancel</button>
      <button id="confirm-logout" class="btn btn-confirm">Log Out</button>
    </div>
  </div>
</div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const logoutLink = document.getElementById('logout-link');
  const modal = document.getElementById('logout-modal');
  const cancelBtn = document.getElementById('cancel-logout');
  const confirmBtn = document.getElementById('confirm-logout');

  logoutLink.addEventListener('click', function(e) {
    e.preventDefault();
    modal.classList.remove('hidden');
  });

  cancelBtn.addEventListener('click', function() {
    modal.classList.add('hidden');
  });

  confirmBtn.addEventListener('click', function() {
    window.location.href = logoutLink.href;
  });

  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.classList.add('hidden');
    }
  });
});
</script>
