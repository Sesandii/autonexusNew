<?php $base = rtrim(BASE_URL, '/'); ?>

<link rel="stylesheet" href="<?= $base ?>/public/assets/css/supervisor/sidebar.css" />
<?php
function isActive($route) {
    $current = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

    // normalize (remove trailing slash)
    $current = rtrim($current, '/');
    $route   = rtrim($route, '/');

    return str_starts_with($current, $route) ? 'active' : '';
}
?>

<div class="sidebar">

  <div class="top-section">
    <div class="logo-container">
      <img src="<?= $base ?>/public/assets/img/Auto1.png" class="logo-collapsed" />
      <img src="<?= $base ?>/public/assets/img/Auto.png" class="logo-expanded" />
    </div>

    <h2 class="brand-text">AUTONEXUS</h2>

    <div class="nav-links">
      <a href="<?= $base ?>/supervisor/dashboard"
         class="<?= isActive('/supervisor/dashboard') ?>">
        <img src="<?= $base ?>/public/assets/img/dashboard.png"/>
        <span class="link-text">Dashboard</span>
      </a>

      <a href="<?= $base ?>/supervisor/workorders"
      class="<?= isActive('/supervisor/workorders') ?>">
        <img src="<?= $base ?>/public/assets/img/jobs.png"/>
        <span class="link-text">Work Orders</span>
      </a>

      <a href="<?= $base ?>/supervisor/assignedjobs"
         class="<?= isActive('/supervisor/assignedjobs') ?>">
        <img src="<?= $base ?>/public/assets/img/assigned.png"/>
        <span class="link-text">Assigned</span>
      </a>

      <a href="<?= $base ?>/supervisor/coordination"
         class="<?= isActive('/supervisor/coordination') ?>">
        <img src="<?= $base ?>/public/assets/img/Complaints.png"/>
        <span class="link-text">Coordinate</span>
      </a>

      <a href="<?= $base ?>/supervisor/history"
         class="<?= isActive('/supervisor/history') ?>">
        <img src="<?= $base ?>/public/assets/img/history.png"/>
        <span class="link-text">Vehicle History</span>
      </a>

      <a href="<?= $base ?>/supervisor/complaints_feedbacks"
         class="<?= isActive('/supervisor/complaints_feedbacks') ?>">
        <img src="<?= $base ?>/public/assets/img/Feedbacks.png"/>
        <span class="link-text">Feedback</span>
      </a>

      <a href="<?= $base ?>/supervisor/reports"
         class="<?= isActive('/supervisor/reports') ?>">
        <img src="<?= $base ?>/public/assets/img/Inspection.png"/>
        <span class="link-text">Report</span>
      </a>
    </div>
  </div>

  <!-- 🔥 Bottom User Section -->
  <div class="sidebar-bottom">
  <a href="<?= $base ?>/supervisor/profile/edit"
  class="<?= isActive('/supervisor/profile/edit')?>">
    <img src="<?= $base ?>/public/assets/img/user.png" />
    <span>Edit Profile</span>
  </a>

  <a href="<?= $base ?>/logout" id="logout-link">
    <img src="<?= $base ?>/public/assets/img/logout.png" />
    <span>Sign Out</span>
</a>
</div>
 <!-- Logout Confirmation Modal -->
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

  // Show modal on logout click
  logoutLink.addEventListener('click', function(e) {
    e.preventDefault();
    modal.classList.remove('hidden');
  });

  // Cancel logout
  cancelBtn.addEventListener('click', function() {
    modal.classList.add('hidden');
  });

  // Confirm logout
  confirmBtn.addEventListener('click', function() {
    window.location.href = logoutLink.href;
  });

  // Close modal if click outside content
  modal.addEventListener('click', function(e) {
    if (e.target === modal) {
      modal.classList.add('hidden');
    }
  });
});

</script>