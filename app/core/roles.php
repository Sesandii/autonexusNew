<?php
// roles.php
require_once CONFIG_PATH . '/config.php';

function redirect_by_role(string $role): void {
    // Always use absolute app routes that your Router knows.
    $map = [
        'admin'           => '/admin-dashboard',
        'service_manager' => '/service-manager/dashboard',
        'supervisor'      => '/supervisor/dashboard',
        'mechanic'        => '/mechanic/dashboard',
        'receptionist'    => '/receptionist/dashboard',
        // send customers to REGISTERED HOME
        'customer'        => '/customer/home',
    ];

    // Default/fallback also goes to registered home
    $target = $map[$role] ?? '/customer/home';

    // Safe join: BASE_URL may end with a slash; $target always starts with one.
    $base = rtrim(BASE_URL ?? '', '/');      // e.g. "/autonexus"
    $loc  = $base . $target;                 // -> "/autonexus/customer/home"

    header('Location: ' . $loc, true, 302);
    exit;
}
