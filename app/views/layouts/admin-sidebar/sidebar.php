<?php
$B = rtrim(BASE_URL, '/');
$current = $current ?? '';
?>

<aside class="sidebar">
  <h2 class="logo">AutoNexus</h2>

  <ul class="menu">
    <!-- Dashboard -->
    <li class="menu-item <?= $current === 'dashboard' ? 'active' : '' ?>">
      <a href="<?= $B ?>/admin-dashboard">
        <i class="fa-solid fa-gauge"></i>
        <span>Dashboard</span>
      </a>
    </li>

    <!-- Operations -->
    <li class="menu-item has-submenu <?= in_array($current, ['appointments', 'approval', 'progress', 'history'], true) ? 'open' : '' ?>">
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

    <!-- Management -->
    <li class="menu-item has-submenu <?= in_array($current, ['branches', 'services', 'pricing', 'customers', 'service-managers', 'supervisors', 'mechanics', 'receptionists'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-solid fa-layer-group"></i>
        <span>Management</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
        <li class="<?= $current === 'branches' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/branches">Branches</a>
        </li>

        <!-- Services -->
        <li class="submenu-group <?= in_array($current, ['services', 'pricing'], true) ? 'open' : '' ?>">
          <button class="submenu-toggle submenu-toggle--inner" type="button">
            <span>Services</span>
            <i class="fa-solid fa-chevron-right caret"></i>
          </button>
          <ul class="submenu submenu--inner">
            <li class="<?= $current === 'services' ? 'active' : '' ?>">
              <a href="<?= $B ?>/admin/admin-viewservices">View Services</a>
            </li>
            <li class="<?= $current === 'pricing' ? 'active' : '' ?>">
              <a href="<?= $B ?>/admin/admin-updateserviceprice">Pricing Management</a>
            </li>
          </ul>
        </li>

        <!-- Manage Users -->
        <li class="submenu-group <?= in_array($current, ['customers', 'service-managers', 'supervisors', 'mechanics', 'receptionists'], true) ? 'open' : '' ?>">
          <button class="submenu-toggle submenu-toggle--inner" type="button">
            <span>Manage Users</span>
            <i class="fa-solid fa-chevron-right caret"></i>
          </button>
          <ul class="submenu submenu--inner">
            <li class="<?= $current === 'customers' ? 'active' : '' ?>">
              <a href="<?= $B ?>/admin/customers">Customers</a>
            </li>
            <li class="<?= $current === 'service-managers' ? 'active' : '' ?>">
              <a href="<?= $B ?>/admin/service-managers">Service Managers</a>
            </li>
            <li class="<?= $current === 'supervisors' ? 'active' : '' ?>">
              <a href="<?= $B ?>/admin/supervisors">Workshop Supervisors</a>
            </li>
            <li class="<?= $current === 'mechanics' ? 'active' : '' ?>">
              <a href="<?= $B ?>/admin/mechanics">Mechanics</a>
            </li>
            <li class="<?= $current === 'receptionists' ? 'active' : '' ?>">
              <a href="<?= $B ?>/admin/viewreceptionist">Receptionists</a>
            </li>
          </ul>
        </li>
      </ul>
    </li>

    <!-- Communication -->
    <li class="menu-item has-submenu <?= in_array($current, ['feedback', 'notifications', 'complaints'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-regular fa-comments"></i>
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

    <!-- Quality Control -->
    <li class="menu-item has-submenu <?= in_array($current, ['qc-reports', 'qc-approvals', 'qc-dashboard'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-solid fa-shield-heart"></i>
        <span>Quality Control</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
        <li class="<?= $current === 'qc-reports' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/quality/inspection-reports">Inspection Reports</a>
        </li>
        <li class="<?= $current === 'qc-approvals' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/quality/final-approvals">Final Approvals</a>
        </li>
        <li class="<?= $current === 'qc-dashboard' ? 'active' : '' ?>">
          <a href="<?= $B ?>/admin/quality/dashboard">Quality Dashboard</a>
        </li>
      </ul>
    </li>

    <!-- Finance & Analytics -->
    <li class="menu-item has-submenu <?= in_array($current, ['invoices', 'reports'], true) ? 'open' : '' ?>">
      <button class="submenu-toggle" type="button">
        <i class="fa-solid fa-chart-column"></i>
        <span>Finance & Analytics</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
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