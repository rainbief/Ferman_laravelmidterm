@echo off
title Hotel System - MySQL Setup
color 0A
echo.
echo ========================================
echo   Hotel System - MySQL Setup Guide
echo ========================================
echo.
echo STEP 1: Start MySQL in XAMPP
echo ----------------------------------------
echo 1. Open XAMPP Control Panel
echo 2. Find "MySQL" in the services list
echo 3. Click the "Start" button
echo 4. Wait until it shows "Running" (green)
echo.
echo Press any key AFTER you have started MySQL...
pause >nul
echo.
echo ========================================
echo   Testing MySQL Connection...
echo ========================================
echo.
php fix_mysql_connection.php
echo.
echo ========================================
echo   Setting up Database...
echo ========================================
echo.
echo Running migrations and seeding data...
php artisan migrate:fresh --seed
echo.
echo ========================================
echo   Setup Complete!
echo ========================================
echo.
echo Your Hotel System is now ready!
echo.
echo Access it at: http://127.0.0.1:8000
echo.
pause





