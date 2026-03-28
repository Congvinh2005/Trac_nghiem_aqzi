<?php
/**
 * Answer Model (Dap An)
 */

class Answer {
    private $conn;
    private $table = "dap_an";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * Create new answer
     */
    public function create($data) {
        $query = "INSERT INTO " . $this->table . " 
                  (ma_cau_hoi, noi_dung, ky_tu, la_dung) 
                  VALUES 
                  (:ma_cau_hoi, :noi_dung, :ky_tu, :la_dung)";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ma_cau_hoi", $data['ma_cau_hoi']);
        $stmt->bindParam(":noi_dung", $data['noi_dung']);
        $stmt->bindParam(":ky_tu", $data['ky_tu']);
        $stmt->bindParam(":la_dung", $data['la_dung'], PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Get all answers by question
     */
    public function getByQuestion($ma_cau_hoi) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE ma_cau_hoi = :ma_cau_hoi 
                  ORDER BY ky_tu ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_cau_hoi", $ma_cau_hoi);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get correct answer for a question
     */
    public function getCorrectAnswer($ma_cau_hoi) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE ma_cau_hoi = :ma_cau_hoi AND la_dung = 1 
                  LIMIT 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_cau_hoi", $ma_cau_hoi);
        $stmt->execute();
        
        return $stmt->fetch();
    }
    
    /**
     * Update answer
     */
    public function update($ma_dap_an, $data) {
        $query = "UPDATE " . $this->table . " 
                  SET noi_dung = :noi_dung,
                      ky_tu = :ky_tu,
                      la_dung = :la_dung
                  WHERE ma_dap_an = :ma_dap_an";
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(":ma_dap_an", $ma_dap_an);
        $stmt->bindParam(":noi_dung", $data['noi_dung']);
        $stmt->bindParam(":ky_tu", $data['ky_tu']);
        $stmt->bindParam(":la_dung", $data['la_dung'], PDO::PARAM_INT);
        
        return $stmt->execute();
    }
    
    /**
     * Delete answer
     */
    public function delete($ma_dap_an) {
        $query = "DELETE FROM " . $this->table . " WHERE ma_dap_an = :ma_dap_an";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_dap_an", $ma_dap_an);
        return $stmt->execute();
    }
    
    /**
     * Delete all answers by question
     */
    public function deleteByQuestion($ma_cau_hoi) {
        $query = "DELETE FROM " . $this->table . " WHERE ma_cau_hoi = :ma_cau_hoi";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":ma_cau_hoi", $ma_cau_hoi);
        return $stmt->execute();
    }
}
?>
