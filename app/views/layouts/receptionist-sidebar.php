<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/receptionist/sidebar.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<div class="sidebar">
  <div class="brand-text">
    <h2>AutoNexus</h2>
  </div>
  
  <?php 
    $current_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  ?>

  <ul class="menu">
    <li class="<?= (strpos($current_path, 'dashboard') !== false) ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/dashboard">
        <i class="fas fa-tachometer-alt"></i> Dashboard
      </a>
    </li>
    <li class="<?= (strpos($current_path, 'appointments') !== false) ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/appointments">
        <i class="fas fa-calendar-check"></i> Appointments
      </a>
    </li>
    <li class="<?= (strpos($current_path, 'service') !== false) ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/service">
        <i class="fas fa-tools"></i> Service & Packages
      </a>
    </li>
    <li class="<?= (strpos($current_path, 'complaints') !== false) ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/complaints">
        <i class="fas fa-exclamation-circle"></i> Complaints
      </a>
    </li>
    <li class="<?= (strpos($current_path, 'billing') !== false) ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/billing">
        <i class="fas fa-file-invoice-dollar"></i> Billing & Payments
      </a>
    </li>
    <li class="<?= (strpos($current_path, 'customers') !== false) ? 'active' : '' ?>">
      <a href="<?= BASE_URL ?>/receptionist/customers">
        <i class="fas fa-users"></i> Customer Profiles
      </a>
    </li>

    <li>
    <a href="<?= BASE_URL ?>/logout" id="logout-link">
        <i class="fa-solid fa-right-from-bracket"></i> 
        <span class="link-text">Sign Out</span>
    </a>
</li>
  </ul>
</div>

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
</div>

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