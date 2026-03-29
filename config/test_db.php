<?php
/**
 * Test Database Connection
 * Run: http://localhost/vinhzota/config/test_db.php
 */

echo "<h2>Database Connection Test</h2>";
echo "<pre>";

// Check if database_config.php exists
echo "=== Checking Config Files ===\n";
$configFile = __DIR__ . '/database_config.php';
if (file_exists($configFile)) {
    echo "✅ database_config.php EXISTS\n";
    require_once $configFile;
    echo "   DB_HOST: " . DB_HOST . "\n";
    echo "   DB_USER: " . DB_USER . "\n";
    echo "   DB_NAME: " . DB_NAME . "\n";
    echo "   DB_PASS: " . (DB_PASS ? str_repeat('*', strlen(DB_PASS)) : '(empty)') . "\n";
} else {
    echo "❌ database_config.php NOT FOUND\n";
    echo "   Will use default localhost settings\n";
}

echo "\n=== Testing Connection ===\n";

try {
    require_once 'database.php';
    
    $database = new Database();
    echo "Database object created\n";
    echo "   Host: " . $database->host . "\n";
    echo "   Database: " . $database->db_name . "\n";
    echo "   User: " . $database->username . "\n";
    
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "\n✅ Connection SUCCESSFUL!\n\n";
        
        // Test query
        $stmt = $conn->query("SELECT 1");
        if ($stmt) {
            echo "✅ Query test passed!\n";
        }
        
        // Get database info
        $stmt = $conn->query("SELECT DATABASE() as db_name");
        $row = $stmt->fetch();
        echo "\nCurrent database: " . $row['db_name'] . "\n";
        
        // Get MySQL version
        $stmt = $conn->query("SELECT VERSION() as version");
        $row = $stmt->fetch();
        echo "MySQL version: " . $row['version'] . "\n";
        
    } else {
        echo "\n❌ Failed to connect\n";
    }
} catch (PDOException $e) {
    echo "\n❌ PDO Error: " . $e->getMessage() . "\n";
    echo "   Error Code: " . $e->getCode() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
} catch (Exception $e) {
    echo "\n❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='../'>← Back to Home</a></p>";
?>
