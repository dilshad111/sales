@echo off
:: Database Backup Script for Sales App
:: Set current directory
cd /d %~dp0

:: Configuration
set DB_NAME=sales
set DB_USER=root
set DB_PASS=
set BACKUP_DIR=backups
set TIMESTAMP=%date:~-4,4%%date:~-7,2%%date:~-10,2%_%time:~0,2%%time:~3,2%%time:~6,2%
set TIMESTAMP=%TIMESTAMP: =0%

:: Create backup directory if not exists
if not exist "%BACKUP_DIR%" (
    mkdir "%BACKUP_DIR%"
)

echo Starting backup of %DB_NAME%...
"C:\xampp\mysql\bin\mysqldump.exe" -u%DB_USER% %DB_NAME% > "%BACKUP_DIR%\%DB_NAME%_%TIMESTAMP%.sql"

if %ERRORLEVEL% equ 0 (
    echo Backup completed successfully: %BACKUP_DIR%\%DB_NAME%_%TIMESTAMP%.sql
) else (
    echo [ERROR] Backup failed. Ensure MySQL is running and XAMPP path is correct.
    pause
)
