<?php

// Test MySQL Connection for XAMPP
$host = '127.0.0.1';
$port = 3306;
$username = 'root';
$password = '';
$database = 'ferman_laravelproject';

echo "Testing MySQL Connection...\n";
echo "Host: $host\n";
echo "Port: $port\n";
echo "Database: $database\n\n";

try {
    // Test connection without database first
    $pdo = new PDO("mysql:host=$host;port=$port", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ MySQL is running!\n\n";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
    if ($stmt->rowCount() > 0) {
        echo "✓ Database '$database' exists!\n";
        
        // Try to connect to the database
        $pdo_db = new PDO("mysql:host=$host;port=$port;dbname=$database", $username, $password);
        $pdo_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "✓ Successfully connected to database!\n";
        echo "\nYou can now run: php artisan migrate:fresh --seed\n";
    } else {
        echo "✗ Database '$database' does NOT exist.\n";
        echo "\nPlease create it in phpMyAdmin:\n";
        echo "1. Go to http://localhost/phpmyadmin\n";
        echo "2. Click 'New' in left sidebar\n";
        echo "3. Database name: $database\n";
        echo "4. Collation: utf8mb4_unicode_ci\n";
        echo "5. Click 'Create'\n";
    }
} catch (PDOException $e) {
    echo "✗ MySQL connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Please:\n";
    echo "1. Open XAMPP Control Panel\n";
    echo "2. Start MySQL service\n";
    echo "3. Wait until it shows 'Running'\n";
    echo "4. Run this script again\n";
}





