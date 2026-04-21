@echo off
setlocal enabledelayedexpansion

:: Master Setup Script for Sales Application
:: Ensure we are in project root
cd /d %~dp0

echo ========================================================
echo        Sales Application Installer & Environment Setup
echo ========================================================
echo.

:: 1. Environment Check
echo [1/4] Checking environment...
if not exist ".env" (
    echo [INFO] Copying .env.example to .env...
    copy ".env.example" ".env"
)

:: 2. Dependencies
echo [2/4] Initializing application dependencies...
:: Assume composer is in path, if not we check XAMPP.
"C:\xampp\php\php.exe" artisan key:generate
"C:\xampp\php\php.exe" artisan storage:link

:: 3. Database Initialization
echo [3/4] Setting up database...
echo [INFO] Please ensure XAMPP MySQL is running.
"C:\xampp\php\php.exe" artisan migrate --force

:: 4. Desktop Integration
echo [4/4] Creating desktop access...
powershell -ExecutionPolicy Bypass -File create_desktop_icon.ps1

:: 5. Auto Backup scheduled task setup (optional but recommended)
echo [5/5] Scheduling daily backup task...
schtasks /create /tn "SalesApp_DailyBackup" /tr "C:\xampp\htdocs\Sales\db_backup.bat" /sc daily /st 20:00 /f

echo.
echo ========================================================
echo       INSTALLATION COMPLETE!
echo       You can now open the 'Sales Application' from your desktop.
echo ========================================================
pause
