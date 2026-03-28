<?php
/**
 * Test login trực tiếp
 */
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'models/User.php';

echo "<h2>Test Login System</h2><hr>";

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo "❌ Không thể kết nối database!<br>";
    exit;
}
echo "✅ Kết nối database thành công!<br><br>";

$user = new User($db);

// Test lấy user giaovien
echo "Đang kiểm tra user 'giaovien'...<br>";
$user_data = $user->getByUsernameOrEmail('giaovien');

if (!$user_data) {
    echo "❌ Không tìm thấy user 'giaovien' trong database!<br>";
    echo "<br><strong>Giải pháp:</strong> Chạy file reset_password.php để tạo lại user<br>";
} else {
    echo "✅ Tìm thấy user:<br>";
    echo "<pre>";
    print_r($user_data);
    echo "</pre>";
    
    // Test password
    $password = 'password123';
    $hash_from_db = $user_data['password'];
    
    echo "<br>Password test: " . $password . "<br>";
    echo "Hash trong DB: " . $hash_from_db . "<br><br>";
    
    if ($user->verifyPassword($password, $hash_from_db)) {
        echo "✅ <strong>Password đúng!</strong><br>";
    } else {
        echo "❌ <strong>Password sai!</strong><br>";
        echo "<br><strong>Giải pháp:</strong> Chạy reset_password.php<br>";
    }
}

echo "<br><br><a href='reset_password.php'>Chạy reset_password.php</a> | ";
echo "<a href='views/dang_nhap.html'>Quay lại đăng nhập</a>";
?>
