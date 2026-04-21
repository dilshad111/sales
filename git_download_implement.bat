@echo off
echo ========================================
echo   GITHUR PULL - DOWNLOADING CHANGES
echo ========================================
echo.

echo FETCHING AND UPDATING FROM GITHUB...
git pull origin master
echo.

echo RUNNING DATABASE MIGRATIONS...
php artisan migrate --force
echo.

echo CLEARING AND OPTIMIZING CACHE...
php artisan optimize:clear
echo.

echo ========================================
echo   DONE! PROJECT UPDATED AND IMPLEMENTED.
echo ========================================
pause
