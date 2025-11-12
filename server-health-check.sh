#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
MAGENTA='\033[0;35m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                  LARAVEL SERVER HEALTH CHECK TOOL                          ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Check if running in Laravel directory
if [ ! -f "artisan" ]; then
    echo -e "${RED}✗ Error: Not in Laravel root directory${NC}"
    echo "Please navigate to your Laravel project root first"
    echo ""
    echo "Try:"
    echo "  cd ~/public_html"
    echo "  cd ~/domains/theartisticbd.com/public_html"
    exit 1
fi

echo -e "${GREEN}✓ Laravel installation detected${NC}"
echo ""

# Initialize counters
TOTAL_CHECKS=0
PASSED_CHECKS=0
FAILED_CHECKS=0
WARNING_CHECKS=0

# Function to print check result
print_check() {
    local status=$1
    local message=$2
    TOTAL_CHECKS=$((TOTAL_CHECKS + 1))
    
    case $status in
        "pass")
            echo -e "${GREEN}✓${NC} $message"
            PASSED_CHECKS=$((PASSED_CHECKS + 1))
            ;;
        "fail")
            echo -e "${RED}✗${NC} $message"
            FAILED_CHECKS=$((FAILED_CHECKS + 1))
            ;;
        "warn")
            echo -e "${YELLOW}⚠${NC} $message"
            WARNING_CHECKS=$((WARNING_CHECKS + 1))
            ;;
    esac
}

