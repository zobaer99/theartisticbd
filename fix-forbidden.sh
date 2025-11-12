#!/bin/bash

echo "======================================"
echo "LARAVEL FORBIDDEN ERROR FIX SCRIPT"
echo "======================================"
echo ""

# Find Laravel installation
if [ ! -f "artisan" ]; then
    echo "❌ Error: Laravel not found in current directory"
    echo "Please navigate to your Laravel root directory first"
    echo "Common locations:"
    echo "  cd ~/public_html"
    echo "  cd ~/domains/theartisticbd.com/public_html"
    echo "  cd /home/u448576780/public_html"
    exit 1
fi

echo "✓ Laravel installation found"
echo ""

echo "1. Fixing file permissions..."
echo "   - Setting storage directory permissions..."
chmod -R 775 storage 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✓ storage permissions updated"
else
    echo "   ⚠ Could not update storage permissions (may need sudo)"
fi

echo "   - Setting bootstrap/cache permissions..."
chmod -R 775 bootstrap/cache 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✓ bootstrap/cache permissions updated"
else
    echo "   ⚠ Could not update bootstrap/cache permissions (may need sudo)"
fi

echo ""
echo "2. Checking .env file..."
if [ -f ".env" ]; then
    echo "   ✓ .env file exists"
    
    # Check APP_ENV
    if grep -q "APP_ENV=" .env; then
        echo "   ✓ APP_ENV is set"
    else
        echo "   ⚠ APP_ENV not found in .env"
    fi
    
    # Check APP_DEBUG
    if grep -q "APP_DEBUG=" .env; then
        echo "   ✓ APP_DEBUG is set"
    else
        echo "   ⚠ APP_DEBUG not found in .env"
    fi
else
    echo "   ❌ .env file missing!"
    if [ -f ".env.example" ]; then
        echo "   Creating .env from .env.example..."
        cp .env.example .env
        echo "   ✓ .env file created"
        echo "   ⚠ Please edit .env and set your database credentials"
    else
        echo "   ❌ .env.example also missing!"
    fi
fi

echo ""
echo "3. Clearing Laravel caches..."

echo "   - Clearing configuration cache..."
php artisan config:clear 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✓ Configuration cache cleared"
else
    echo "   ⚠ Could not clear config cache"
fi

echo "   - Clearing application cache..."
php artisan cache:clear 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✓ Application cache cleared"
else
    echo "   ⚠ Could not clear application cache"
fi

echo "   - Clearing route cache..."
php artisan route:clear 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✓ Route cache cleared"
else
    echo "   ⚠ Could not clear route cache"
fi

echo "   - Clearing view cache..."
php artisan view:clear 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✓ View cache cleared"
else
    echo "   ⚠ Could not clear view cache"
fi

echo ""
echo "4. Creating storage link..."
php artisan storage:link 2>/dev/null
if [ $? -eq 0 ]; then
    echo "   ✓ Storage link created"
else
    echo "   ⚠ Storage link may already exist or command failed"
fi

echo ""
echo "5. Checking directory structure..."
required_dirs=(
    "storage/app"
    "storage/app/public"
    "storage/framework"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "storage/logs"
    "bootstrap/cache"
)

for dir in "${required_dirs[@]}"; do
    if [ -d "$dir" ]; then
        echo "   ✓ $dir exists"
    else
        echo "   ❌ $dir is missing!"
        mkdir -p "$dir"
        echo "   ✓ Created $dir"
    fi
done

echo ""
echo "6. Final permission check..."
echo "   storage permissions: $(stat -c %a storage 2>/dev/null || stat -f %A storage 2>/dev/null || echo 'unknown')"
echo "   bootstrap/cache permissions: $(stat -c %a bootstrap/cache 2>/dev/null || stat -f %A bootstrap/cache 2>/dev/null || echo 'unknown')"

echo ""
echo "======================================"
echo "✓ SCRIPT COMPLETED!"
echo "======================================"
echo ""
echo "Now test your settings update at:"
echo "https://theartisticbd.com/admin/setting/update"
echo ""
echo "If still forbidden, run these commands:"
echo "  tail -f storage/logs/laravel.log"
echo ""
echo "Or check demo mode:"
echo "  cat config/services.php | grep -A 3 'demo'"
echo ""
