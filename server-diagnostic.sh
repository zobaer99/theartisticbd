#!/bin/bash

echo "======================================"
echo "LARAVEL SERVER DIAGNOSTIC TOOL"
echo "======================================"
echo ""

echo "1. Checking Laravel Installation..."
if [ -f "artisan" ]; then
    echo "✓ Laravel detected"
else
    echo "✗ Laravel not found - run this from Laravel root directory"
    exit 1
fi

echo ""
echo "2. Checking .env file..."
if [ -f ".env" ]; then
    echo "✓ .env file exists"
    echo "   APP_ENV: $(grep APP_ENV .env | cut -d '=' -f2)"
    echo "   APP_DEBUG: $(grep APP_DEBUG .env | cut -d '=' -f2)"
    echo "   APP_URL: $(grep APP_URL .env | cut -d '=' -f2)"
else
    echo "✗ .env file missing!"
fi

echo ""
echo "3. Checking Permissions..."
echo "   storage permissions: $(stat -c %a storage 2>/dev/null || stat -f %A storage)"
echo "   bootstrap/cache permissions: $(stat -c %a bootstrap/cache 2>/dev/null || stat -f %A bootstrap/cache)"

echo ""
echo "4. Checking Storage Directories..."
for dir in storage/app storage/framework storage/logs storage/framework/cache storage/framework/sessions storage/framework/views; do
    if [ -d "$dir" ]; then
        echo "   ✓ $dir exists"
    else
        echo "   ✗ $dir missing"
    fi
done

echo ""
echo "5. Checking PHP Version..."
php -v | head -n 1

echo ""
echo "6. Checking Laravel Version..."
php artisan --version

echo ""
echo "7. Checking Config Cache..."
if [ -f "bootstrap/cache/config.php" ]; then
    echo "   Config is cached"
    echo "   Run: php artisan config:clear"
else
    echo "   ✓ Config not cached"
fi

echo ""
echo "8. Checking Route Cache..."
if [ -f "bootstrap/cache/routes-v7.php" ]; then
    echo "   Routes are cached"
    echo "   Run: php artisan route:clear"
else
    echo "   ✓ Routes not cached"
fi

echo ""
echo "======================================"
echo "RECOMMENDED ACTIONS:"
echo "======================================"
echo "1. chmod -R 775 storage"
echo "2. chmod -R 775 bootstrap/cache"
echo "3. php artisan config:clear"
echo "4. php artisan cache:clear"
echo "5. php artisan route:clear"
echo "6. php artisan view:clear"
echo ""
