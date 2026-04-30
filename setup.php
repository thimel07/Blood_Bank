<?php
// Database Import Setup Script
set_time_limit(300);

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Create connection without database selection
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        die('Connection Failed: ' . $conn->connect_error);
    }
    
    echo "<h2>Importing Blood Bank Database...</h2>";
    echo "<hr>";
    
    // Read the SQL file
    $sql_file = __DIR__ . '/database/blood_bank_complete.sql';
    
    if (!file_exists($sql_file)) {
        die("SQL file not found: $sql_file");
    }
    
    $sql_content = file_get_contents($sql_file);
    
    // Split SQL statements
    $statements = array_filter(
        array_map('trim', explode(";\n", $sql_content)),
        function($s) { return strlen($s) > 0 && !str_starts_with($s, '--'); }
    );
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        if (trim($statement) === '') continue;
        
        if ($conn->multi_query($statement)) {
            // Process all results
            do {
                $conn->next_result();
            } while ($conn->more_results());
            $success_count++;
            echo "<p style='color: green;'>✓ Executed</p>";
        } else {
            $error_count++;
            echo "<p style='color: red;'>✗ Error: " . htmlspecialchars($conn->error) . "</p>";
        }
    }
    
    $conn->close();
    
    echo "<hr>";
    echo "<p><strong>Import Complete!</strong></p>";
    echo "<p>Successfully executed: $success_count statements</p>";
    if ($error_count > 0) {
        echo "<p style='color: red;'>Errors: $error_count statements</p>";
    }
    echo "<p><a href='/blood-bank/login.php'>Go to Login</a></p>";
    
} catch (Exception $e) {
    die("<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Import</title>
    <style>
        body { font-family: Arial; margin: 20px; }
        p { line-height: 1.6; }
    </style>
</head>
<body>
</body>
</html>
