<?php
$B = rtrim(BASE_URL, '/');
$current = $current ?? '';
?>

<aside class="sidebar">
  <div class="logo-wrap">
    <a href="<?= $B ?>/admin-dashboard" class="logo-link">
      <img src="<?= $B ?>/public/assets/img/logo.jpg" alt="AutoNexus Logo" class="logo-img">
      <span class="logo-text">AutoNexus</span>
    </a>
  </div>

  <ul class="menu">
    <!-- Dashboard -->
    <li class="menu-item <?= $current === 'dashboard' ? 'active' : '' ?>">
      <a href="<?= $B ?>/admin-dashboard">
        <i class="fa-solid fa-gauge"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Operations -->
    <li
      class="menu-item has-submenu <?= in_array($current, ['appointments', 'approval', 'progress', 'history'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-solid fa-gear"></i>
        <span>Operations</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
        <li class="<?= $current === 'appointments' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/appointments">Appointments</a>
        </li>
        <li class="<?= $current === 'approval' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-serviceapproval">Service Approval</a>
        </li>
        <li class="<?= $current === 'progress' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-ongoingservices">Service Progress</a>
        </li>
        <li class="<?= $current === 'history' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-servicehistory">Service History</a>
        </li>
      </ul>
    </li>

    <!-- Branches -->
    <li class="menu-item <?= $current === 'branches' ? 'active' : '' ?>">
      <a href="<?= $B ?>/admin/branches">
        <i class="fa-solid fa-building"></i>
        <span>Branches</span>
      </a>
    </li>

    <!-- Services -->
    <li class="menu-item <?= $current === 'services' ? 'active' : '' ?>">
      <a href="<?= $B ?>/admin/admin-viewservices">
        <i class="fa-solid fa-screwdriver-wrench"></i>
        <span>Services</span>
      </a>
    </li>

    <!-- Users -->
    <li
      class="menu-item has-submenu <?= in_array($current, ['staff', 'customers', 'service-managers', 'mechanics', 'supervisors', 'receptionists'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-solid fa-users"></i>
        <span>Users</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
        <li class="<?= $current === 'staff' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-viewstaff">Staff Management</a>
        </li>
        <li class="<?= $current === 'customers' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/customers">Customers</a>
        </li>
      </ul>
    </li>

    <!-- Communication -->
    <li
      class="menu-item has-submenu <?= in_array($current, ['feedback', 'notifications', 'complaints'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-solid fa-comments"></i>
        <span>Communication</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
        <li class="<?= $current === 'feedback' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-viewfeedback">Feedback</a>
        </li>
        <li class="<?= $current === 'notifications' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-notifications">Notifications</a>
        </li>
        <li class="<?= $current === 'complaints' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-viewcomplaints">Complaints</a>
        </li>
      </ul>
    </li>

    <!-- Finance & Analytics -->
    <li
      class="menu-item has-submenu <?= in_array($current, ['invoices', 'payments', 'reports'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-solid fa-chart-column"></i>
        <span>Finance & Analytics</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
        <li class="<?= $current === 'payments' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-viewpayments">Payments</a>
        </li>
        <li class="<?= $current === 'invoices' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-viewinvoices">Invoices</a>
        </li>
        <li class="<?= $current === 'reports' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/admin-viewreports">Reports</a>
        </li>
      </ul>
    </li>

    <!-- Profile -->
    <li class="menu-item <?= $current === 'profile' ? 'active' : '' ?>">
      <a href="<?= $B ?>/admin/profile">
        <i class="fa-solid fa-user"></i>
        <span>Profile</span>
      </a>
    </li>

    <!-- Logout -->
    <li class="menu-item <?= $current === 'logout' ? 'active' : '' ?>">
      <a href="<?= $B ?>/logout">
        <i class="fa-solid fa-right-from-bracket"></i>
        <span>Log Out</span>
      </a>
    </li>
  </ul>
</aside>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.submenu-toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const parent = btn.closest('.has-submenu, .submenu-group');
        if (parent) {
          parent.classList.toggle('open');
        }
      });
    });
  });
</script>