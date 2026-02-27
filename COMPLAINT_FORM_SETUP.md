# Customer Complaint Form - Asset Path Configuration

## Overview
This document explains how the customer complaint form CSS and assets are served in the AutoNexus application running on XAMPP/Windows.

## File Locations

### View File
- **Path**: `app/views/customer/complaint/index.php`
- **Purpose**: HTML structure for the complaint form

### CSS File
- **Path**: `public/assets/css/customer/complaint.css`
- **Purpose**: Styles for the complaint form

### JavaScript File
- **Path**: `public/assets/js/customer/complaint.js`
- **Purpose**: Client-side validation and interactivity

### Controller
- **Path**: `app/controllers/customer/ComplaintController.php`
- **Purpose**: Handles form submission and business logic

## How Asset Paths Work

### Directory Structure
```
C:\xampp\htdocs\autonexus\          (Project root)
├── .htaccess                        (Root rewrite rules)
├── app\
│   ├── controllers\
│   │   └── customer\
│   │       └── ComplaintController.php
│   └── views\
│       └── customer\
│           └── complaint\
│               └── index.php
├── public\
│   ├── .htaccess                    (Public folder rewrite rules)
│   ├── index.php                    (Front controller)
│   └── assets\
│       ├── css\
│       │   └── customer\
│       │       └── complaint.css    ✅ CSS file location
│       └── js\
│           └── customer\
│               └── complaint.js     ✅ JS file location
└── config\
    └── config.php                   (BASE_URL definition)
```

### URL Resolution

#### 1. Configuration (config.php)
```php
define('BASE_URL', 'http://localhost/autonexus');
```

#### 2. Root .htaccess (autonexus/.htaccess)
```apache
RewriteEngine On
RewriteBase /autonexus/

# Let real files/folders pass through (CSS, JS, images)
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Send everything else to /public/index.php
RewriteRule ^ public/index.php [QSA,L]
```

**Explanation**:
- `RewriteBase /autonexus/` - Sets the base path for all rewrites
- Files that exist (CSS, JS, images) are served directly
- Non-existent paths are routed to `public/index.php`

#### 3. Public .htaccess (autonexus/public/.htaccess)
```apache
RewriteEngine On
RewriteBase /autonexus/

RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

RewriteRule ^ index.php [QSA,L]
```

**Explanation**:
- Similar logic: static files pass through, dynamic requests go to index.php

#### 4. View File Link Tag
```php
<link rel="stylesheet" href="<?= rtrim(BASE_URL, '/') ?>/public/assets/css/customer/complaint.css" />
```

**Resolves to**:
```
http://localhost/autonexus/public/assets/css/customer/complaint.css
```

## Testing Asset Accessibility

### Method 1: Direct Browser Access
1. Open browser
2. Navigate to: `http://localhost/autonexus/public/assets/css/customer/complaint.css`
3. **Expected**: CSS file content should display
4. **If 404**: Check file exists at correct path and .htaccess is working

### Method 2: Browser Developer Tools
1. Open complaint form: `http://localhost/autonexus/customer/complaint`
2. Open Developer Tools (F12)
3. Go to Network tab
4. Refresh page
5. Look for `complaint.css` in the list
6. **Expected**: Status 200, Type: stylesheet
7. **If 404**: Check the requested URL in the Network tab

### Method 3: Check in Page Source
1. Open complaint form
2. Right-click → View Page Source
3. Find the `<link>` tag for complaint.css
4. Click the href URL
5. **Expected**: CSS file opens in new tab

## Common Issues and Solutions

### Issue 1: CSS Not Loading (404 Error)
**Symptoms**: Form appears unstyled, plain HTML
**Solutions**:
1. Verify file exists: `C:\xampp\htdocs\autonexus\public\assets\css\customer\complaint.css`
2. Check Apache is running in XAMPP
3. Verify `mod_rewrite` is enabled in Apache
4. Check `.htaccess` files are being processed (verify in httpd.conf: `AllowOverride All`)

