<?php $base = rtrim(BASE_URL, '/'); ?>

<link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/sidebar.css" />

<div class="sidebar">
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
