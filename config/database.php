<?php
/**
 * Database Configuration
 * Vinhzota - Hệ thống quản lý bài tập trực tuyến
 * Tự động phát hiện môi trường local hay hosting
 */

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    private $charset = "utf8mb4";

    public $conn;

    public function __construct() {
        // Tự động phát hiện môi trường
        if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || 
            strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false ||
            strpos($_SERVER['HTTP_HOST'], '192.168.') !== false) {
            // Local environment
            $this->host = "localhost";
            $this->db_name = "vinhzota";
            $this->username = "root";
            $this->password = "";
        } else {
            // Hosting environment - có thể override bằng define
            $this->host = defined('DB_HOST') ? DB_HOST : "localhost";
            $this->db_name = defined('DB_NAME') ? DB_NAME : "vinhzota";
            $this->username = defined('DB_USER') ? DB_USER : "root";
            $this->password = defined('DB_PASS') ? DB_PASS : "";
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
            // Log error on production, show on local
            if (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false) {
                echo "Connection Error: " . $e->getMessage();
            } else {
                error_log("DB Connection Error: " . $e->getMessage());
            }
        }

        return $this->conn;
    }
}
?>
