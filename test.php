<?php
// Enable error display for testing
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "=== PHP Test ===\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Server: " . $_SERVER['SERVER_SOFTWARE'] . "\n\n";

echo "=== Database Connection Test ===\n";
try {
    $conn = new mysqli("localhost", "root", "", "blood_bank_system");
    
    if ($conn->connect_error) {
        echo "Connection failed: " . $conn->connect_error . "\n";
    } else {
        echo "Database connected successfully!\n";
        
        // Check if tables exist
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "Tables in database:\n";
            while ($row = $result->fetch_array()) {
                echo "  - " . $row[0] . "\n";
            }
        } else {
            echo "Error querying tables: " . $conn->error . "\n";
        }
    }
} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}

echo "\n=== File Permissions Test ===\n";
$files = [
    'includes/db_connect.php',
    'index.php',
    'login.php',
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "$file: EXISTS (readable: " . (is_readable($path) ? "YES" : "NO") . ")\n";
    } else {
        echo "$file: NOT FOUND\n";
    }
}

echo "\n=== Apache Modules ===\n";
echo "Loaded modules: ";
if (extension_loaded('mysqli')) echo "MySQLi ";
if (extension_loaded('pdo')) echo "PDO ";
if (extension_loaded('mbstring')) echo "mbstring ";
echo "\n";
?>
