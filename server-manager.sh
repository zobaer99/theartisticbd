#!/bin/bash

# Master script for server management
# This is the main entry point for all server operations

clear

cat << "EOF"
╔════════════════════════════════════════════════════════════════════════════╗
║                 THEARTISTICBD.COM - SERVER MANAGEMENT SUITE                ║
╚════════════════════════════════════════════════════════════════════════════╝
EOF

echo ""
echo "This is your central hub for server management and troubleshooting."
echo ""

# Check if we're on the server or local machine
if [ -f "artisan" ]; then
    # We're on the server
    echo "✓ Detected: Running on Laravel server"
    echo ""
    echo "What would you like to do?"
    echo ""
    echo "  1) Run Health Check (Recommended - check server status)"
    echo "  2) Run Automated Fix (Fix forbidden error and permissions)"
    echo "  3) Run Quick Diagnostics"
    echo "  4) View Recent Logs"
    echo "  5) Clear All Caches Manually"
    echo "  6) View All Available Tools"
    echo "  7) Exit"
    echo ""
    read -p "Select option (1-7): " choice
    
    case $choice in
        1)
            echo ""
            echo "Running comprehensive health check..."
            echo ""
            bash server-health-check.sh
            ;;
        2)
            echo ""
            echo "Running automated fix script..."
            echo ""
            bash fix-forbidden.sh
            ;;
        3)
            echo ""
            echo "Running quick diagnostics..."
            echo ""
            bash server-diagnostic.sh
            ;;
        4)
            echo ""
            echo "Showing last 50 lines of Laravel log..."
            echo "════════════════════════════════════════════════════════════════"
            tail -n 50 storage/logs/laravel.log 2>/dev/null || echo "No log file found"
            echo "════════════════════════════════════════════════════════════════"
            ;;
        5)
            echo ""
            echo "Clearing all caches..."
            php artisan config:clear && echo "✓ Config cache cleared"
            php artisan cache:clear && echo "✓ Application cache cleared"
            php artisan route:clear && echo "✓ Route cache cleared"
            php artisan view:clear && echo "✓ View cache cleared"
            php artisan optimize:clear && echo "✓ Optimization cache cleared"
            echo ""
            echo "All caches cleared successfully!"
            ;;
        6)
            echo ""
            echo "Available Tools on Server:"
            echo "════════════════════════════════════════════════════════════════"
            echo "  • server-health-check.sh    - Comprehensive health diagnostics"
            echo "  • fix-forbidden.sh          - Automated permission/cache fixes"
            echo "  • server-diagnostic.sh      - Quick diagnostic check"
            echo "  • server-connect-and-fix.sh - Connection helper (for local use)"
            echo ""
            echo "Available Documentation:"
            echo "════════════════════════════════════════════════════════════════"
            echo "  • SSH-IMPLEMENTATION-GUIDE.txt - Complete implementation guide"
            echo "  • README-FIX-FORBIDDEN.txt     - Forbidden error troubleshooting"
            echo "  • CONNECT_TO_SERVER.txt        - Server connection instructions"
            echo "  • QUICK-FIX-GUIDE.txt         - Quick reference guide"
            echo "  • SERVER_SETUP_COMMANDS.txt    - Manual setup commands"
            echo ""
            ;;
        7)
            echo "Goodbye!"
            exit 0
            ;;
        *)
            echo "Invalid option"
            exit 1
            ;;
    esac
else
    # We're on local machine
    echo "✓ Detected: Running on local machine"
    echo ""
    echo "What would you like to do?"
    echo ""
    echo "  1) Connect to Server (SSH)"
    echo "  2) Show Server Connection Details"
    echo "  3) Show Commands to Run on Server"
    echo "  4) View Documentation"
    echo "  5) Test SSH Connection"
    echo "  6) Exit"
    echo ""
    read -p "Select option (1-6): " choice
    
    case $choice in
        1)
            bash server-connect-and-fix.sh
            ;;
        2)
            echo ""
            echo "Server Connection Details:"
            echo "════════════════════════════════════════════════════════════════"
            echo "  IP Address: 217.196.54.155"
            echo "  SSH Port:   65002"
            echo "  Username:   u448576780"
            echo ""
            echo "Connection Command:"
            echo "  ssh -p 65002 u448576780@217.196.54.155"
            echo ""
            echo "Or use the connect-server.bat (Windows) or"
            echo "server-connect-and-fix.sh (Linux/Mac) scripts"
            echo ""
            ;;
        3)
            echo ""
            echo "After connecting to server, navigate to project and run:"
            echo "════════════════════════════════════════════════════════════════"
            echo ""
            echo "# Navigate to project"
            echo "cd ~/public_html"
            echo ""
            echo "# Option 1: Use master script"
            echo "bash server-manager.sh"
            echo ""
            echo "# Option 2: Run health check"
            echo "bash server-health-check.sh"
            echo ""
            echo "# Option 3: Run automated fix"
            echo "bash fix-forbidden.sh"
            echo ""
            ;;
        4)
            echo ""
            echo "Available Documentation Files:"
            echo "════════════════════════════════════════════════════════════════"
            ls -1 *.txt 2>/dev/null | while read file; do
                echo "  • $file"
            done
            echo ""
            echo "View any file with: cat filename.txt"
            echo "Or open with text editor"
            echo ""
            ;;
        5)
            echo ""
            echo "Testing SSH availability..."
            if command -v ssh &> /dev/null; then
                echo "✓ SSH client is installed"
                ssh -V 2>&1 | head -n 1
                echo ""
                echo "You can connect to the server."
            else
                echo "✗ SSH client not found"
                echo ""
                echo "Please install OpenSSH client:"
                echo "  Windows: Settings > Apps > Optional Features > OpenSSH Client"
                echo "  Linux: sudo apt install openssh-client"
                echo "  Mac: SSH is pre-installed"
            fi
            echo ""
            ;;
        6)
            echo "Goodbye!"
            exit 0
            ;;
        *)
            echo "Invalid option"
            exit 1
            ;;
    esac
fi

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "For more help, see: SSH-IMPLEMENTATION-GUIDE.txt"
echo "════════════════════════════════════════════════════════════════"
echo ""
