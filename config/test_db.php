<?php
/**
 * Test Database Connection
 */

echo "<h2>Database Connection Test</h2>";
echo "<pre>";
echo "=== Server Info ===\n";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
echo "\n";

echo "=== Checking Config Files ===\n";
$configFile = __DIR__ . '/database_config.php';
if (file_exists($configFile)) {
    echo "✅ database_config.php EXISTS\n";
    require_once $configFile;
    echo "   DB_HOST: " . DB_HOST . "\n";
    echo "   DB_USER: " . DB_USER . "\n";
    echo "   DB_NAME: " . DB_NAME . "\n";
} else {
    echo "❌ database_config.php NOT FOUND\n";
    echo "   Using database.php defaults\n";
}
echo "\n";

echo "=== Testing Connection ===\n";
try {
    require_once 'database.php';
    
    echo "Creating Database object...\n";
    $database = new Database();
    
    echo "Database object created ✅\n";
    echo "   Host: " . $database->host . "\n";
    echo "   Database: " . $database->db_name . "\n";
    echo "   User: " . $database->username . "\n";
    echo "   Password: " . ($database->password ? str_repeat('*', strlen($database->password)) : '(empty)') . "\n";
    echo "\n";
    
    echo "Attempting connection...\n";
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "\n✅✅✅ Connection SUCCESSFUL! ✅✅✅\n\n";
        
        $stmt = $conn->query("SELECT 1");
        if ($stmt) {
            echo "✅ Query test passed!\n";
        }
        
        $stmt = $conn->query("SELECT DATABASE() as db_name");
        $row = $stmt->fetch();
        echo "\nCurrent database: " . $row['db_name'] . "\n";
        
        $stmt = $conn->query("SELECT VERSION() as version");
        $row = $stmt->fetch();
        echo "MySQL version: " . $row['version'] . "\n";
        
    } else {
        echo "\n❌❌❌ Connection FAILED! ❌❌❌\n";
        echo "   getConnection() returned null\n";
    }
    
} catch (PDOException $e) {
    echo "\n❌❌❌ PDO Exception! ❌❌❌\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Code: " . $e->getCode() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    
} catch (Exception $e) {
    echo "\n❌❌❌ Exception! ❌❌❌\n";
    echo "   Message: " . $e->getMessage() . "\n";
    echo "   Type: " . get_class($e) . "\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='../'>← Back to Home</a></p>";
?>
