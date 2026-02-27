#!/bin/bash
# Manual Validation Test for Asset URL Mapping
# This test verifies that .htaccess rules work as expected

PROJECT_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$PROJECT_ROOT"

echo "=========================================="
echo "Asset URL Mapping Validation Test"
echo "=========================================="
echo ""

# Check if .htaccess files exist
echo "1. Checking .htaccess files..."
if [ -f ".htaccess" ]; then
    echo "   ✓ Root .htaccess exists"
else
    echo "   ✗ Root .htaccess MISSING"
    exit 1
fi

if [ -f "public/.htaccess" ]; then
    echo "   ✓ Public .htaccess exists"
else
    echo "   ✗ Public .htaccess MISSING"
    exit 1
fi
echo ""

# Check key files exist
echo "2. Checking asset files..."
ASSET_FILES=(
    "public/assets/css/customer/complaint.css"
    "public/assets/css/customer/dashboard.css"
    "public/index.php"
)

ALL_EXIST=true
for file in "${ASSET_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "   ✓ $file"
    else
        echo "   ✗ $file MISSING"
        ALL_EXIST=false
    fi
done

if [ "$ALL_EXIST" = false ]; then
    exit 1
fi
echo ""

# Validate .htaccess syntax
echo "3. Validating .htaccess syntax..."

# Check for RewriteEngine
if grep -q "RewriteEngine On" .htaccess; then
    echo "   ✓ Root .htaccess has RewriteEngine On"
else
    echo "   ✗ Root .htaccess missing RewriteEngine On"
    exit 1
fi

# Check for assets rewrite rule
if grep -q "^RewriteRule.*assets.*public/assets" .htaccess; then
    echo "   ✓ Root .htaccess has assets rewrite rule"
else
    echo "   ✗ Root .htaccess missing assets rewrite rule"
    exit 1
fi

# Check for security rules
if grep -q "^RewriteRule.*app/.*404" .htaccess && grep -q "^RewriteRule.*config/.*404" .htaccess; then
    echo "   ✓ Root .htaccess blocks app/ and config/"
else
    echo "   ✗ Root .htaccess missing security rules"
    exit 1
fi
echo ""

# Check example view
echo "4. Checking example complaint view..."
if [ -f "app/views/customer/complaint/index.php" ]; then
    if grep -q "public/assets/css/customer/complaint.css" app/views/customer/complaint/index.php; then
        echo "   ✓ Complaint view uses correct asset path pattern"
    else
        echo "   ✗ Complaint view has incorrect asset path"
        exit 1
    fi
else
    echo "   ⚠ Complaint view not found (optional)"
fi
echo ""

# Check README documentation
echo "5. Checking documentation..."
if grep -q "Asset Linking" README.md; then
    echo "   ✓ README has Asset Linking documentation"
else
    echo "   ✗ README missing Asset Linking section"
    exit 1
fi
echo ""

echo "=========================================="
echo "✓ All validation checks passed!"
echo "=========================================="
echo ""
echo "The asset URL mapping fix is correctly configured."
echo ""
echo "To test with a web server (Apache/XAMPP):"
echo "1. Access: http://localhost/autonexus/public/assets/css/customer/complaint.css"
echo "2. Access: http://localhost/autonexus/assets/css/customer/complaint.css"
echo "Both should return HTTP 200 with CSS content"
echo ""
exit 0
