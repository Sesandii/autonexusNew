<?php $base = rtrim(BASE_URL, '/'); ?>
<link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/sidebar.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<?php
function isActive($route) {
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    $current = rtrim($current, '/');
    $route   = rtrim($route, '/');

    return str_starts_with($current, $route) ? 'active' : '';
}
?>

<div class="sidebar"> 
<h2 class="brand-text">AutoNexus</h2>
  <div class="nav-links">

  <a href="<?= $base ?>/mechanic/dashboard" class="<?= isActive('/mechanic/dashboard') ?>">
    <i class="fa-solid fa-gauge-high"></i> <span class="link-text">Dashboard</span>
  </a>

  <a href="<?= $base ?>/mechanic/jobs" class="<?= isActive('/mechanic/jobs') ?>">
    <i class="fa-solid fa-layer-group"></i> <span class="link-text">Jobs</span>
  </a>

  <a href="<?= $base ?>/mechanic/assignedjobs" class="<?= isActive('/mechanic/assignedjobs') ?>">
    <i class="fa-solid fa-screwdriver-wrench"></i> 
    <span class="link-text">Assigned</span>
  </a>

  <a href="<?= $base ?>/mechanic/history" class="<?= isActive('/mechanic/history') ?>">
  <i class="fa-solid fa-clock-rotate-left"></i> <span class="link-text">Vehicle History</span>
  </a>

  <a href="<?= $base ?>/mechanic/profile/edit" class="<?= isActive('/mechanic/profile/edit') ?>">
    <i class="fa-solid fa-user"></i> <span class="link-text">Profile</span>
  </a>

  <a href="<?= $base ?>/logout" id="logout-link">
    <i class="fa-solid fa-right-from-bracket"></i> <span class="link-text">Sign Out</span>
  </a>
  </div>
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