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
      <a href="<?= BASE_URL ?>/receptionist/dashboard">Dashboard</a>
    </li>
    <li class="<?= ($current_page == 'appointments') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/appointments">Appointments</a>
    </li>
    <li class="<?= ($current_page == 'service') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/service">Service & Packages</a>
    </li>
    <li class="<?= ($current_page == 'complaints') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/complaints">Complaints</a>
    </li>
    <li class="<?= ($current_page == 'billing') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/billing">Billing & Payments</a>
    </li>
    <li class="<?= ($current_page == 'customers') ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/customers">Customer Profiles</a>
    </li>
  </ul>
</div>
