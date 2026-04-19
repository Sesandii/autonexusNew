<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/autonexus');
}

$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$baseUrl = rtrim(BASE_URL, '/');

if (strpos($currentPath, $baseUrl) === 0) {
    $currentPath = substr($currentPath, strlen($baseUrl));
}

$currentPath = strtok($currentPath, '?');
$segments = explode('/', trim($currentPath, '/'));
$section = $segments[1] ?? '';
$sections = ['dashboard','appointments','schedule','work-orders','services','complaints','billing','customers','performance','reports'];
$activePage = in_array($section, $sections) ? $section : '';
?>

<div class="sidebar">
    <div class="logo">
        <h2>AUTONEXUS</h2>
    </div>

    <ul class="menu">
        <li class="<?= $activePage == 'dashboard' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/dashboard">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li class="<?= $activePage == 'appointments' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/appointments">
                <i class="fas fa-calendar"></i>
                <span>Appointments</span>
            </a>
        </li>
        <li class="<?= $activePage == 'schedule' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/schedule">
                <i class="fas fa-users"></i>
                <span>Staff Management</span>
            </a>
        </li>
        <li class="<?= $activePage == 'work-orders' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/work-orders">
                <i class="fas fa-clipboard-list"></i>
                <span>Work Orders</span>
            </a>
        </li>
        <li class="<?= $activePage == 'services' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/services">
                <i class="fas fa-wrench"></i>
                <span>Services & Packages</span>
            </a>
        </li>
        <li class="<?= $activePage == 'complaints' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/complaints">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Complaints</span>
            </a>
        </li>
        <li class="<?= $activePage == 'billing' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/billing">
                <i class="fas fa-credit-card"></i>
                <span>Billing & Payments</span>
            </a>
        </li>
        <li class="<?= $activePage == 'customers' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/customers">
                <i class="fas fa-user"></i>
                <span>Customer Profiles</span>
            </a>
        </li>
        <li class="<?= $activePage == 'performance' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/performance">
                <i class="fas fa-chart-line"></i>
                <span>Team Performance</span>
            </a>
        </li>
        <li class="<?= $activePage == 'reports' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/reports">
                <i class="fas fa-file-alt"></i>
                <span>Reports</span>
            </a>
        </li>
        <li class="<?= $activePage == 'profile' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/profile">
                <i class="fa-solid fa-user"></i>
                <span>Profile</span>
            </a>
        </li>
<li>
  <a href="<?= BASE_URL ?>/logout.php">
    <i class="fa-solid fa-right-from-bracket"></i>
    <span>Log Out</span>
  </a>
</li>
    </ul>
</div>