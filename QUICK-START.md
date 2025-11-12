# 🚀 Quick Start Guide - SSH Server Access

## For Windows Users

### Method 1: Interactive Menu (Recommended)
```batch
1. Double-click: connect-server.bat
2. Select option 1
3. Enter your password
4. Once connected:
   cd ~/public_html
   bash fix-forbidden.sh
```

### Method 2: Direct Command
```batch
1. Open PowerShell or CMD
2. Run: ssh -p 65002 u448576780@217.196.54.155
3. Enter password
4. Navigate: cd ~/public_html
5. Fix: bash fix-forbidden.sh
```

---

## For Mac/Linux Users

### Method 1: Master Script (Recommended)
```bash
bash server-manager.sh
# Select option 1, then follow prompts
```

### Method 2: Direct SSH
```bash
ssh -p 65002 u448576780@217.196.54.155
cd ~/public_html
bash fix-forbidden.sh
```

---

## 🎯 What You Need

- **Server IP**: 217.196.54.155
- **Port**: 65002
- **Username**: u448576780
- **Password**: [You have this]

---

## 🔧 Available Tools

| Tool | Purpose | When to Use |
|------|---------|-------------|
| `connect-server.bat` | Connect from Windows | Initial connection |
| `server-manager.sh` | Master control script | All operations |
| `fix-forbidden.sh` | Fix permissions & cache | Forbidden errors |
| `server-health-check.sh` | Full diagnostics | Regular checkups |
| `server-diagnostic.sh` | Quick check | Fast status |

---

## 📝 Common Commands

### After Connecting to Server:

#### Navigate to project:
```bash
cd ~/public_html
```

#### Run automated fix:
```bash
bash fix-forbidden.sh
```

#### Check server health:
```bash
bash server-health-check.sh
```

#### View recent logs:
```bash
tail -n 50 storage/logs/laravel.log
```

#### Clear all caches manually:
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## ✅ Success Checklist

After running fixes, verify:

- [ ] Can access admin: https://theartisticbd.com/admin
- [ ] Can view settings: https://theartisticbd.com/admin/setting/system
- [ ] Can update settings without "Forbidden" error
- [ ] No errors in: `tail storage/logs/laravel.log`
- [ ] Health check passes: `bash server-health-check.sh`

---

## 🆘 Need Help?

1. **Quick Reference**: QUICK-FIX-GUIDE.txt
2. **Full Guide**: README-FIX-FORBIDDEN.txt
3. **Implementation Details**: SSH-IMPLEMENTATION-GUIDE.txt
4. **Summary**: IMPLEMENTATION-SUMMARY.md

---

## 🎉 That's It!

You're ready to connect and fix the forbidden error!

Start with: `connect-server.bat` (Windows) or `bash server-manager.sh` (Mac/Linux)
