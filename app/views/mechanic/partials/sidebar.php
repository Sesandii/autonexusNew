<?php $base = rtrim(BASE_URL, '/'); ?>

<link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/sidebar.css" />

<div class="sidebar">
<div class="top-section">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto1.png" class="logo-collapsed" />
    <img src="/autonexus/public/assets/img/Auto.png" class="logo-expanded" />
  </div>

  <h2 class="brand-text">AUTONEXUS</h2>

  <a href="<?= $base ?>/mechanic/dashboard"
     class="<?= ($_SERVER['REQUEST_URI'] === '/autonexus/supervisor/dashboard') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/dashboard.png"/>
    <span class="link-text">Dashboard</span>
  </a>

  <a href="<?= $base ?>/mechanic/jobs"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/jobs') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/jobs.png"/>
    <span class="link-text">Jobs</span>
  </a>

  <a href="<?= $base ?>/mechanic/assignedjobs"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/assignedjobs') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/assigned.png"/>
    <span class="link-text">Assigned</span>
  </a>

  <a href="<?= $base ?>/mechanic/history"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/history') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/history.png"/>
    <span class="link-text">Vehicle History</span>
  </a>
</div>

<!-- 🔥 Bottom User Section -->
<div class="sidebar-bottom">
  <a href="<?= $base ?>/mechanic/profile/edit">
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