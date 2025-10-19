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
    <li><a<?= isActive('/customer/dashboard',$currentPath) ?> href="<?= $base ?>/customer/dashboard"><i class="fa fa-home"></i> Home</a></li>
    <li><a<?= isActive('/customer/dashboard',$currentPath) ?> href="<?= $base ?>/customer/dashboard"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>

    <!-- Update these targets as you implement each page -->
    <li><a href="<?= $base ?>/customer/book"><i class="fa fa-book"></i> Book Appointment</a></li>
    <li><a href="<?= $base ?>/customer/appointments"><i class="fa-regular fa-calendar-days"></i> View Appointments</a></li>
    <li><a href="<?= $base ?>/customer/track"><i class="fa-solid fa-heart-pulse"></i> Track Service</a></li>
    <li><a href="<?= $base ?>/customer/history"><i class="fa-solid fa-clock-rotate-left"></i> Service History</a></li>
    <li><a href="<?= $base ?>/customer/reminders"><i class="fa-solid fa-clipboard-check"></i> Service Reminder</a></li>
    <li><a href="<?= $base ?>/services/available"><i class="fa fa-check-circle"></i> Available Services</a></li>
    <li><a href="<?= $base ?>/customer/reviews"><i class="fa-regular fa-message"></i> Reviews</a></li>
    <li><a href="<?= $base ?>/customer/profile"><i class="fa fa-user"></i> Profile</a></li>
    <li><a href="<?= $base ?>/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
  </ul>
</aside>