### Issue 2: Wrong Path in View File
**Symptoms**: Console shows 404 for CSS file
**Solutions**:
1. Ensure link uses: `<?= rtrim(BASE_URL, '/') ?>/public/assets/css/customer/complaint.css`
2. Do NOT use: `<?= BASE_URL ?>/assets/css/customer/complaint.css` (missing /public/)
3. Verify BASE_URL in config.php matches your XAMPP setup

### Issue 3: Permissions (Less common on Windows)
**Symptoms**: 403 Forbidden error
**Solutions**:
1. Check file permissions allow reading
2. Ensure XAMPP has read access to the directory

### Issue 4: Cache Issues
**Symptoms**: Old CSS still showing after changes
**Solutions**:
1. Hard refresh browser: Ctrl+Shift+R (Windows)
2. Clear browser cache
3. Use incognito/private mode for testing

## XAMPP-Specific Configuration

### Required XAMPP Settings

1. **Apache httpd.conf** (`C:\xampp\apache\conf\httpd.conf`):
   ```apache
   LoadModule rewrite_module modules/mod_rewrite.so
   ```
   (Should be uncommented)

2. **Document Root**:
   ```apache
   DocumentRoot "C:/xampp/htdocs"
   <Directory "C:/xampp/htdocs">
       Options Indexes FollowSymLinks Includes ExecCGI
       AllowOverride All
       Require all granted
   </Directory>
   ```
   **Important**: `AllowOverride All` enables .htaccess files

3. **Virtual Host** (Optional but recommended):
   ```apache
   <VirtualHost *:80>
       ServerName autonexus.local
       DocumentRoot "C:/xampp/htdocs/autonexus"
       <Directory "C:/xampp/htdocs/autonexus">
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```

## Troubleshooting Commands

### Windows Command Prompt
```cmd
# Navigate to project
cd C:\xampp\htdocs\autonexus

# Check if CSS file exists
dir /s complaint.css

# Check .htaccess files
type .htaccess
type public\.htaccess
```

### Test Rewrite Rules
1. Access: `http://localhost/autonexus/public/assets/css/customer/complaint.css`
2. Should return CSS content (not 404)

## Routes Configuration

The complaint form routes are defined in `public/index.php`:

```php
//customer - complaint
$router->get('/customer/complaint', [\app\controllers\customer\ComplaintController::class, 'index']);
$router->post('/customer/complaint', [\app\controllers\customer\ComplaintController::class, 'store']);
```

Access the form at: `http://localhost/autonexus/customer/complaint`

## Why This Configuration Works

1. **RewriteBase**: Tells Apache where the application root is (`/autonexus/`)
2. **File Pass-Through**: Static files (CSS, JS, images) are served directly by Apache
3. **Front Controller**: Dynamic routes go through `public/index.php`
4. **Separation**: `public/` folder contains web-accessible files, `app/` is protected
5. **Consistency**: All customer forms use the same pattern

## Additional Notes

- The CSS file includes extensive comments explaining the styling
- The JavaScript file includes form validation and auto-fill functionality
- The controller includes proper validation and security checks
- All paths use BASE_URL for portability across environments
- The form follows the same pattern as other customer forms (rate-service, booking, etc.)

## Success Verification Checklist

- [x] CSS file created at: `public/assets/css/customer/complaint.css`
- [x] JS file created at: `public/assets/js/customer/complaint.js`
- [x] View file created at: `app/views/customer/complaint/index.php`
- [x] Controller created at: `app/controllers/customer/ComplaintController.php`
- [x] Routes added in: `public/index.php`
- [x] .htaccess files properly configured
- [x] Documentation provided

**Final Test**: Open `http://localhost/autonexus/customer/complaint` in browser and verify:
1. Form displays with proper styling (not plain HTML)
2. No 404 errors in browser console
3. Form elements are styled with red brand colors
4. Buttons have hover effects
5. Form is responsive

---

**Document Created**: 2024
**Last Updated**: 2024
**Maintained By**: AutoNexus Development Team
