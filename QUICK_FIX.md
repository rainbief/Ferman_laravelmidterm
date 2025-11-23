# Quick Fix: MySQL Connection Error

## The Problem
You're getting: `SQLSTATE[HY000] [2002] No connection could be made because the target machine actively refused it`

This means **MySQL is not running** in XAMPP.

## The Solution (3 Steps)

### Step 1: Start MySQL
1. Open **XAMPP Control Panel**
2. Find **MySQL** in the list
3. Click the **Start** button
4. Wait until it shows **"Running"** (green background)

### Step 2: Create Database (if needed)
1. Open **phpMyAdmin**: http://localhost/phpmyadmin
2. Click **"New"** in the left sidebar
3. Database name: `ferman_laravelproject`
4. Collation: `utf8mb4_unicode_ci`
5. Click **"Create"**

### Step 3: Run Setup
**Option A - Easy Way:**
Double-click `START_MYSQL_AND_SETUP.bat`

**Option B - Manual:**
```bash
php fix_mysql_connection.php
php artisan migrate:fresh --seed
```

## Verify It's Working
After starting MySQL, test the connection:
```bash
php fix_mysql_connection.php
```

If you see "✓ Connection successful!" then run:
```bash
php artisan migrate:fresh --seed
```

## Troubleshooting

### MySQL Won't Start?
- Check if port 3306 is already in use
- Try running XAMPP as Administrator
- Check XAMPP error logs

### Still Having Issues?
1. Make sure XAMPP is properly installed
2. Check Windows Services for MySQL
3. Restart your computer and try again

## Your Database Settings
- **Host:** 127.0.0.1
- **Port:** 3306
- **Database:** ferman_laravelproject
- **Username:** root
- **Password:** (empty)

These are already configured in your `.env` file.





