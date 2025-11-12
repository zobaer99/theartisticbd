#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Server details
SERVER_IP="217.196.54.155"
SERVER_PORT="65002"
SERVER_USER="u448576780"

echo -e "${BLUE}╔════════════════════════════════════════════════════════════════════════════╗${NC}"
echo -e "${BLUE}║         SSH Server Connection and Forbidden Error Fix Tool                ║${NC}"
echo -e "${BLUE}╚════════════════════════════════════════════════════════════════════════════╝${NC}"
echo ""

# Function to show server details
show_server_details() {
    echo -e "${YELLOW}Server Details:${NC}"
    echo -e "  IP Address: ${GREEN}${SERVER_IP}${NC}"
    echo -e "  SSH Port: ${GREEN}${SERVER_PORT}${NC}"
    echo -e "  Username: ${GREEN}${SERVER_USER}${NC}"
    echo ""
}

# Function to test SSH connection
test_connection() {
    echo -e "${YELLOW}Testing SSH connection...${NC}"
    if command -v ssh &> /dev/null; then
        echo -e "${GREEN}✓ SSH client is installed${NC}"
        return 0
    else
        echo -e "${RED}✗ SSH client not found${NC}"
        echo "  Please install OpenSSH client first"
        return 1
    fi
}

# Function to connect to server
connect_to_server() {
    echo -e "${YELLOW}Connecting to server...${NC}"
    echo "You will be prompted for your password"
    echo ""
    ssh -p ${SERVER_PORT} ${SERVER_USER}@${SERVER_IP}
}

# Function to show remote commands
show_remote_commands() {
    echo -e "${BLUE}╔════════════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║                   Commands to Run on Server                               ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo -e "${YELLOW}1. Navigate to your Laravel project:${NC}"
    echo "   cd ~/public_html"
    echo "   # or"
    echo "   cd ~/domains/theartisticbd.com/public_html"
    echo ""
    echo -e "${YELLOW}2. Run the automated fix script:${NC}"
    echo "   bash fix-forbidden.sh"
    echo ""
    echo -e "${YELLOW}3. Or run commands manually:${NC}"
    echo "   chmod -R 775 storage bootstrap/cache"
    echo "   php artisan config:clear"
    echo "   php artisan cache:clear"
    echo "   php artisan route:clear"
    echo "   php artisan view:clear"
    echo "   php artisan storage:link"
    echo ""
    echo -e "${YELLOW}4. Check for errors in logs:${NC}"
    echo "   tail -n 50 storage/logs/laravel.log"
    echo ""
    echo -e "${YELLOW}5. Verify demo mode is disabled:${NC}"
    echo "   cat config/services.php | grep -A 3 'demo'"
    echo ""
}

# Main menu
show_menu() {
    echo -e "${BLUE}╔════════════════════════════════════════════════════════════════════════════╗${NC}"
    echo -e "${BLUE}║                           Main Menu                                        ║${NC}"
    echo -e "${BLUE}╚════════════════════════════════════════════════════════════════════════════╝${NC}"
    echo ""
    echo "1) Show Server Details"
    echo "2) Test SSH Connection"
    echo "3) Connect to Server via SSH"
    echo "4) Show Commands to Run on Server"
    echo "5) View All Documentation"
    echo "6) Exit"
    echo ""
    read -p "Select an option (1-6): " choice
    
    case $choice in
        1)
            show_server_details
            echo ""
            read -p "Press Enter to continue..."
            show_menu
            ;;
        2)
            test_connection
            echo ""
            read -p "Press Enter to continue..."
            show_menu
            ;;
        3)
            show_server_details
            connect_to_server
            ;;
        4)
            show_remote_commands
            echo ""
            read -p "Press Enter to continue..."
            show_menu
            ;;
        5)
            echo -e "${YELLOW}Available Documentation Files:${NC}"
            echo "  - README-FIX-FORBIDDEN.txt (Comprehensive forbidden error guide)"
            echo "  - CONNECT_TO_SERVER.txt (Server connection instructions)"
            echo "  - QUICK-FIX-GUIDE.txt (Quick step-by-step guide)"
            echo "  - SERVER_SETUP_COMMANDS.txt (Server setup commands)"
            echo ""
            read -p "Press Enter to continue..."
            show_menu
            ;;
        6)
            echo -e "${GREEN}Goodbye!${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid option${NC}"
            echo ""
            read -p "Press Enter to continue..."
            show_menu
            ;;
    esac
}

# Start the script
clear
show_server_details

# Check if running on server (if artisan exists, we're already on the server)
if [ -f "artisan" ]; then
    echo -e "${GREEN}✓ Detected Laravel installation - Running on server${NC}"
    echo ""
    echo -e "${YELLOW}This script appears to be running on the server.${NC}"
    echo "Would you like to run the fix commands now?"
    echo ""
    echo "1) Yes - Run automated fix"
    echo "2) No - Show manual commands"
    echo "3) Exit"
    echo ""
    read -p "Select an option (1-3): " server_choice
    
    case $server_choice in
        1)
            bash fix-forbidden.sh
            ;;
        2)
            show_remote_commands
            ;;
        3)
            exit 0
            ;;
        *)
            echo -e "${RED}Invalid option${NC}"
            exit 1
            ;;
    esac
else
    # We're on local machine, show connection menu
    show_menu
fi
