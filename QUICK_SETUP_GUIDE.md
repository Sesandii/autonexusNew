# Quick Setup Guide - Copy & Paste Solution

## TL;DR - The Fix
The root `.htaccess` file has been updated to redirect `/autonexus/assets/...` URLs to `/autonexus/public/assets/...`. This solves the 404 errors for CSS, JS, and image files.

---

## Copy & Paste: Root .htaccess Configuration

**File:** `/autonexus/.htaccess` (already updated in this repository)

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

---

## Copy & Paste: Enable mod_rewrite in XAMPP

**Step 1:** Open `C:\xampp\apache\conf\httpd.conf`

**Step 2:** Find this line (around line 150):
```apache
#LoadModule rewrite_module modules/mod_rewrite.so
```

**Step 3:** Remove the `#` to uncomment it:
```apache
LoadModule rewrite_module modules/mod_rewrite.so
```

**Step 4:** Find the `<Directory "C:/xampp/htdocs">` section (around line 230):
```apache
<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride None
    Require all granted
</Directory>
```

**Step 5:** Change `AllowOverride None` to `AllowOverride All`:
```apache
<Directory "C:/xampp/htdocs">
    Options Indexes FollowSymLinks Includes ExecCGI
    AllowOverride All
    Require all granted
</Directory>
```

**Step 6:** Save the file and restart Apache in XAMPP Control Panel

---

## Copy & Paste: Asset URL Patterns in PHP Views

Both patterns now work correctly:

### Pattern 1: With "public/" (existing code - no changes needed)
```php
<link rel="stylesheet" href="<?= BASE_URL ?>/public/assets/css/customer/complaint.css">
<script src="<?= BASE_URL ?>/public/assets/js/customer/script.js"></script>
<img src="<?= BASE_URL ?>/public/assets/img/logo.png" alt="Logo">
```

### Pattern 2: Without "public/" (new, also works)
```php
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/customer/complaint.css">
<script src="<?= BASE_URL ?>/assets/js/customer/script.js"></script>
<img src="<?= BASE_URL ?>/assets/img/logo.png" alt="Logo">
```

---

## Verify the Fix Works

### Test 1: Direct URL Access
Open your browser and try these URLs:
- `http://localhost/autonexus/assets/css/customer/complaint.css` ✅ Should work
- `http://localhost/autonexus/public/assets/css/customer/complaint.css` ✅ Should also work

### Test 2: Use the Test Page
Visit: `http://localhost/autonexus/test-assets.html`

This page:
- Loads CSS using the `/autonexus/assets/...` pattern
- Shows a styled complaint form
- Includes testing instructions

### Test 3: Check Network Tab
1. Open any page with CSS (e.g., complaint form)
2. Press F12 to open DevTools
3. Go to Network tab
4. Refresh the page
5. Look for CSS files - they should show status **200 OK** (not 404)

---

## Troubleshooting Checklist

If assets still don't load:

- [ ] Is `mod_rewrite` enabled in `httpd.conf`?
- [ ] Is `AllowOverride All` set in `httpd.conf`?
- [ ] Did you restart Apache after making changes?
- [ ] Is the `.htaccess` file present in `/autonexus/` directory?
- [ ] Is `BASE_URL` set to `/autonexus` in `config/config.php`?
- [ ] Did you clear browser cache (Ctrl+F5)?
- [ ] Does the CSS file physically exist in `public/assets/css/...`?

---

## What Changed

### Before
- Request: `http://localhost/autonexus/assets/css/customer/complaint.css`
- Result: **404 Not Found** ❌

### After
- Request: `http://localhost/autonexus/assets/css/customer/complaint.css`
- Rewritten to: `http://localhost/autonexus/public/assets/css/customer/complaint.css`
- Result: **200 OK** ✅ File served successfully

---

## Files Modified/Created

1. **Updated:** `.htaccess` - Added asset rewrite rule
2. **Created:** `public/assets/css/customer/complaint.css` - Example CSS file
3. **Created:** `public/test-assets.html` - Test page to verify setup
4. **Created:** `XAMPP_STATIC_ASSETS_SETUP.md` - Detailed documentation
5. **Created:** `QUICK_SETUP_GUIDE.md` - This quick reference (you are here)

---

## Need More Help?

See the detailed guide: `XAMPP_STATIC_ASSETS_SETUP.md`

This includes:
- Detailed explanation of the problem
- Step-by-step XAMPP configuration
- Complete troubleshooting guide
- Apache configuration examples
- Directory structure diagrams
