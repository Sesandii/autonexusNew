<?php
$base = rtrim(BASE_URL, '/');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
function isActive($path, $current) { return strpos($current, $path) === 0 ? ' class="active"' : ''; }
?>
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

    <!-- Profile -->
    <li><a<?= isActive('/customer/profile', $currentPath) ?> href="<?= $base ?>/customer/profile"><i class="fa fa-user"></i> Profile</a></li>

    <!-- Logout -->
    <li><a href="<?= $base ?>/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
  </ul>
</aside>
