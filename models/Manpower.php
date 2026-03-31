<?php
class Manpower {
    private $conn;
    private $table_name = "manpower";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ✅ เพิ่มฟังก์ชันตรวจสอบ เลขที่ตำแหน่ง ซ้ำ
    public function isPositionNumberExists($position_number, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE position_number = ?";
        if ($exclude_id) {
            $query .= " AND id != ?";
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $position_number);
        if ($exclude_id) {
            $stmt->bindParam(2, $exclude_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY department ASC, position_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function readByDepartment($department) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE department = ? ORDER BY position_number ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $department);
        $stmt->execute();
        return $stmt;
    }

    public function readAvailablePositions($current_position_number = "") {
        $query = "SELECT * FROM " . $this->table_name . " WHERE status = 'vacant'";
        if(!empty($current_position_number)) {
            $query .= " OR position_number = :current_pos";
        }
        $query .= " ORDER BY department, position_number";
        
        $stmt = $this->conn->prepare($query);
        if(!empty($current_position_number)) {
            $stmt->bindParam(":current_pos", $current_position_number);
        }
        $stmt->execute();
        return $stmt;
    }

    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (department, division, employee_type, position_number, position_name, level, status, remark) 
                  VALUES 
                  (:department, :division, :employee_type, :position_number, :position_name, :level, :status, :remark)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":department", $data['department']);
        $stmt->bindParam(":division", $data['division']);
        $stmt->bindParam(":employee_type", $data['employee_type']);
        $stmt->bindParam(":position_number", $data['position_number']);
        $stmt->bindParam(":position_name", $data['position_name']);
        $stmt->bindParam(":level", $data['level']);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":remark", $data['remark']);

        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET department = :department, division = :division, employee_type = :employee_type, 
                      position_number = :position_number, position_name = :position_name, 
                      level = :level, status = :status, remark = :remark 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":department", $data['department']);
        $stmt->bindParam(":division", $data['division']);
        $stmt->bindParam(":employee_type", $data['employee_type']);
        $stmt->bindParam(":position_number", $data['position_number']);
        $stmt->bindParam(":position_name", $data['position_name']);
        $stmt->bindParam(":level", $data['level']);
        $stmt->bindParam(":status", $data['status']);
        $stmt->bindParam(":remark", $data['remark']);
        $stmt->bindParam(":id", $id);

        try {
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    public function updateStatusByPositionNumber($position_number, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE position_number = :position_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":position_number", $position_number);
        return $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        return $stmt->execute();
    }
}
?>