<?php
/**
 * Test Database Connection
 * Run: http://localhost/vinhzota/config/test_db.php
 */

require_once 'database.php';

echo "<h2>Database Connection Test</h2>";
echo "<pre>";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "✅ Connection successful!\n\n";
        echo "Host: " . $database->host . "\n";
        echo "Database: " . $database->db_name . "\n";
        echo "User: " . $database->username . "\n";
        echo "Charset: " . $database->charset . "\n\n";
        
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
        echo "❌ Failed to connect\n";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Error Code: " . $e->getCode() . "\n";
}

echo "</pre>";
echo "<hr>";
echo "<p><a href='../views/trang_admin.html'>← Back to Admin</a></p>";
?>
