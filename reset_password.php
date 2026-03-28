<?php
/**
 * File này để reset password cho user giaovien và hocsinh
 * Chạy 1 lần rồi xóa file đi
 */

require_once 'config/database.php';
require_once 'models/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

// Reset password cho giáo viên
$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Đang reset password...<br><br>";

// Update cho giaovien
$query = "UPDATE users SET password = :password WHERE ten_user = 'giaovien'";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $hash);
$stmt->execute();
echo "✅ Đã reset password cho giaovien<br>";

// Update cho hocsinh
$query = "UPDATE users SET password = :password WHERE ten_user = 'hocsinh'";
$stmt = $db->prepare($query);
$stmt->bindParam(':password', $hash);
$stmt->execute();
echo "✅ Đã reset password cho hocsinh<br>";

echo "<br><br>✅ HOÀN THÀNH!<br>";
echo "Password mới cho cả 2 tài khoản: <strong>password123</strong><br><br>";
echo "Hash mới: " . $hash . "<br><br>";
echo "Bây giờ bạn có thể <a href='views/dang_nhap.html'>đăng nhập</a>";
?>
