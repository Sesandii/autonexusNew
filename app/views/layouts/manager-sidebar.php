<?php
if (!defined('BASE_URL')) {
    define('BASE_URL', '/autonexus'); // adjust if needed
}

// Get the current path after BASE_URL
$currentPath = $_SERVER['REQUEST_URI'] ?? '/';
$baseUrl = rtrim(BASE_URL, '/');

// Remove the BASE_URL from the path
if (strpos($currentPath, $baseUrl) === 0) {
    $currentPath = substr($currentPath, strlen($baseUrl));
}

// Remove query string
$currentPath = strtok($currentPath, '?');

// Explode path into segments
$segments = explode('/', trim($currentPath, '/')); // e.g. ['receptionist','appointments','edit','1']

// Get main section (after 'receptionist')
$section = $segments[1] ?? '';

// Define sidebar sections
$sections = ['dashboard','appointments','services','complaints','billing','profiles'];

// Determine active page
$activePage = in_array($section, $sections) ? $section : '';
?>

<div class="sidebar">
    <div class="logo">
        <img src="<?= BASE_URL ?>/public/assets/img/logo.png" alt="AutoNexus Logo" width="240">
        <h2>AUTONEXUS</h2>
    </div>

    <ul class="menu">
        <li class="<?= ($current_page == 'dashboard') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/dashboard">Dashboard</a>
        </li>
        <li class="<?= ($current_page == 'appointments') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/appointments">Appointments</a>
        </li>
        <li class="<?= $activePage === 'teamSchedule' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/schedule">Team Schedule</a>
        </li>
        <li class="<?= ($current_page == 'service') ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/services">Service & Packages</a>
        </li>
        <li class="<?= $activePage === 'complaints' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/complaints">Complaints</a>
        </li>
        <li class="<?= $activePage === 'billing' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/billing">Billing & Payments</a>
        </li>
        <li class="<?= $activePage === 'profiles' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/customers">Customer Profiles</a>
        </li>
        <li class="<?= $activePage === 'performance' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/performance">Team Performance</a>
        </li>
        <li class="<?= $activePage === 'reports' ? 'active' : '' ?>">
            <a href="<?= BASE_URL ?>/manager/reports">Reports</a>
        </li>
    </ul>
</div>
