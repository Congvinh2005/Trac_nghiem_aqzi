<?php
/**
 * Database Configuration
 * Vinhzota - Hệ thống quản lý bài tập trực tuyến
 * Tự động phát hiện môi trường local hay hosting
 */

// Chỉ nạp cấu hình hosting khi KHÔNG phải môi trường local
$httpHost = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
$isLocalHost = (
    stripos($httpHost, 'localhost') !== false ||
    stripos($httpHost, '127.0.0.1') !== false ||
    stripos($httpHost, '192.168.') !== false
);

if (!$isLocalHost && file_exists(__DIR__ . '/database_config.php')) {
    require_once __DIR__ . '/database_config.php';
}

class Database {
    public $host;
    public $db_name;
    public $username;
    public $password;
    private $charset = "utf8mb4";

    public $conn;

    public function __construct() {
        // Ưu tiên dùng config từ database_config.php nếu tồn tại
        if (defined('DB_HOST')) {
            $this->host = DB_HOST;
            $this->db_name = DB_NAME;
            $this->username = DB_USER;
            $this->password = DB_PASS;
            if (defined('DB_CHARSET')) {
                $this->charset = DB_CHARSET;
            }
        } 
        // Tự động phát hiện môi trường nếu không có config file
        else if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
            strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
            strpos($_SERVER['HTTP_HOST'], '192.168.') !== false) {
            // Local environment
            $this->host = "localhost";
            $this->db_name = "vinhzota";
            $this->username = "root";
            $this->password = "";
        } else {
            // Hosting environment - udtbalbihosting
            $this->host = "localhost";
            $this->db_name = "udtbalbihosting_vinhzota";
            $this->username = "udtbalbihosting_vinhzota";
            $this->password = "Vinh@1234";
        }
    }

    /**
     * Get database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            
        } catch(PDOException $e) {
            // Hiển thị lỗi rõ ràng
            die("PDO Connection Error: " . $e->getMessage() . 
                "\nHost: {$this->host}, DB: {$this->db_name}, User: {$this->username}");
        }

        return $this->conn;
    }
}
?>
