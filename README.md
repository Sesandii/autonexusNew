# AutoNexus - Service Center Management System

This folder contains a minimal MVC skeleton (no frameworks) and copies of your static assets where possible.

## Customer Complaint Form
For information about the customer complaint form setup, CSS asset paths, and troubleshooting, see [COMPLAINT_FORM_SETUP.md](./COMPLAINT_FORM_SETUP.md).

## What we did
- Extracted your uploaded zip to: `/mnt/data/user_project_raw`
- Collected CSS/JS/IMG assets and placed them in `public/assets/...`
- Created core MVC files: Router, Controller, Model, Database, front controller
- Added a sample Home controller + view and a layout
- Created `.htaccess` for Apache rewrite to `public/index.php`

## Next steps
1. Point Apache DocumentRoot to `public/`.
2. Update `config/config.php` with your DB credentials.
3. For each existing page, create a Controller + View:
   - Route in `public/index.php`
   - Controller in `app/controllers`
   - View in `app/views/<module>/...`
   - (Optional) Model in `app/models` for DB ops

## Copied assets
[
  [
    "autonexus/admin-appoinments/script.js",
    "public/assets/js/script.js"
  ],
  [
    "autonexus/admin-appoinments/style.css",
    "public/assets/css/style.css"
  ],
  [
    "autonexus/admin-appoinments/styles.css",
    "public/assets/css/styles.css"
  ],
  [
    "autonexus/admin-dashboard/dashboard.css",
    "public/assets/css/dashboard.css"
  ],
  [
    "autonexus/admin-dashboard/script.js",
    "public/assets/js/script_1.js"
  ],
  [
    "autonexus/admin-notifications/style.css",
    "public/assets/css/style_1.css"
  ],
  [
    "autonexus/admin-ongoingservices/styles.css",
    "public/assets/css/styles_1.css"
  ],
  [
    "autonexus/admin-serviceapproval/styles.css",
    "public/assets/css/styles_2.css"
  ],
  [
    "autonexus/admin-serviceprogress/style.css",
    "public/assets/css/style_2.css"
  ],
  [
    "autonexus/admin-shared/management.css",
    "public/assets/css/management.css"
  ],
  [
    "autonexus/admin-sidebar/styles.css",
    "public/assets/css/styles_3.css"
  ],
  [
    "autonexus/admin-updateserviceprice/styles.css",
    "public/assets/css/styles_4.css"
  ],
  [
    "autonexus/admin-viewbranchdetails/styles.css",
    "public/assets/css/styles_5.css"
  ],
  [
    "autonexus/admin-viewfeedback/script.js",
    "public/assets/js/script_2.js"
  ],
  [
    "autonexus/admin-viewfeedback/style.css",
    "public/assets/css/style_3.css"
  ],
  [
    "autonexus/admin-viewinvoices/script.js",
    "public/assets/js/script_3.js"
  ],
  [
    "autonexus/admin-viewinvoices/style.css",
    "public/assets/css/style_4.css"
  ],
  [
    "autonexus/admin-viewmanager/script.js",
    "public/assets/js/script_4.js"
  ],
  [
    "autonexus/admin-viewreceptionist/script.js",
    "public/assets/js/script_5.js"
  ],
  [
    "autonexus/admin-viewreports/script.js",
    "public/assets/js/script_6.js"
  ],
  [
    "autonexus/admin-viewreports/style.css",
    "public/assets/css/style_5.css"
  ],
  [
    "autonexus/admin-viewservices/script.js",
    "public/assets/js/script_7.js"
  ],
  [
    "autonexus/admin-viewservices/styles.css",
    "public/assets/css/styles_6.css"
  ],
  [
    "autonexus/admin-viewsupervisor/script.js",
    "public/assets/js/script_8.js"
  ],
  [
    "autonexus/Login/autonexus-logo.jpg",
    "public/assets/img/autonexus-logo.jpg"
  ],
  [
    "autonexus/Login/car-image.jpg",
    "public/assets/img/car-image.jpg"
  ],
  [
    "autonexus/Login/hero.png",
    "public/assets/img/hero.png"
  ],
  [
    "autonexus/Login/script.js",
    "public/assets/js/script_9.js"
  ],
  [
    "autonexus/Login/styles.css",
    "public/assets/css/styles_7.css"
  ],
  [
    "autonexus/register/styles.css",
    "public/assets/css/styles_8.css"
  ]
]

## Inventory of your original upload
See the separate CSV `inventory.csv`.
