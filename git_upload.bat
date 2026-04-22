@echo off
echo ========================================
echo   GITHUR PUSH - UPLOADING CHANGES
echo ========================================
echo.

echo STAGING ALL CHANGES...
git add .
echo.

set msg=Purchases form and report Updated
set /p msg="Sales Order updated (Default: %msg%): "
if "%msg%"=="" set msg=Update: %date% %time%

echo.
echo COMMITTING CHANGES...
git commit -m "%msg%"
echo.

echo PUSHING TO GITHUB...
git push origin master
echo.

echo ========================================
echo   DONE! CHANGES UPLOADED.
echo ========================================
pause
