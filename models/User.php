<?php
/**
 * User Model
 */

class User {
    private $conn;
    private $table = "users";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Get user by username or email
     */
    public function getByUsernameOrEmail($value) {
        $query = "SELECT * FROM " . $this->table . "
                  WHERE ten_user = :username OR email = :email
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":username", $value);
        $stmt->bindParam(":email", $value);
        $stmt->execute();

        return $stmt->fetch();
    }
    
    /**
     * Get user by ID
     */
    public function getById($ma_user) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE ma_user = :ma_user 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_user", $ma_user);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $query = "SELECT ma_user FROM " . $this->table . "
                  WHERE email = :email
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    /**
     * Check if phone exists
     */
    public function phoneExists($phone) {
        $query = "SELECT ma_user FROM " . $this->table . "
                  WHERE phone = :phone
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":phone", $phone);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        try {
            $query = "INSERT INTO " . $this->table . "
                      (ma_user, ten_user, password, full_name, email, phone, school, phan_quyen)
                      VALUES
                      (:ma_user, :ten_user, :password, :full_name, :email, :phone, :school, :phan_quyen)";

            $stmt = $this->conn->prepare($query);

            // Generate user ID
            $ma_user = $this->generateUserId($data['phan_quyen']);

            // Hash password
            $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

            // Bind parameters
            $stmt->bindParam(":ma_user", $ma_user);
            $stmt->bindParam(":ten_user", $data['ten_user']);
            $stmt->bindParam(":password", $password_hash);
            $stmt->bindParam(":full_name", $data['full_name']);
            $stmt->bindParam(":email", $data['email']);
            $stmt->bindParam(":phone", $data['phone']);
            $stmt->bindParam(":school", $data['school']);
            $stmt->bindParam(":phan_quyen", $data['phan_quyen']);

            if ($stmt->execute()) {
                return $ma_user;
            }

            error_log('Execute failed. SQL error: ' . print_r($stmt->errorInfo(), true));
            return false;
        } catch (PDOException $e) {
            error_log('Create user error: ' . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Generate user ID
     */
    private function generateUserId($role) {
        $prefix = $role === 'teacher' ? 'GV' : 'HS';
        $timestamp = date('YmdHis');
        $random = strtoupper(substr(md5(uniqid()), 0, 4));
        return $prefix . $random;
    }
    
    /**
     * Update user
     */
    public function update($ma_user, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET full_name = :full_name,
                      email = :email,
                      phone = :phone,
                      school = :school,
                      avatar = :avatar
                  WHERE ma_user = :ma_user";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ma_user", $ma_user);
        $stmt->bindParam(":full_name", $data['full_name']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":school", $data['school']);
        $stmt->bindParam(":avatar", $data['avatar']);
        
        return $stmt->execute();
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
}
?>
