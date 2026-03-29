<?php
/**
 * Test Direct API Login
 */
session_start();
require_once '../models/User.php';
require_once 'database.php';

echo "<h2>Direct API Login Test</h2>";
echo "<pre>\n";

// Test credentials
$username = 'admin';
$password = 'password123';

echo "=== Testing Login API ===\n";
echo "Username: " . $username . "\n";
echo "Password: " . $password . "\n";
echo "\n";

try {
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    echo "✅ Database connected\n\n";
    
    // Get user
    echo "Fetching user...\n";
    $user_data = $user->getByUsernameOrEmail($username);
    
    if (!$user_data) {
        echo "❌ User not found!\n";
        exit;
    }
    
    echo "✅ User found:\n";
    echo "   ten_user: " . $user_data['ten_user'] . "\n";
    echo "   phan_quyen: " . $user_data['phan_quyen'] . "\n";
    echo "\n";
    
    // Verify password
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
        'phan_quyen' => $user_data['phan_quyen'],
        'avatar' => $user_data['avatar']
    ];
    
    echo "✅ Session created!\n";
    echo "   Session ID: " . session_id() . "\n";
    echo "   User in session: " . $_SESSION['user']['ten_user'] . "\n";
    echo "\n";
    
    // Return JSON like the real API
    $redirect = ($user_data['phan_quyen'] === 'teacher') ? '/views/trang_admin.html' : '/views/trang_chu.html';
    
    echo "=== JSON Response (what JavaScript receives) ===\n";
    $response = [
        'success' => true,
        'message' => 'Đăng nhập thành công',
        'redirect' => $redirect,
        'user' => [
            'ten_user' => $user_data['ten_user'],
            'full_name' => $user_data['full_name'],
            'phan_quyen' => $user_data['phan_quyen']
        ]
    ];
    
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    echo "\n\n";
    
    echo "✅ LOGIN SUCCESSFUL!\n";
    echo "Redirect to: " . $redirect . "\n";
    
} catch (Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
}

echo "</pre>\n";
?>
