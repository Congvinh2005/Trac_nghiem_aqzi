<?php
/**
 * Test Login Debug
 */
session_start();
require_once 'database.php';

echo "<h2>Login Debug Test</h2>";
echo "<pre>\n";

// Test credentials
$username = 'admin';
$password = 'password123';

echo "=== Test Login ===\n";
echo "Username: " . $username . "\n";
echo "Password: " . $password . "\n";
echo "\n";

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        echo "❌ Database connection failed\n";
        exit;
    }
    
    echo "✅ Database connected\n\n";
    
    // Find user
    $stmt = $conn->prepare("SELECT * FROM users WHERE ten_user = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "❌ User 'admin' NOT FOUND in database\n";
        echo "\n=== All Users ===\n";
        $stmt = $conn->query("SELECT ma_user, ten_user, email, full_name FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $u) {
            echo "   - " . $u['ten_user'] . " (" . $u['email'] . ") - " . $u['full_name'] . "\n";
        }
        exit;
    }
    
    echo "✅ User found:\n";
    echo "   ma_user: " . $user['ma_user'] . "\n";
    echo "   ten_user: " . $user['ten_user'] . "\n";
    echo "   email: " . $user['email'] . "\n";
    echo "   full_name: " . $user['full_name'] . "\n";
    echo "   phan_quyen: " . $user['phan_quyen'] . "\n";
    echo "   password hash: " . substr($user['password'], 0, 60) . "...\n";
    echo "\n";
    
    // Verify password
    echo "=== Password Verification ===\n";
    $verify = password_verify($password, $user['password']);
    echo "Verify 'password123': " . ($verify ? '✅ SUCCESS' : '❌ FAILED') . "\n";
    echo "\n";
    
    if (!$verify) {
        echo "❌ Password does not match!\n";
        echo "\n=== Solution ===\n";
        echo "Let's update password to 'password123'...\n";
        
        $new_hash = password_hash('password123', PASSWORD_DEFAULT);
        echo "New hash: " . $new_hash . "\n";
        
        $update = $conn->prepare("UPDATE users SET password = ? WHERE ma_user = ?");
        $update->execute([$new_hash, $user['ma_user']]);
        
        if ($update) {
            echo "✅ Password updated!\n";
            
            // Verify again
            $stmt = $conn->prepare("SELECT password FROM users WHERE ma_user = ?");
            $stmt->execute([$user['ma_user']]);
            $updated = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $verify_new = password_verify('password123', $updated['password']);
            echo "Verify new password: " . ($verify_new ? '✅ SUCCESS' : '❌ FAILED') . "\n";
            
            if ($verify_new) {
                echo "\n✅ NOW YOU CAN LOGIN WITH:\n";
                echo "   Username: " . $user['ten_user'] . "\n";
                echo "   Password: password123\n";
            }
        } else {
            echo "❌ Failed to update password\n";
        }
    } else {
        echo "✅ Password is correct! You can login now.\n";
    }
    
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "</pre>\n";
?>
