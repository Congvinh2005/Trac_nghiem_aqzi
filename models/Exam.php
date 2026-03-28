<?php
/**
 * Exam Model (De Thi)
 */

class Exam {
    private $conn;
    private $table = "de_thi";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Generate exam code (6 characters)
     */
    public function generateCode() {
        $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }
        return $code;
    }
    
    /**
     * Generate exam ID
     */
    private function generateId() {
        return 'DE' . date('YmdHis') . strtoupper(substr(md5(uniqid()), 0, 4));
    }
    
    /**
     * Create new exam
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (ma_de, ma_giao_vien, ten_de, ma_code, thoi_gian_nap, cho_xem_ket_qua, yeu_cau_dang_nhap) 
                  VALUES 
                  (:ma_de, :ma_giao_vien, :ten_de, :ma_code, :thoi_gian_nap, :cho_xem_ket_qua, :yeu_cau_dang_nhap)";
        
        $stmt = $this->conn->prepare($query);
        
        $ma_de = $this->generateId();
        $ma_code = $this->generateCode();
        
        $stmt->bindParam(":ma_de", $ma_de);
        $stmt->bindParam(":ma_giao_vien", $data['ma_giao_vien']);
        $stmt->bindParam(":ten_de", $data['ten_de']);
        $stmt->bindParam(":ma_code", $ma_code);
        $stmt->bindParam(":thoi_gian_nap", $data['thoi_gian_nap']);
        $stmt->bindParam(":cho_xem_ket_qua", $data['cho_xem_ket_qua']);
        $stmt->bindParam(":yeu_cau_dang_nhap", $data['yeu_cau_dang_nhap']);
        
        if ($stmt->execute()) {
            return ['ma_de' => $ma_de, 'ma_code' => $ma_code];
        }
        
        return false;
    }
    
    /**
     * Get exam by code
     */
    public function getByCode($code) {
        $query = "SELECT d.*, u.full_name as giao_vien 
                  FROM " . $this->table . " d
                  JOIN users u ON d.ma_giao_vien = u.ma_user
                  WHERE d.ma_code = :code 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":code", $code);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get exam by ID
     */
    public function getById($ma_de) {
        $query = "SELECT d.*, u.full_name as giao_vien 
                  FROM " . $this->table . " d
                  JOIN users u ON d.ma_giao_vien = u.ma_user
                  WHERE d.ma_de = :ma_de 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_de", $ma_de);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Get all exams by teacher
     */
    public function getByTeacher($ma_giao_vien) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE ma_giao_vien = :ma_giao_vien 
                  ORDER BY ngay_tao DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_giao_vien", $ma_giao_vien);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Update exam
     */
    public function update($ma_de, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET ten_de = :ten_de,
                      thoi_gian_nap = :thoi_gian_nap,
                      cho_xem_ket_qua = :cho_xem_ket_qua,
                      yeu_cau_dang_nhap = :yeu_cau_dang_nhap,
                      trang_thai = :trang_thai
                  WHERE ma_de = :ma_de";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ma_de", $ma_de);
        $stmt->bindParam(":ten_de", $data['ten_de']);
        $stmt->bindParam(":thoi_gian_nap", $data['thoi_gian_nap']);
        $stmt->bindParam(":cho_xem_ket_qua", $data['cho_xem_ket_qua']);
        $stmt->bindParam(":yeu_cau_dang_nhap", $data['yeu_cau_dang_nhap']);
        $stmt->bindParam(":trang_thai", $data['trang_thai']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete exam
     */
    public function delete($ma_de) {
        $query = "DELETE FROM " . $this->table . " WHERE ma_de = :ma_de";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_de", $ma_de);
        return $stmt->execute();
    }
    
    /**
     * Check if code exists
     */
    public function codeExists($code) {
        $query = "SELECT ma_code FROM " . $this->table . " WHERE ma_code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":code", $code);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }
}
?>
