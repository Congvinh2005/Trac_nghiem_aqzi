<?php
// Test password hash
$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "<br>";
echo "Hash mới: " . $hash . "<br><br>";

// Test verify
if (password_verify($password, $hash)) {
    echo "✅ Verify thành công!";
} else {
    echo "❌ Verify thất bại!";
}

// Test hash cũ
$old_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
echo "<br><br>Test hash cũ:<br>";
if (password_verify($password, $old_hash)) {
    echo "✅ Hash cũ vẫn hoạt động!";
} else {
    echo "❌ Hash cũ không hoạt động!";
}
?>
