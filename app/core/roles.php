<?php
// roles.php

require_once CONFIG_PATH . '/config.php';




function redirect_by_role(string $role): void {
    $map = [
        'admin'           => '/admin-dashboard',
        'service_manager' => 'service_manager/dashboard.php',
        'supervisor'      => 'supervisor/dashboard.php',
        'mechanic'        => 'mechanic/dashboard.php',
        'receptionist'    => 'receptionist/dashboard.php',
        'customer'        => '/customer/dashboard.php',
    ];
    $target = $map[$role] ?? './customer/dashboard.php';
    header('Location: ' . BASE_URL . $target);
    exit;
}
