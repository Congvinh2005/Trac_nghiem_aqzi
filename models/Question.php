<?php
/**
 * Question Model (Cau Hoi)
 */

class Question {
    private $conn;
    private $table = "cau_hoi";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create new question
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (ma_de, noi_dung, hinh_anh, thu_tu) 
                  VALUES 
                  (:ma_de, :noi_dung, :hinh_anh, :thu_tu)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ma_de", $data['ma_de']);
        $stmt->bindParam(":noi_dung", $data['noi_dung']);
        $stmt->bindParam(":hinh_anh", $data['hinh_anh']);
        $stmt->bindParam(":thu_tu", $data['thu_tu']);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Get all questions by exam
     */
    public function getByExam($ma_de) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE ma_de = :ma_de 
                  ORDER BY thu_tu ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_de", $ma_de);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get question by ID
     */
    public function getById($ma_cau_hoi) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE ma_cau_hoi = :ma_cau_hoi 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_cau_hoi", $ma_cau_hoi);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Update question
     */
    public function update($ma_cau_hoi, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET noi_dung = :noi_dung,
                      hinh_anh = :hinh_anh,
                      thu_tu = :thu_tu
                  WHERE ma_cau_hoi = :ma_cau_hoi";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ma_cau_hoi", $ma_cau_hoi);
        $stmt->bindParam(":noi_dung", $data['noi_dung']);
        $stmt->bindParam(":hinh_anh", $data['hinh_anh']);
        $stmt->bindParam(":thu_tu", $data['thu_tu']);
        
        return $stmt->execute();
    }
    
    /**
     * Delete question
     */
    public function delete($ma_cau_hoi) {
        $query = "DELETE FROM " . $this->table . " WHERE ma_cau_hoi = :ma_cau_hoi";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_cau_hoi", $ma_cau_hoi);
        return $stmt->execute();
    }
    
    /**
     * Delete all questions by exam
     */
    public function deleteByExam($ma_de) {
        $query = "DELETE FROM " . $this->table . " WHERE ma_de = :ma_de";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_de", $ma_de);
        return $stmt->execute();
    }
}
?>
