╔════════════════════════════════════════════════════════════════════════════╗
║                    FIX FORBIDDEN ERROR - QUICK START                       ║
╚════════════════════════════════════════════════════════════════════════════╝

YOUR SERVER DETAILS:
  IP Address: 217.196.54.155
  SSH Port: 65002
  Username: u448576780

╔════════════════════════════════════════════════════════════════════════════╗
║                              METHOD 1: SSH                                 ║
╚════════════════════════════════════════════════════════════════════════════╝

STEP 1: Connect to Server
--------------------------
Windows (PowerShell/CMD):
  ssh -p 65002 u448576780@217.196.54.155

Or double-click: connect-server.bat

Mac/Linux:
  ssh -p 65002 u448576780@217.196.54.155

Enter your password when prompted.


STEP 2: Navigate to Laravel Project
------------------------------------
cd ~/public_html
# or if different location:
cd ~/domains/theartisticbd.com/public_html

Verify you're in the right place:
  ls -la
# You should see: artisan, composer.json, app/, public/, etc.


STEP 3: Run the Fix Script
---------------------------
Option A - Use the automated script:
  bash fix-forbidden.sh

Option B - Manual commands (copy all at once):
  chmod -R 775 storage bootstrap/cache && \
  php artisan config:clear && \
  php artisan cache:clear && \
  php artisan route:clear && \
  php artisan view:clear && \
  php artisan optimize:clear && \
  php artisan storage:link && \
  echo "✓ All fixes applied!"


STEP 4: Test
------------
Open your browser and try to update settings:
  https://theartisticbd.com/admin/setting/system


╔════════════════════════════════════════════════════════════════════════════╗
║                   METHOD 2: HOSTING CONTROL PANEL                          ║
╚════════════════════════════════════════════════════════════════════════════╝

If SSH doesn't work or you prefer GUI:

1. LOGIN TO CONTROL PANEL
   - Go to your hosting control panel (cPanel/Plesk/Hostinger Panel)
   - Login with your credentials

2. OPEN FILE MANAGER
   - Find "File Manager" or "Files" section
   - Navigate to your website root directory
   - You should see: public_html or domains/theartisticbd.com/public_html

3. FIX PERMISSIONS FOR 'storage' FOLDER
   - Find the 'storage' folder
   - Right-click → Properties/Permissions
   - Set permissions to: 775 (or check Read, Write, Execute)
   - Apply to subdirectories: YES
   - Click Save/Apply

4. FIX PERMISSIONS FOR 'bootstrap/cache'
   - Find the 'bootstrap' folder
   - Open it and find 'cache' folder
   - Right-click → Properties/Permissions
   - Set permissions to: 775
   - Click Save/Apply

5. CHECK .env FILE
   - Look for .env file in root directory
   - If missing, rename .env.example to .env
   - Right-click .env → Edit
   - Make sure these lines exist:
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://theartisticbd.com
   - Save the file

6. USE TERMINAL (if available in control panel)
   - Find "Terminal" or "SSH Terminal" option
   - Click to open terminal
   - Run these commands:
     cd ~/public_html
     php artisan config:clear
     php artisan cache:clear
     php artisan route:clear
     php artisan view:clear
     php artisan storage:link

7. TEST
   - Try updating settings again in browser


╔════════════════════════════════════════════════════════════════════════════╗
║                         TROUBLESHOOTING                                    ║
╚════════════════════════════════════════════════════════════════════════════╝

STILL GETTING FORBIDDEN?

Check #1: Demo Mode
-------------------
SSH command:
  cat config/services.php | grep -A 3 "demo"

Should show:
  'demo' => [
      'enabled' => false,
  ],

If it says 'enabled' => true:
  nano config/services.php
  Change to: 'enabled' => false
  Save (Ctrl+X, Y, Enter)
  Run: php artisan config:clear


Check #2: View Error Logs
--------------------------
  tail -n 100 storage/logs/laravel.log

This will show the actual error causing the forbidden response.


Check #3: Server Error Logs
----------------------------
Ask your hosting provider to check:
- ModSecurity logs
- Apache/Nginx error logs
- PHP error logs

Tell them: "POST requests to /admin/setting/update return 403 Forbidden"


Check #4: PHP Version
---------------------
  php -v

Laravel requires PHP 8.0 or higher.
If lower, contact your hosting provider to upgrade PHP.


Check #5: .htaccess File
-------------------------
Make sure public/.htaccess exists and contains Laravel's default rules.


╔════════════════════════════════════════════════════════════════════════════╗
║                    DIAGNOSTIC TOOLS INCLUDED                               ║
╚════════════════════════════════════════════════════════════════════════════╝

1. server-check.php
   - Upload to public/ directory
   - Access: https://theartisticbd.com/server-check.php
   - View full diagnostic report in browser
   - ⚠️ DELETE after checking!

2. fix-forbidden.sh
   - Automated fix script
   - Upload to Laravel root
   - Run: bash fix-forbidden.sh
   - Fixes all common issues

3. server-diagnostic.sh
   - Detailed diagnostic script
   - Run: bash server-diagnostic.sh
   - Shows all configuration details


╔════════════════════════════════════════════════════════════════════════════╗
║                         CONTACT SUPPORT                                    ║
╚════════════════════════════════════════════════════════════════════════════╝

If nothing works, contact your hosting provider with this message:

  "Hi, I'm getting 403 Forbidden errors when submitting POST requests to
  /admin/setting/update on my Laravel application. The issue appears to be
  related to either ModSecurity rules, PHP restrictions, or file permissions.
  
  Server details:
  - IP: 217.196.54.155
  - Port: 65002
  - Account: u448576780
  
  Can you please:
  1. Check ModSecurity logs for blocked requests
  2. Verify PHP is allowed to write to storage directory
  3. Check if there are any POST request restrictions
  4. Review Apache/Nginx error logs for this domain
  
  Thank you!"


╔════════════════════════════════════════════════════════════════════════════╗
║                           SUCCESS CHECKLIST                                ║
╚════════════════════════════════════════════════════════════════════════════╝

After fixing, verify these:

□ Can login to admin panel: https://theartisticbd.com/admin
□ Can view settings page: https://theartisticbd.com/admin/setting/system
□ Can update and save settings without "Forbidden" error
□ Changes are saved in database
□ No error messages in storage/logs/laravel.log

If all checked, problem is solved! ✓


╔════════════════════════════════════════════════════════════════════════════╗
║                          NEED MORE HELP?                                   ║
╚════════════════════════════════════════════════════════════════════════════╝

If you encounter any issues:
1. Copy the exact error message
2. Run: tail -n 50 storage/logs/laravel.log
3. Copy the output
4. Share with me for further assistance

Good luck! 🚀
