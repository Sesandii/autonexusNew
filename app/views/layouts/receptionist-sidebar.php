<?php
// Smart active page detection that works anywhere
$current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Fallback for subdirectory installations
if (defined('BASE_URL') && strpos($_SERVER['REQUEST_URI'], BASE_URL) === 0) {
    $path = substr($_SERVER['REQUEST_URI'], strlen(BASE_URL));
    $segments = explode('/', trim($path, '/'));
    $current_page = $segments[1] ?? $current_page;
}
?>


<div class="sidebar">
  <div class="logo">
    <img src="<?= BASE_URL ?>/public/assets/img/logo.png" alt="AutoNexus Logo">
    <h2>AUTONEXUS</h2>
    <p>VEHICLE SERVICE</p>
  </div>
  
  <?php 
    // get current route to set "active" class dynamically
    $current_page = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
  ?>

  <ul class="menu">
    <li class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/dashboard">
        <i class="fas fa-th-large"></i>
        <span>Dashboard</span>
      </a>
    </li>
    <li class="<?= ($current_page == 'appointments') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/appointments">
        <i class="fas fa-calendar-check"></i>
        <span>Appointments</span>
      </a>
    </li>
    <li class="<?= ($current_page == 'service') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/service">
        <i class="fas fa-tools"></i>
        <span>Service & Packages</span>
      </a>
    </li>
    <li class="<?= ($current_page == 'complaints') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/complaints">
        <i class="fas fa-exclamation-triangle"></i>
        <span>Complaints</span>
      </a>
    </li>
    <li class="<?= ($current_page == 'billing') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/billing">
        <i class="fas fa-file-invoice-dollar"></i>
        <span>Billing & Payments</span>
      </a>
    </li>
    <li class="<?= ($current_page == 'customers') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/customers">
        <i class="fas fa-users"></i>
        <span>Customer Profiles</span>
      </a>
    </li>
  </ul>
</div>