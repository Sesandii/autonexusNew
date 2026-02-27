# XAMPP Static Assets Setup Guide

## Problem Fixed
The complaint form (and other pages) were showing unstyled because CSS files were returning 404 errors when accessed at URLs like:
- `http://localhost/autonexus/assets/css/customer/complaint.css`

## Root Cause
Static assets (CSS, JS, images) are physically located in the `public/assets/` directory, but some code was trying to access them at `/autonexus/assets/...` URLs (without the "public/" prefix).

## Solution Implemented

### 1. Updated Root `.htaccess` File
The root `.htaccess` file (located at `/autonexus/.htaccess`) now includes a rewrite rule to redirect asset requests:

```apache
RewriteEngine On
RewriteBase /autonexus/

# Redirect /autonexus/assets/ to /autonexus/public/assets/
RewriteRule ^assets/(.*)$ public/assets/$1 [L]

# Let real files/folders pass through
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Send everything else to /public/index.php
RewriteRule ^ public/index.php [QSA,L]

# Block internal folders
RewriteRule ^app/ - [R=404,L]
RewriteRule ^config/ - [R=404,L]
```

**Key Change:** Added `RewriteRule ^assets/(.*)$ public/assets/$1 [L]` at the top (before other rules).

This rule maps:
- `/autonexus/assets/css/customer/complaint.css` → `/autonexus/public/assets/css/customer/complaint.css`
- `/autonexus/assets/js/script.js` → `/autonexus/public/assets/js/script.js`
- `/autonexus/assets/img/logo.png` → `/autonexus/public/assets/img/logo.png`

### 2. How It Works

1. When a browser requests `http://localhost/autonexus/assets/css/customer/complaint.css`
2. Apache matches the pattern `^assets/(.*)$` 
3. It rewrites the request to `public/assets/css/customer/complaint.css`
4. The file is served from `/autonexus/public/assets/css/customer/complaint.css`

### 3. XAMPP Configuration (Windows)

#### Directory Structure
Your XAMPP installation should have this structure:
```
C:\xampp\htdocs\autonexus\
  ├── .htaccess              ← Root .htaccess (updated)
  ├── app/
  ├── config/
  ├── public/
  │   ├── .htaccess          ← Public .htaccess
  │   ├── index.php          ← Front controller
  │   └── assets/
  │       ├── css/
  │       ├── js/
  │       └── img/
  ├── vendor/
  └── README.md
```

#### Apache Configuration
Make sure `mod_rewrite` is enabled in XAMPP:

1. Open `C:\xampp\apache\conf\httpd.conf`
2. Find the line: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Remove the `#` to uncomment it: `LoadModule rewrite_module modules/mod_rewrite.so`
4. Find `AllowOverride None` under the `<Directory "C:/xampp/htdocs">` section
5. Change it to: `AllowOverride All`
6. Save the file and restart Apache

#### Verify Setup
1. Start Apache in XAMPP Control Panel
2. Open browser and navigate to: `http://localhost/autonexus/`
3. Test asset loading by accessing:
   - `http://localhost/autonexus/assets/css/customer/complaint.css`
   - `http://localhost/autonexus/public/assets/css/customer/complaint.css`
   
   Both URLs should work and return the CSS file.

### 4. Using Asset Paths in Views

You can now reference assets in two ways (both will work):

#### Method 1: With "public/" prefix (recommended for existing code)
```php
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/customer/complaint.css">
<script src="<?= BASE_URL ?>/public/assets/js/customer/script.js"></script>
<img src="<?= BASE_URL ?>/public/assets/img/logo.png" alt="Logo">
```

#### Method 2: Without "public/" prefix (will be rewritten automatically)
```php
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/customer/complaint.css">
<script src="<?= BASE_URL ?>/assets/js/customer/script.js"></script>
<img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo">
```

### 5. Troubleshooting

#### Assets still return 404
1. Verify Apache `mod_rewrite` is enabled
2. Check that `.htaccess` files exist in both root and `public/` directories
3. Ensure `AllowOverride All` is set in Apache config
4. Restart Apache after making configuration changes
5. Clear browser cache (Ctrl+F5)

#### Permission Issues on Windows
1. Make sure XAMPP can read files in the `autonexus` folder
2. Check that the `public/assets/` directory and its contents are not read-only

#### Wrong BASE_URL
1. Open `config/config.php`
2. Verify that `BASE_URL` is set correctly:
   ```php
   const BASE_URL = '/autonexus';  // No trailing slash
   ```

### 6. Summary

✅ **What was fixed:**
- Added asset URL rewriting in root `.htaccess`
- Now `/autonexus/assets/...` URLs work correctly
- Both old and new asset URL patterns are supported

✅ **What you need to do:**
1. Ensure `mod_rewrite` is enabled in Apache
2. Set `AllowOverride All` in Apache config
3. Restart Apache
4. Clear browser cache

✅ **Testing:**
- Visit any page with CSS (e.g., complaint form)
- Open browser DevTools (F12) → Network tab
- Verify CSS files load with HTTP 200 status (not 404)

## Questions or Issues?
If assets still don't load after following these steps:
1. Check Apache error logs: `C:\xampp\apache\logs\error.log`
2. Verify the file exists: `C:\xampp\htdocs\autonexus\public\assets\css\customer\complaint.css`
3. Test with a direct URL: `http://localhost/autonexus/public/assets/css/customer/complaint.css`
