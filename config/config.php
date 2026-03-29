<?php
/**
 * Base Configuration for Vinhzota
 * Tự động phát hiện môi trường local hay hosting
 */

// Phát hiện protocol
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || 
             (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) ? "https://" : "http://";

// Phát hiện host
$host = $_SERVER['HTTP_HOST'];

// Tự động phát hiện base path
$scriptName = $_SERVER['SCRIPT_NAME'];
$pathParts = explode('/', trim(dirname($scriptName), '/'));

// Tìm thư mục 'vinhzota' trong path
$basePath = '';
foreach ($pathParts as $part) {
    if ($part === 'vinhzota') {
        $basePath = '/vinhzota';
        break;
    }
}

// Nếu không tìm thấy, kiểm tra xem có phải root không
if (empty($basePath)) {
    // Nếu đang chạy ở root (hosting)
    if (strpos($scriptName, 'index.php') !== false) {
        $basePath = '';
    } else {
        // Mặc định là /vinhzota cho local
        $basePath = '/vinhzota';
    }
}

// Base URL đầy đủ
define('BASE_URL', $protocol . $host . $basePath);
define('BASE_PATH', $basePath);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Thay đổi khi deploy
define('DB_PASS', '');      // Thay đổi khi deploy
define('DB_NAME', 'vinhzota');

// Timezone
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Error reporting (tắt trên production)
if (strpos(BASE_URL, 'localhost') !== false) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Session configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
