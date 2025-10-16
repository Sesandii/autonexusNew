<?php $B = rtrim(BASE_URL, '/'); ?>

<aside class="sidebar">
  <h2 class="logo">AutoNexus</h2>
  <ul class="menu">
    <!-- Dashboard -->
    <li class="menu-item <?php if($current=='dashboard') echo 'active'; ?>">
      <a href="<?= $B ?>/"><i class="fa-solid fa-gauge"></i><span>Dashboard</span></a>
    </li>

    <!-- Manage Users -->
    <li class="menu-item has-submenu">
      <button class="submenu-toggle">
        <i class="fa-solid fa-users-gear"></i>
        <span>Manage Users</span>
        <i class="fa-solid fa-chevron-right caret"></i>
      </button>

      <ul class="submenu">
        <li class="<?php if($current=='customers') echo 'active'; ?>">
          <a href="<?= $B ?>/customers"><span>Customers</span></a>
        </li>
        <li class="<?php if($current=='service-managers') echo 'active'; ?>">
          <a href="<?= $B ?>/service-managers"><span>Service Managers</span></a>
        </li>
        <li class="<?php if($current=='supervisors') echo 'active'; ?>">
          <a href="<?= $B ?>/supervisors"><span>Workshop Supervisors</span></a>
        </li>
        <li class="<?php if($current=='mechanics') echo 'active'; ?>">
          <a href="<?= $B ?>/mechanics"><span>Mechanics</span></a>
        </li>
        <li class="<?php if($current=='receptionists') echo 'active'; ?>">
          <a href="<?= $B ?>/receptionists"><span>Receptionists</span></a>
        </li>
      </ul>
    </li>

    <!-- Other menu items -->
    <li class="menu-item <?php if($current=='branches') echo 'active'; ?>">
      <a href="<?= $B ?>/branches"><i class="fa-solid fa-diagram-project"></i><span>Manage Branches</span></a>
    </li>

    <li class="menu-item <?php if($current=='services') echo 'active'; ?>">
      <a href="<?= $B ?>/services"><i class="fa-solid fa-screwdriver-wrench"></i><span>Service Management</span></a>
    </li>

    <li class="menu-item <?php if($current=='pricing') echo 'active'; ?>">
      <a href="<?= $B ?>/pricing"><i class="fa-solid fa-dollar-sign"></i><span>Pricing Management</span></a>
    </li>

    <li class="menu-item <?php if($current=='approval') echo 'active'; ?>">
      <a href="<?= $B ?>/service-approval"><i class="fa-solid fa-clipboard-check"></i><span>Service Approval</span></a>
    </li>

    <li class="menu-item <?php if($current=='appointments') echo 'active'; ?>">
      <a href="<?= $B ?>/appointments"><i class="fa-regular fa-calendar-days"></i><span>Appointments</span></a>
    </li>

    <li class="menu-item <?php if($current=='progress') echo 'active'; ?>">
      <a href="<?= $B ?>/service-progress"><i class="fa-solid fa-heart-pulse"></i><span>Service Progress</span></a>
    </li>

    <li class="menu-item <?php if($current=='history') echo 'active'; ?>">
      <a href="<?= $B ?>/service-history"><i class="fa-solid fa-clock-rotate-left"></i><span>Service History</span></a>
    </li>

    <li class="menu-item <?php if($current=='feedback') echo 'active'; ?>">
      <a href="<?= $B ?>/feedback"><i class="fa-regular fa-message"></i><span>Feedback</span></a>
    </li>

    <li class="menu-item <?php if($current=='notifications') echo 'active'; ?>">
      <a href="<?= $B ?>/notifications"><i class="fa-regular fa-bell"></i><span>Notifications</span></a>
    </li>

    <li class="menu-item <?php if($current=='reports') echo 'active'; ?>">
      <a href="<?= $B ?>/reports"><i class="fa-solid fa-chart-column"></i><span>Reports</span></a>
    </li>

    <li class="menu-item <?php if($current=='invoices') echo 'active'; ?>">
      <a href="<?= $B ?>/invoices"><i class="fa-solid fa-file-invoice-dollar"></i><span>Invoices</span></a>
    </li>
  </ul>
</aside>
