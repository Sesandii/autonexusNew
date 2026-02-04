#!/bin/bash
# Asset URL Mapping Test Script
# This script documents the expected behavior of asset URL mapping

echo "==================================="
echo "Asset URL Mapping Test"
echo "==================================="
echo ""

echo "Project Structure:"
echo "  /home/runner/work/autonexusNew/autonexusNew/"
echo "    ├── .htaccess (root - routes requests)"
echo "    └── public/"
echo "        ├── .htaccess (public - serves assets)"
echo "        ├── index.php (front controller)"
echo "        └── assets/"
echo "            ├── css/"
echo "            ├── js/"
echo "            └── img/"
echo ""

echo "Expected URL Mappings:"
echo "========================================"
echo "Browser URL → Physical File"
echo "========================================"
echo ""

# Test files
TEST_FILES=(
    "public/assets/css/customer/complaint.css"
    "public/assets/css/customer/dashboard.css"
    "public/assets/css/customer/sidebar.css"
)

for file in "${TEST_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "✓ File exists: $file"
        
        # Test pattern 1: /autonexus/public/assets/...
        echo "  URL Pattern 1: http://localhost/autonexus/$file"
        
        # Test pattern 2: /autonexus/assets/... (without public)
        shortpath="${file#public/}"
        echo "  URL Pattern 2: http://localhost/autonexus/$shortpath"
        echo ""
    else
        echo "✗ File missing: $file"
        echo ""
    fi
done

echo "========================================"
echo "How Views Should Reference Assets:"
echo "========================================"
echo ""
echo "In PHP views, use BASE_URL constant:"
echo ""
echo '<?php $base = rtrim(BASE_URL, "/"); ?>'
echo ""
echo "<!-- CSS -->"
echo '<link rel="stylesheet" href="<?= $base ?>/public/assets/css/customer/complaint.css">'
echo ""
echo "<!-- JavaScript -->"
echo '<script src="<?= $base ?>/public/assets/js/script.js"></script>'
echo ""
echo "<!-- Images -->"
echo '<img src="<?= $base ?>/public/assets/img/logo.png" alt="Logo">'
echo ""

echo "========================================"
echo ".htaccess Configuration Summary:"
echo "========================================"
echo ""
echo "Root .htaccess (/autonexusNew/.htaccess):"
echo "  1. Allow direct access to public/assets/* files"
echo "  2. Rewrite /assets/* → public/assets/*"
echo "  3. Route all other requests → public/index.php"
echo "  4. Block access to app/ and config/ folders"
echo ""
echo "Public .htaccess (/autonexusNew/public/.htaccess):"
echo "  1. Allow direct access to assets/* files"
echo "  2. Route all other requests → index.php"
echo ""

echo "========================================"
echo "Testing with Apache:"
echo "========================================"
echo ""
echo "1. Ensure mod_rewrite is enabled:"
echo "   sudo a2enmod rewrite"
echo "   sudo systemctl restart apache2"
echo ""
echo "2. Virtual host should point to project root:"
echo "   DocumentRoot /var/www/autonexusNew"
echo "   (NOT to the public/ folder)"
echo ""
echo "3. Test asset access:"
echo "   curl -I http://localhost/autonexus/public/assets/css/customer/complaint.css"
echo "   curl -I http://localhost/autonexus/assets/css/customer/complaint.css"
echo ""
echo "Both should return HTTP 200 OK"
echo ""
