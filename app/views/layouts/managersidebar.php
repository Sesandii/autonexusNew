<?php
$base = rtrim(BASE_URL, '/');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

function isActive($path, $current) {
    return strpos($current, $path) === 0 ? ' class="active"' : '';
}
?>
<aside class="sidebar">
  <div class="logo">
    <img src="<?= $base ?>/public/assets/img/logo.jpg" alt="AutoNexus Logo">
    <span class="brand-name">AutoNexus</span>
  </div>

  <ul class="menu">
    <li><a<?= isActive('/manager/dashboard', $currentPath) ?> href="<?= $base ?>/manager/dashboard"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>
    <li><a<?= isActive('/manager/appointments', $currentPath) ?> href="<?= $base ?>/manager/appointments"><i class="fa-regular fa-calendar-days"></i> Appointments</a></li>
    <li><a<?= isActive('/manager/schedule', $currentPath) ?> href="<?= $base ?>/manager/schedule"><i class="fa-regular fa-calendar"></i> Team Schedule</a></li>
    <li><a<?= isActive('/manager/services', $currentPath) ?> href="<?= $base ?>/manager/services"><i class="fa-solid fa-screwdriver-wrench"></i> Services & Packages</a></li>
    <li><a<?= isActive('/manager/complaints', $currentPath) ?> href="<?= $base ?>/manager/complaints"><i class="fa-solid fa-triangle-exclamation"></i> Complaints</a></li>
    <li><a<?= isActive('/manager/performance', $currentPath) ?> href="<?= $base ?>/manager/performance"><i class="fa-solid fa-chart-line"></i> Team Performance</a></li>
    <li><a<?= isActive('/manager/customers', $currentPath) ?> href="<?= $base ?>/manager/customers"><i class="fa-solid fa-users"></i> Customers</a></li>
    <li><a<?= isActive('/manager/service-history', $currentPath) ?> href="<?= $base ?>/manager/servicehistory"><i class="fa-solid fa-clock-rotate-left"></i> Service History</a></li>
    <li><a<?= isActive('/manager/reports', $currentPath) ?> href="<?= $base ?>/manager/reports"><i class="fa-solid fa-file-lines"></i> Reports</a></li>
    <li><a href="<?= $base ?>/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
  </ul>
</aside>
