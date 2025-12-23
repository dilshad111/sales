@echo off
cd /d c:\xampp\htdocs\Sales
start /b php artisan serve
start /b php artisan queue:listen --tries=1
start /b npm run dev
pause
