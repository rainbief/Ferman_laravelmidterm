# XAMPP Database Setup Guide

## Step 1: Start XAMPP MySQL
1. Open XAMPP Control Panel
2. Click "Start" next to MySQL
3. Wait until it shows "Running" (green)

## Step 2: Create Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" in the left sidebar
3. Database name: `ferman_laravelproject`
4. Collation: `utf8mb4_unicode_ci`
5. Click "Create"

## Step 3: Verify .env File
Make sure your `.env` file has:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ferman_laravelproject
DB_USERNAME=root
DB_PASSWORD=
```

## Step 4: Run Migrations
After MySQL is running and database is created, run:
```
php artisan migrate:fresh --seed
```





