<?php
/**
 * Script to reset password for user 'vinh'
 * Run this once to fix the password issue
 */

require_once 'config/database.php';

try {
    $db = (new Database())->getConnection();
    
    // Generate correct hash for 'vinh123'
    $password = 'vinh123';
    $correct_hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<h2>Reset Password for user 'vinh'</h2>";
    echo "<p>New password: <strong>vinh123</strong></p>";
    echo "<p>Generated hash: <code>$correct_hash</code></p>";
    
    // Update password in database
    $query = "UPDATE users SET password = :password WHERE ten_user = :username";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':password', $correct_hash);
    $stmt->bindParam(':username', $username);
    $username = 'vinh';
    
    if ($stmt->execute()) {
        echo "<p style='color: green;'><strong>✓ SUCCESS:</strong> Password updated!</p>";
        
        // Verify
        $check_query = "SELECT password FROM users WHERE ten_user = :username";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->bindParam(':username', $username);
        $check_stmt->execute();
        $row = $check_stmt->fetch();
        
        if ($row) {
            echo "<p>Hash in DB: <code>" . $row['password'] . "</code></p>";
            
            // Test verify
            if (password_verify($password, $row['password'])) {
                echo "<p style='color: green;'><strong>✓ VERIFIED:</strong> password_verify() returns TRUE</p>";
                echo "<p>You can now login with username: <strong>vinh</strong> and password: <strong>vinh123</strong></p>";
            } else {
                echo "<p style='color: red;'><strong>✗ FAILED:</strong> password_verify() returns FALSE</p>";
            }
        }
    } else {
        echo "<p style='color: red;'><strong>✗ FAILED:</strong> Could not update password</p>";
        print_r($stmt->errorInfo());
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERROR:</strong> " . $e->getMessage() . "</p>";
}
?>
