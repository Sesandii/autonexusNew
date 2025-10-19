<?php $B = rtrim(BASE_URL, '/'); ?>

<aside class="sidebar">
  <h2 class="logo">AutoNexus</h2>
  <ul class="menu">
    <!-- Dashboard -->
    <li class="menu-item <?php if($current=='dashboard') echo 'active'; ?>">
      <a href="<?= BASE_URL ?>/admin-dashboard">
    <i class="fa-solid fa-gauge"></i>
    <span>Dashboard</span>
</a>

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
          <a href="<?= BASE_URL ?>/admin/customers">Customers</a>
        </li>
        <li class="<?php if($current=='service-managers') echo 'active'; ?>">
          <a href="<?= BASE_URL ?>/admin/service-managers">Service Managers</a>

        </li>
        <li class="<?php if($current=='supervisors') echo 'active'; ?>">
         <a href="<?= BASE_URL ?>/admin/supervisors">Workshop Supervisors</a>
        </li>
        <li class="<?php if($current=='mechanics') echo 'active'; ?>">
          <a href="<?= BASE_URL ?>/admin/mechanics">Mechanics</a>
        </li>
       <li class="<?php if(($current ?? '')==='receptionists') echo 'active'; ?>">
  <a href="<?= rtrim(BASE_URL,'/') ?>/admin/viewreceptionist">Receptionists</a>
</li>

      </ul>
    </li>

    <!-- Other menu items -->
    <li class="menu-item <?php if($current=='branches') echo 'active'; ?>">
      <a href="<?= $B ?>/admin/branches"><i class="fa-solid fa-diagram-project"></i><span>Manage Branches</span></a>
    </li>

    <li class="menu-item <?php if($current=='services') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewservices"><i class="fa-solid fa-screwdriver-wrench"></i><span>Service Management</span></a>
    </li>

    <li class="menu-item <?php if($current=='pricing') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-updateserviceprice"><i class="fa-solid fa-dollar-sign"></i><span>Pricing Management</span></a>
    </li>

    <li class="menu-item <?php if($current=='approval') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-serviceapproval"><i class="fa-solid fa-clipboard-check"></i><span>Service Approval</span></a>
    </li>

   

    <li class="menu-item <?php if($current=='appointments') echo 'active'; ?>">
  <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-appointments"><i class="fa-solid fa-calendar-check"></i><span>Appointments</span>
  </a>
</li>

    <li class="menu-item <?php if($current=='progress') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-ongoingservices"><i class="fa-solid fa-heart-pulse"></i><span>Service Progress</span></a>
    </li>

    <li class="menu-item <?php if($current=='history') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-servicehistory"><i class="fa-solid fa-clock-rotate-left"></i><span>Service History</span></a>
    </li>

    <li class="menu-item <?php if($current=='feedback') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewfeedback"><i class="fa-regular fa-message"></i><span>Feedback</span></a>
    </li>

    <li class="menu-item <?php if($current=='notifications') echo 'active'; ?>">
       <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-notifications"><i class="fa-regular fa-bell"></i><span>Notifications</span></a>
    </li>

    <li class="menu-item <?php if($current=='reports') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewreports"><i class="fa-solid fa-chart-column"></i><span>Reports</span></a>
    </li>

    <li class="menu-item <?php if($current=='invoices') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewinvoices"><i class="fa-solid fa-file-invoice-dollar"></i><span>Invoices</span></a>
    </li>
    <li class="menu-item <?php if($current=='invoices') echo 'active'; ?>">
      <a href="<?= rtrim(BASE_URL,'/') ?>/admin/admin-viewinvoices"><i class="fa-solid fa-right-from-bracket"></i><span>Log Out</span></a>
    </li>
    
  </ul>
</aside>
