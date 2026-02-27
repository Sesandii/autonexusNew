# Customer Area Sidebar Documentation

## Overview
This document details the location and structure of the customer area sidebar code in the AutoNexus application.

## Primary Sidebar File

### File Path
**`/home/runner/work/autonexusNew/autonexusNew/app/views/layouts/customer-sidebar.php`**

### Line Numbers
The complete sidebar markup is defined from **line 6 to line 43**.

### Sidebar Navigation Links
The sidebar contains the following navigation items (in order):

1. **Home** - Line 14
   - Route: `/customer/home`
   - Icon: `fa fa-home`

2. **Dashboard** - Line 17
   - Route: `/customer/dashboard`
   - Icon: `fa-solid fa-gauge`

3. **View Appointment** - Line 22
   - Route: `/customer/appointments`
   - Icon: `fa-regular fa-calendar-days`

4. **Track Service** - Line 25
   - Route: `/customer/track-services`
   - Icon: `fa-solid fa-location-crosshairs`

5. **Service History** - Line 28
   - Route: `/customer/service-history`
   - Icon: `fa-solid fa-clock-rotate-left`

6. **Service Reminder** - Line 31
   - Route: `/customer/service-reminder`
   - Icon: `fa-solid fa-bell`

7. **Reviews** - Line 34
   - Route: `/customer/rate-service`
   - Icon: `fa-regular fa-message`

8. **Profile** - Line 37
   - Route: `/customer/profile`
   - Icon: `fa fa-user`

9. **Logout** - Line 40
   - Route: `/logout`
   - Icon: `fa fa-sign-out`

## Sample Sidebar Code

```php
<?php
$base = rtrim(BASE_URL, '/');
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
function isActive($path, $current) { return strpos($current, $path) === 0 ? ' class="active"' : ''; }
?>
<aside class="sidebar">
  <div class="logo">
    <img src="<?= $base ?>/public/assets/img/logo.jpg" alt="AutoNexus Logo">
    <span class="brand-name">AutoNexus</span>
  </div>

  <ul class="menu">
    <!-- Registered Home -->
    <li><a<?= isActive('/customer/home', $currentPath) ?> href="<?= $base ?>/customer/home"><i class="fa fa-home"></i> Home</a></li>

    <!-- Dashboard -->
    <li><a<?= isActive('/customer/dashboard', $currentPath) ?> href="<?= $base ?>/customer/dashboard"><i class="fa-solid fa-gauge"></i> Dashboard</a></li>

    

    <!-- View Appointment -->
    <li><a<?= isActive('/customer/appointments', $currentPath) ?> href="<?= $base ?>/customer/appointments"><i class="fa-regular fa-calendar-days"></i> View Appointment</a></li>

    <!-- Track Services -->
    <li><a<?= isActive('/customer/track-services', $currentPath) ?> href="<?= $base ?>/customer/track-services"><i class="fa-solid fa-location-crosshairs"></i> Track Service</a></li>

    <!-- Service History -->
    <li><a<?= isActive('/customer/service-history', $currentPath) ?> href="<?= $base ?>/customer/service-history"><i class="fa-solid fa-clock-rotate-left"></i> Service History</a></li>

    <!-- Service Reminder -->
    <li><a<?= isActive('/customer/service-reminder', $currentPath) ?> href="<?= $base ?>/customer/service-reminder"><i class="fa-solid fa-bell"></i> Service Reminder</a></li>

    <!-- Reviews / Feedback -->
    <li><a<?= isActive('/customer/rate-service', $currentPath) ?> href="<?= $base ?>/customer/rate-service"><i class="fa-regular fa-message"></i> Reviews</a></li>

    <!-- Profile -->
    <li><a<?= isActive('/customer/profile', $currentPath) ?> href="<?= $base ?>/customer/profile"><i class="fa fa-user"></i> Profile</a></li>

    <!-- Logout -->
   <li><a href="<?= rtrim(BASE_URL, '/') ?>/logout"><i class="fa fa-sign-out"></i> Logout</a></li>

  </ul>
</aside>
```

## Sidebar Usage in Customer Pages

The sidebar is included in customer area pages using the following PHP include statement:

```php
<?php include APP_ROOT . '/views/layouts/customer-sidebar.php'; ?>
```

### Pages Using the Sidebar (9 files):

1. `/app/views/customer/dashboard/index.php` - Line 18
2. `/app/views/customer/feedback/index.php` - Includes sidebar
3. `/app/views/customer/service-reminder/index.php` - Includes sidebar
4. `/app/views/customer/track-services/index.php` - Line 18
5. `/app/views/customer/profile/vehicle_form.php` - Includes sidebar
6. `/app/views/customer/service-history/index.php` - Line 21
7. `/app/views/customer/appointments/index.php` - Line 20
8. `/app/views/customer/profile/index.php` - Includes sidebar
9. `/app/views/customer/profile/edit.php` - Includes sidebar

## Styling

The sidebar styling is defined in:
**`/home/runner/work/autonexusNew/autonexusNew/public/assets/css/customer/sidebar.css`**

This CSS file is linked in all customer pages that use the sidebar.

## Key Features

1. **Dynamic Active State**: The `isActive()` function automatically highlights the current page in the sidebar navigation
2. **Responsive Design**: The sidebar uses CSS for responsive behavior
3. **Icon Integration**: Uses Font Awesome icons for visual navigation elements
4. **Logo Display**: Includes the AutoNexus logo and brand name at the top
5. **Base URL Handling**: Dynamically constructs URLs using the BASE_URL constant

## Technical Details

- **File Type**: PHP
- **HTML Structure**: Uses semantic HTML with `<aside>` element
- **Navigation**: Unordered list (`<ul>`) with list items (`<li>`) containing anchor tags
- **Active Link Detection**: PHP function compares current path with menu item paths
- **Icon Library**: Font Awesome 6.4.0 (loaded via CDN in customer pages)
