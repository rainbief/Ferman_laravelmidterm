<?php

echo "========================================\n";
echo "MySQL Connection Fixer for XAMPP\n";
echo "========================================\n\n";

// Try different connection methods
$configs = [
    ['host' => '127.0.0.1', 'port' => 3306, 'name' => 'Standard (127.0.0.1:3306)'],
    ['host' => 'localhost', 'port' => 3306, 'name' => 'Localhost (localhost:3306)'],
    ['host' => '127.0.0.1', 'port' => 3307, 'name' => 'Alternative Port (127.0.0.1:3307)'],
];

$username = 'root';
$password = '';
$database = 'ferman_laravelproject';

$connected = false;

foreach ($configs as $config) {
    echo "Trying: {$config['name']}...\n";
    try {
        $pdo = new PDO(
            "mysql:host={$config['host']};port={$config['port']}",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        echo "✓ Connection successful!\n";
        echo "  Host: {$config['host']}\n";
        echo "  Port: {$config['port']}\n\n";
        
        // Check if database exists
        $stmt = $pdo->query("SHOW DATABASES LIKE '$database'");
        if ($stmt->rowCount() > 0) {
            echo "✓ Database '$database' exists!\n\n";
            echo "Your .env should have:\n";
            echo "DB_CONNECTION=mysql\n";
            echo "DB_HOST={$config['host']}\n";
            echo "DB_PORT={$config['port']}\n";
            echo "DB_DATABASE=$database\n";
            echo "DB_USERNAME=$username\n";
            echo "DB_PASSWORD=\n\n";
            $connected = true;
            break;
        } else {
            echo "✗ Database '$database' does NOT exist.\n";
            echo "  Creating database...\n";
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            echo "✓ Database created!\n\n";
            $connected = true;
            break;
        }
    } catch (PDOException $e) {
        echo "✗ Failed: " . $e->getMessage() . "\n\n";
    }
}

if (!$connected) {
    echo "\n========================================\n";
    echo "MySQL is NOT running!\n";
    echo "========================================\n\n";
    echo "Please do the following:\n\n";
    echo "1. Open XAMPP Control Panel\n";
    echo "2. Find 'MySQL' in the list\n";
    echo "3. Click the 'Start' button\n";
    echo "4. Wait until it shows 'Running' (green)\n";
    echo "5. Run this script again: php fix_mysql_connection.php\n\n";
    echo "If MySQL won't start, check:\n";
    echo "- Port 3306 is not used by another application\n";
    echo "- XAMPP is installed correctly\n";
    echo "- You have administrator rights\n";
}





