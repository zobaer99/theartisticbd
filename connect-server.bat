@echo off
color 0A
title Server Connection Tool - theartisticbd.com

:MENU
cls
echo ================================================================================
echo              SSH SERVER CONNECTION AND TROUBLESHOOTING TOOL
echo ================================================================================
echo.
echo Server Details:
echo   IP Address: 217.196.54.155
echo   SSH Port: 65002
echo   Username: u448576780
echo.
echo ================================================================================
echo                                MAIN MENU
echo ================================================================================
echo.
echo 1. Connect to Server via SSH
echo 2. Show Quick Fix Commands
echo 3. View Documentation Files
echo 4. Test SSH Availability
echo 5. Exit
echo.
echo ================================================================================
echo.

set /p choice="Select an option (1-5): "

if "%choice%"=="1" goto CONNECT
if "%choice%"=="2" goto COMMANDS
if "%choice%"=="3" goto DOCS
if "%choice%"=="4" goto TEST
if "%choice%"=="5" goto EXIT
goto MENU

:CONNECT
cls
echo ================================================================================
echo                        CONNECTING TO SERVER
echo ================================================================================
echo.
echo Server: 217.196.54.155:65002
echo Username: u448576780
echo.
echo You will be prompted for your password...
echo After connecting, navigate to your Laravel project and run:
echo   cd ~/public_html
echo   bash fix-forbidden.sh
echo.
pause
echo.
ssh -p 65002 u448576780@217.196.54.155
echo.
echo Connection closed.
pause
goto MENU

:COMMANDS
cls
echo ================================================================================
echo              COMMANDS TO RUN ON SERVER AFTER CONNECTION
echo ================================================================================
echo.
echo 1. Navigate to Laravel project:
echo    cd ~/public_html
echo    # or: cd ~/domains/theartisticbd.com/public_html
echo.
echo 2. Run automated fix:
echo    bash fix-forbidden.sh
echo.
echo 3. Or run manual commands:
echo    chmod -R 775 storage bootstrap/cache
echo    php artisan config:clear
echo    php artisan cache:clear
echo    php artisan route:clear
echo    php artisan view:clear
echo    php artisan storage:link
echo.
echo 4. Check logs for errors:
echo    tail -n 50 storage/logs/laravel.log
echo.
echo 5. Verify demo mode is OFF:
echo    cat config/services.php ^| grep -A 3 'demo'
echo.
echo ================================================================================
pause
goto MENU

:DOCS
cls
echo ================================================================================
echo                        AVAILABLE DOCUMENTATION
echo ================================================================================
echo.
echo 1. README-FIX-FORBIDDEN.txt
echo    - Comprehensive guide to fix forbidden errors
echo    - Multiple methods (SSH, Control Panel, File Manager)
echo    - Detailed troubleshooting steps
echo.
echo 2. CONNECT_TO_SERVER.txt
echo    - Server connection instructions
echo    - SSH setup for different platforms
echo    - Navigation and basic commands
echo.
echo 3. QUICK-FIX-GUIDE.txt
echo    - Step-by-step quick fix guide
echo    - Copy-paste commands
echo    - Fast troubleshooting
echo.
echo 4. SERVER_SETUP_COMMANDS.txt
echo    - All server setup commands
echo    - Permission fixes
echo    - Laravel optimization commands
echo.
echo ================================================================================
echo.
echo These files are in the current directory.
echo Open them with any text editor (Notepad, VS Code, etc.)
echo.
pause
goto MENU

:TEST
cls
echo ================================================================================
echo                        TESTING SSH AVAILABILITY
echo ================================================================================
echo.
echo Checking if SSH client is available...
echo.

where ssh >nul 2>nul
if %errorlevel% equ 0 (
    echo [SUCCESS] SSH client is available!
    echo.
    ssh -V
    echo.
    echo You can connect to the server using option 1.
) else (
    echo [ERROR] SSH client not found!
    echo.
    echo Please install OpenSSH client:
    echo.
    echo For Windows 10/11:
    echo   1. Open Settings
    echo   2. Go to Apps ^> Optional Features
    echo   3. Click "Add a feature"
    echo   4. Search for "OpenSSH Client"
    echo   5. Install it
    echo.
    echo Or use PuTTY:
    echo   Download from: https://www.putty.org/
    echo.
)
echo.
echo ================================================================================
pause
goto MENU

:EXIT
cls
echo.
echo Thank you for using the Server Connection Tool!
echo.
echo For help, refer to:
echo   - README-FIX-FORBIDDEN.txt
echo   - QUICK-FIX-GUIDE.txt
echo.
timeout /t 2 >nul
exit
