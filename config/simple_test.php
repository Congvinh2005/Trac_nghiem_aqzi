<?php
/**
 * Simple DB Test - PDO Direct
 */
echo "<h2>Simple PDO Test</h2>";
echo "<pre>\n";

// Test 1: MySQLi (đã work)
echo "=== Test 1: MySQLi ===\n";
$mysqli = new mysqli('localhost', 'udtbalbihosting_vinhzota', 'Vinh@1234', 'udtbalbihosting_vinhzota');
if ($mysqli->connect_error) {
    echo "❌ MySQLi Failed: " . $mysqli->connect_error . "\n";
} else {
    echo "✅ MySQLi Success!\n";
    $mysqli->close();
}
echo "\n";

// Test 2: PDO Direct
echo "=== Test 2: PDO Direct ===\n";
try {
    $dsn = "mysql:host=localhost;dbname=udtbalbihosting_vinhzota;charset=utf8mb4";
    $pdo = new PDO($dsn, 'udtbalbihosting_vinhzota', 'Vinh@1234');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ PDO Success!\n";
    
    // Test query
    $stmt = $pdo->query("SELECT DATABASE() as db_name");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "   Current DB: " . $row['db_name'] . "\n";
    
} catch (PDOException $e) {
    echo "❌ PDO Failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check database.php
echo "=== Test 3: database.php ===\n";
require_once 'database.php';

$database = new Database();
echo "Host: " . $database->host . "\n";
echo "DB: " . $database->db_name . "\n";
echo "User: " . $database->username . "\n";
echo "Pass: " . ($database->password ? str_repeat('*', strlen($database->password)) : '(empty)') . "\n";

$conn = $database->getConnection();
if ($conn) {
    echo "✅ database.php Connection Success!\n";
} else {
    echo "❌ database.php Connection Failed (null)!\n";
}

echo "</pre>\n";
?>
