@echo off
echo ========================================
echo Hotel System - Database Setup
echo ========================================
echo.
echo Testing MySQL connection...
php test_db_connection.php
echo.
echo If MySQL is running, press any key to continue with migrations...
pause
echo.
echo Running migrations and seeding database...
php artisan migrate:fresh --seed
echo.
echo Done! Your hotel system is ready.
echo.
pause