# Section 1: System Information
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}1. SYSTEM INFORMATION${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

# PHP Version
if command -v php &> /dev/null; then
    PHP_VERSION=$(php -v | head -n 1)
    echo -e "PHP Version: ${GREEN}$PHP_VERSION${NC}"
    
    # Check if PHP version is 8.0 or higher
    PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
    PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")
    if [ "$PHP_MAJOR" -ge 8 ]; then
        print_check "pass" "PHP version is 8.0 or higher"
    else
        print_check "fail" "PHP version is below 8.0 (Laravel requirement)"
    fi
else
    print_check "fail" "PHP not found"
fi

# Laravel Version
if [ -f "artisan" ]; then
    LARAVEL_VERSION=$(php artisan --version 2>/dev/null)
    echo -e "Laravel Version: ${GREEN}$LARAVEL_VERSION${NC}"
    print_check "pass" "Laravel artisan is accessible"
else
    print_check "fail" "Laravel artisan not found"
fi

# Disk Space
DISK_USAGE=$(df -h . | tail -1 | awk '{print $5}' | sed 's/%//')
echo -e "Disk Usage: ${YELLOW}${DISK_USAGE}%${NC}"
if [ "$DISK_USAGE" -lt 80 ]; then
    print_check "pass" "Disk space is adequate (${DISK_USAGE}% used)"
elif [ "$DISK_USAGE" -lt 90 ]; then
    print_check "warn" "Disk space is getting low (${DISK_USAGE}% used)"
else
    print_check "fail" "Disk space is critically low (${DISK_USAGE}% used)"
fi

echo ""

# Section 2: File and Directory Permissions
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}2. FILE AND DIRECTORY PERMISSIONS${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

# Check storage directory
if [ -d "storage" ]; then
    STORAGE_PERMS=$(stat -c %a storage 2>/dev/null || stat -f %A storage 2>/dev/null)
    echo -e "storage permissions: ${YELLOW}$STORAGE_PERMS${NC}"
    if [ "$STORAGE_PERMS" = "775" ] || [ "$STORAGE_PERMS" = "777" ]; then
        print_check "pass" "storage directory has correct permissions"
    else
        print_check "fail" "storage directory permissions incorrect (should be 775)"
    fi
else
    print_check "fail" "storage directory not found"
fi

# Check bootstrap/cache
if [ -d "bootstrap/cache" ]; then
    CACHE_PERMS=$(stat -c %a bootstrap/cache 2>/dev/null || stat -f %A bootstrap/cache 2>/dev/null)
    echo -e "bootstrap/cache permissions: ${YELLOW}$CACHE_PERMS${NC}"
    if [ "$CACHE_PERMS" = "775" ] || [ "$CACHE_PERMS" = "777" ]; then
        print_check "pass" "bootstrap/cache has correct permissions"
    else
        print_check "fail" "bootstrap/cache permissions incorrect (should be 775)"
    fi
else
    print_check "fail" "bootstrap/cache directory not found"
fi

# Check required storage subdirectories
REQUIRED_DIRS=(
    "storage/app"
    "storage/app/public"
    "storage/framework"
    "storage/framework/cache"
    "storage/framework/sessions"
    "storage/framework/views"
    "storage/logs"
)

for dir in "${REQUIRED_DIRS[@]}"; do
    if [ -d "$dir" ]; then
        print_check "pass" "$dir exists"
    else
        print_check "fail" "$dir is missing"
    fi
done

echo ""

# Section 3: Environment Configuration
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}3. ENVIRONMENT CONFIGURATION${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

# Check .env file
if [ -f ".env" ]; then
    print_check "pass" ".env file exists"
    
    # Check important .env variables
    if grep -q "APP_ENV=" .env; then
        APP_ENV=$(grep "APP_ENV=" .env | cut -d '=' -f2)
        echo -e "  APP_ENV: ${YELLOW}$APP_ENV${NC}"
        print_check "pass" "APP_ENV is set"
    else
        print_check "fail" "APP_ENV not found in .env"
    fi
    
    if grep -q "APP_DEBUG=" .env; then
        APP_DEBUG=$(grep "APP_DEBUG=" .env | cut -d '=' -f2)
        echo -e "  APP_DEBUG: ${YELLOW}$APP_DEBUG${NC}"
        if [ "$APP_DEBUG" = "false" ]; then
            print_check "pass" "APP_DEBUG is set to false (production)"
        else
            print_check "warn" "APP_DEBUG is true (should be false in production)"
        fi
    else
        print_check "warn" "APP_DEBUG not found in .env"
    fi
    
    if grep -q "APP_URL=" .env; then
        APP_URL=$(grep "APP_URL=" .env | cut -d '=' -f2)
        echo -e "  APP_URL: ${YELLOW}$APP_URL${NC}"
        print_check "pass" "APP_URL is set"
    else
        print_check "warn" "APP_URL not found in .env"
    fi
    
    if grep -q "DB_DATABASE=" .env; then
        print_check "pass" "Database configuration exists"
    else
        print_check "fail" "Database configuration missing"
    fi
else
    print_check "fail" ".env file not found"
    if [ -f ".env.example" ]; then
        print_check "warn" ".env.example exists - copy it to .env"
    fi
fi

echo ""

# Section 4: Laravel Cache Status
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}4. LARAVEL CACHE STATUS${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

# Config cache
if [ -f "bootstrap/cache/config.php" ]; then
    print_check "warn" "Configuration is cached (run: php artisan config:clear)"
else
    print_check "pass" "Configuration cache is clear"
fi

# Route cache
if [ -f "bootstrap/cache/routes-v7.php" ]; then
    print_check "warn" "Routes are cached (run: php artisan route:clear)"
else
    print_check "pass" "Route cache is clear"
fi

echo ""

# Section 5: Storage Link
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}5. STORAGE SYMBOLIC LINK${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

if [ -L "public/storage" ]; then
    print_check "pass" "Storage symbolic link exists"
    LINK_TARGET=$(readlink public/storage)
    echo -e "  Links to: ${YELLOW}$LINK_TARGET${NC}"
else
    print_check "warn" "Storage symbolic link not found (run: php artisan storage:link)"
fi

echo ""

# Section 6: Demo Mode Check
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}6. DEMO MODE CHECK${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

if [ -f "config/services.php" ]; then
    if grep -q "'demo'" config/services.php; then
        DEMO_STATUS=$(grep -A 2 "'demo'" config/services.php | grep "enabled" | grep -o "true\|false")
        echo -e "Demo Mode: ${YELLOW}$DEMO_STATUS${NC}"
        if [ "$DEMO_STATUS" = "false" ]; then
            print_check "pass" "Demo mode is disabled"
        else
            print_check "fail" "Demo mode is enabled - this prevents updates!"
        fi
    else
        print_check "pass" "No demo mode configuration found"
    fi
else
    print_check "warn" "config/services.php not found"
fi

echo ""

# Section 7: Log Files
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}7. LOG FILES${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

if [ -f "storage/logs/laravel.log" ]; then
    LOG_SIZE=$(du -h storage/logs/laravel.log | cut -f1)
    echo -e "Laravel log size: ${YELLOW}$LOG_SIZE${NC}"
    
    # Check for recent errors
    RECENT_ERRORS=$(tail -n 100 storage/logs/laravel.log 2>/dev/null | grep -i "error" | wc -l)
    echo -e "Recent errors (last 100 lines): ${YELLOW}$RECENT_ERRORS${NC}"
    
    if [ "$RECENT_ERRORS" -eq 0 ]; then
        print_check "pass" "No recent errors in log"
    elif [ "$RECENT_ERRORS" -lt 10 ]; then
        print_check "warn" "$RECENT_ERRORS errors found in recent logs"
    else
        print_check "fail" "$RECENT_ERRORS errors found in recent logs - investigate!"
    fi
else
    print_check "warn" "Laravel log file not found"
fi

echo ""

# Section 8: Composer Dependencies
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"
echo -e "${CYAN}8. COMPOSER DEPENDENCIES${NC}"
echo -e "${CYAN}═══════════════════════════════════════════════════════════════════════════${NC}"

if [ -f "composer.json" ]; then
    print_check "pass" "composer.json exists"
    
    if [ -f "composer.lock" ]; then
        print_check "pass" "composer.lock exists"
    else
        print_check "warn" "composer.lock not found - run: composer install"
    fi
    
    if [ -d "vendor" ]; then
        print_check "pass" "vendor directory exists"
    else
        print_check "fail" "vendor directory missing - run: composer install"
    fi
else
    print_check "fail" "composer.json not found"
fi

echo ""

# Final Summary
echo -e "${BLUE}╔════════════════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║                            HEALTH CHECK SUMMARY                            ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════════════════╝${NC}"
echo ""
echo -e "Total Checks: ${CYAN}$TOTAL_CHECKS${NC}"
echo -e "Passed:       ${GREEN}$PASSED_CHECKS${NC}"
echo -e "Warnings:     ${YELLOW}$WARNING_CHECKS${NC}"
echo -e "Failed:       ${RED}$FAILED_CHECKS${NC}"
echo ""

# Calculate percentage
if [ "$TOTAL_CHECKS" -gt 0 ]; then
    PASS_PERCENT=$((PASSED_CHECKS * 100 / TOTAL_CHECKS))
    echo -e "Health Score: ${GREEN}${PASS_PERCENT}%${NC}"
    echo ""
fi

# Recommendations
if [ "$FAILED_CHECKS" -gt 0 ] || [ "$WARNING_CHECKS" -gt 0 ]; then
    echo -e "${YELLOW}╔════════════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${YELLOW}║                          RECOMMENDED ACTIONS                               ║${NC}"
    echo -e "${YELLOW}╚════════════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    
    if [ "$FAILED_CHECKS" -gt 0 ]; then
        echo -e "${RED}Critical issues found! Run the fix script:${NC}"
        echo "  bash fix-forbidden.sh"
        echo ""
    fi
    
    if [ "$WARNING_CHECKS" -gt 0 ]; then
        echo -e "${YELLOW}Warnings detected. Consider these actions:${NC}"
        echo "  1. Clear all caches: php artisan optimize:clear"
        echo "  2. Create storage link: php artisan storage:link"
        echo "  3. Review recent log entries: tail -n 50 storage/logs/laravel.log"
        echo ""
    fi
else
    echo -e "${GREEN}╔════════════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${GREEN}║                 ✓ ALL CHECKS PASSED - SERVER IS HEALTHY!                  ║${NC}"
    echo -e "${GREEN}╚════════════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
fi

# Show recent errors if any
if [ -f "storage/logs/laravel.log" ] && [ "$RECENT_ERRORS" -gt 0 ]; then
    echo ""
    echo -e "${YELLOW}Recent error samples from log:${NC}"
    echo -e "${CYAN}───────────────────────────────────────────────────────────────────────────${NC}"
    tail -n 100 storage/logs/laravel.log | grep -i "error" | tail -n 5
    echo -e "${CYAN}───────────────────────────────────────────────────────────────────────────${NC}"
    echo ""
    echo "View full log: tail -n 100 storage/logs/laravel.log"
fi

echo ""
echo -e "${BLUE}Health check completed at: $(date)${NC}"
echo ""
