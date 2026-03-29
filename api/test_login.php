<?php
/**
 * Test Direct API Login - Simple Version
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<h2>Simple Login Test</h2>";
echo "<pre>\n";

// Test credentials
$username = 'admin';
$password = 'password123';

echo "Username: " . $username . "\n";
echo "Password: " . $password . "\n\n";

try {
    echo "Loading database...\n";
    require_once '../config/database.php';
    echo "✅ database.php loaded\n\n";
    
    echo "Creating Database object...\n";
    $database = new Database();
    echo "✅ Database object created\n";
    echo "   Host: " . $database->host . "\n";
    echo "   DB: " . $database->db_name . "\n\n";
    
    echo "Getting connection...\n";
    $db = $database->getConnection();
    
    if (!$db) {
        echo "❌ Connection failed (null)\n";
        exit;
    }
    
    echo "✅ Connection successful\n\n";
    
    echo "Loading User model...\n";
    require_once '../models/User.php';
    echo "✅ User model loaded\n\n";
    
    echo "Creating User object...\n";
    $user = new User($db);
    echo "✅ User object created\n\n";
    
    echo "Fetching user '" . $username . "'...\n";
    $user_data = $user->getByUsernameOrEmail($username);
    
    if (!$user_data) {
        echo "❌ User not found!\n";
        echo "\nAll users:\n";
        $stmt = $db->query("SELECT ten_user, email FROM users");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "   - " . $row['ten_user'] . " (" . $row['email'] . ")\n";
        }
        exit;
    }
    
    echo "✅ User found:\n";
    echo "   ten_user: " . $user_data['ten_user'] . "\n";
    echo "   phan_quyen: " . $user_data['phan_quyen'] . "\n\n";
    
    echo "Verifying password...\n";
    $verify_result = $user->verifyPassword($password, $user_data['password']);
    
    if (!$verify_result) {
        echo "❌ Password verification FAILED!\n";
        exit;
    }
    
    echo "✅ Password verified!\n\n";
    
    // Set session
    $_SESSION['user'] = [
        'ma_user' => $user_data['ma_user'],
        'ten_user' => $user_data['ten_user'],
        'full_name' => $user_data['full_name'],
        'email' => $user_data['email'],
        'phan_quyen' => $user_data['phan_quyen']
    ];
    
    echo "✅ Session created!\n";
    echo "   User: " . $_SESSION['user']['ten_user'] . "\n\n";
    
    echo "✅✅✅ LOGIN SUCCESSFUL! ✅✅✅\n";
    echo "Redirect to: /views/" . ($user_data['phan_quyen'] === 'teacher' ? 'trang_admin.html' : 'trang_chu.html') . "\n";
    
} catch (Exception $e) {
    echo "❌❌❌ ERROR! ❌❌❌\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "</pre>\n";
?>
