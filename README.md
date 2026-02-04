# Proposed MVC Layout

This folder contains a minimal MVC skeleton (no frameworks) and copies of your static assets where possible.

## Project Structure

```
autonexusNew/
├── .htaccess           # Root rewrite rules (routes to public/)
├── app/                # Application code (controllers, models, views)
├── config/             # Configuration files
├── public/             # Web-accessible directory (DOCUMENT ROOT)
│   ├── .htaccess       # Public folder rewrite rules
│   ├── index.php       # Front controller
│   └── assets/         # Static assets (CSS, JS, images)
│       ├── css/
│       ├── js/
│       └── img/
└── vendor/             # Composer dependencies
```

## Asset Linking (IMPORTANT)

### Correct Way to Reference Assets in Views

Always use the `BASE_URL` constant when linking to CSS, JavaScript, or images:

```php
<?php $base = rtrim(BASE_URL, '/'); ?>

<!-- CSS -->
<link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/dashboard.css">

<!-- JavaScript -->
<script src="<?= $base ?>/public/assets/js/script.js"></script>

<!-- Images -->
<img src="<?= $base ?>/public/assets/img/logo.png" alt="Logo">
```

### URL Mapping

The `.htaccess` configuration maps asset URLs as follows:

| View Reference | Browser URL | Physical File |
|----------------|-------------|---------------|
| `<?= BASE_URL ?>/public/assets/css/style.css` | `/autonexus/public/assets/css/style.css` | `public/assets/css/style.css` |
| `<?= BASE_URL ?>/assets/css/style.css` | `/autonexus/assets/css/style.css` | `public/assets/css/style.css` |

**Both patterns work!** You can use either:
- `/autonexus/public/assets/...` (explicit)
- `/autonexus/assets/...` (cleaner)

### For New Assets

When adding new CSS, JavaScript, or image files:

1. Place them in the appropriate `public/assets/` subdirectory:
   - CSS: `public/assets/css/{module}/`
   - JS: `public/assets/js/`
   - Images: `public/assets/img/`

2. Reference them in views using the BASE_URL pattern shown above

3. Example: Adding a complaint form CSS:
   ```php
   <!-- In app/views/customer/complaint/index.php -->
   <link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/complaint.css">
   ```

## What we did
- Extracted your uploaded zip to: `/mnt/data/user_project_raw`
- Collected CSS/JS/IMG assets and placed them in `public/assets/...`
- Created core MVC files: Router, Controller, Model, Database, front controller
- Added a sample Home controller + view and a layout
- Created `.htaccess` for Apache rewrite to `public/index.php`
- **Configured asset URL mapping for both `/public/assets/*` and `/assets/*` paths**

## Next steps
1. Point Apache DocumentRoot to `public/`.
2. Update `config/config.php` with your DB credentials.
3. For each existing page, create a Controller + View:
   - Route in `public/index.php`
   - Controller in `app/controllers`
   - View in `app/views/<module>/...`
   - (Optional) Model in `app/models` for DB ops

## Deployment Notes

### XAMPP/Windows Development
- Install in: `C:\xampp\htdocs\autonexus`
- Access via: `http://localhost/autonexus`
- DocumentRoot: `C:\xampp\htdocs\autonexus` (NOT public/)
- The root `.htaccess` handles routing to `public/` folder

### Production Deployment
For production, you have two options:

**Option A: DocumentRoot = project root** (like XAMPP)
- Point DocumentRoot to `/var/www/autonexus`
- The root `.htaccess` will route to `public/`
- Use `BASE_URL = /autonexus` or `/` depending on setup

**Option B: DocumentRoot = public folder** (recommended)
- Point DocumentRoot to `/var/www/autonexus/public`
- Set `RewriteBase /` in `public/.htaccess`
- Update `BASE_URL = ''` in config
- Most secure as app/ and config/ are outside web root

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
