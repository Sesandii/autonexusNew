#!/bin/bash
# Validation script to check if the static assets fix is properly configured

echo "=== AutoNexus Static Assets Configuration Validation ==="
echo ""

# Check if .htaccess exists in root
if [ -f ".htaccess" ]; then
    echo "✅ Root .htaccess file exists"
    if grep -q "RewriteRule.*assets.*public/assets" .htaccess; then
        echo "✅ Asset rewrite rule is present in root .htaccess"
    else
        echo "❌ Asset rewrite rule NOT found in root .htaccess"
    fi
else
    echo "❌ Root .htaccess file NOT found"
fi

echo ""

# Check if public/.htaccess exists
if [ -f "public/.htaccess" ]; then
    echo "✅ public/.htaccess file exists"
else
    echo "❌ public/.htaccess file NOT found"
fi

echo ""

# Check if test page exists
if [ -f "public/test-assets.html" ]; then
    echo "✅ Test page (public/test-assets.html) exists"
else
    echo "❌ Test page NOT found"
fi

echo ""

# Check if example complaint.css exists
if [ -f "public/assets/css/customer/complaint.css" ]; then
    echo "✅ Example complaint.css file exists"
else
    echo "❌ Example complaint.css file NOT found"
fi

echo ""

# Check if documentation exists
if [ -f "XAMPP_STATIC_ASSETS_SETUP.md" ]; then
    echo "✅ Detailed setup documentation exists"
else
    echo "❌ Detailed setup documentation NOT found"
fi

if [ -f "QUICK_SETUP_GUIDE.md" ]; then
    echo "✅ Quick setup guide exists"
else
    echo "❌ Quick setup guide NOT found"
fi

echo ""

# Check BASE_URL configuration
if [ -f "config/config.php" ]; then
    echo "✅ config/config.php exists"
    if grep -q "const BASE_URL" config/config.php; then
        BASE_URL=$(grep "const BASE_URL" config/config.php | grep -o "'/[^']*'" | tr -d "'")
        echo "   BASE_URL is set to: $BASE_URL"
        if [ "$BASE_URL" = "/autonexus" ]; then
            echo "✅ BASE_URL is correctly set to /autonexus"
        else
            echo "⚠️  BASE_URL is set to $BASE_URL (expected /autonexus)"
        fi
    fi
else
    echo "❌ config/config.php NOT found"
fi

echo ""
echo "=== Summary ==="
echo "The configuration files have been updated to support both URL patterns:"
echo "  1. /autonexus/assets/... (rewritten to /autonexus/public/assets/...)"
echo "  2. /autonexus/public/assets/... (direct access)"
echo ""
echo "Next steps on XAMPP/Windows:"
echo "  1. Ensure mod_rewrite is enabled in Apache"
echo "  2. Set AllowOverride All in httpd.conf"
echo "  3. Restart Apache"
echo "  4. Test with: http://localhost/autonexus/test-assets.html"
echo ""
echo "See QUICK_SETUP_GUIDE.md for copy-paste instructions."
