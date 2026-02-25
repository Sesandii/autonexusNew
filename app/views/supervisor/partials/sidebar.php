<?php $base = rtrim(BASE_URL, '/'); ?>

<link rel="stylesheet" href="/autonexus/public/assets/css/supervisor/sidebar.css" />

<div class="sidebar">
  <div class="logo-container">
    <img src="/autonexus/public/assets/img/Auto1.png" class="logo-collapsed" />
    <img src="/autonexus/public/assets/img/Auto.png" class="logo-expanded" />
  </div>

  <h2 class="brand-text">AUTONEXUS</h2>

  <a href="<?= $base ?>/supervisor/dashboard"
     class="<?= ($_SERVER['REQUEST_URI'] === '/autonexus/supervisor/dashboard') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/dashboard.png"/>
    <span class="link-text">Dashboard</span>
  </a>

  <a href="<?= $base ?>/supervisor/workorders"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/workorders') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/jobs.png"/>
    <span class="link-text">Work Orders</span>
  </a>

  <a href="<?= $base ?>/supervisor/assignedjobs"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/assignedjobs') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/assigned.png"/>
    <span class="link-text">Assigned</span>
  </a>

  <a href="<?= $base ?>/supervisor/coordination"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/coordination') ? 'nav' : '' ?>">
     <img src="/autonexus/public/assets/img/Complaints.png"/>
    <span class="link-text">Coordinate</span>
  </a>

  <a href="<?= $base ?>/supervisor/history"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/history') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/history.png"/>
    <span class="link-text">Vehicle History</span>
  </a>

  <a href="<?= $base ?>/supervisor/complaints_feedbacks"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/complaints_feedbacks') ? 'nav' : '' ?>">
     <img src="/autonexus/public/assets/img/Feedbacks.png"/>
    <span class="link-text">Feedback</span>
  </a>

  <a href="<?= $base ?>/supervisor/reports"
     class="<?= str_contains($_SERVER['REQUEST_URI'], '/reports') ? 'nav' : '' ?>">
    <img src="/autonexus/public/assets/img/Inspection.png"/>
    <span class="link-text">Report</span>
  </a>
</div>
