<?php
// ==========================================
// ชื่อไฟล์: Manpower.php
// ที่อยู่ไฟล์: models/Manpower.php
// ==========================================

class Manpower {
    private $conn;
    private $table_name = "manpower";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. 🌟 [แก้บั๊ก Fatal Error]: ฟังก์ชันค้นหาและกรองข้อมูล
    public function search($search = '', $emp_type = '', $dept = '', $status = '') {
        $query = "SELECT * FROM " . $this->table_name;
        $where_clauses = [];
        $params = [];

        // ค้นหาจาก เลขที่ตำแหน่ง หรือ ชื่อตำแหน่ง
        if (!empty($search)) {
            $where_clauses[] = "(position_number LIKE ? OR position_name LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }
        // กรองตามประเภทบุคลากร
        if (!empty($emp_type)) {
            $where_clauses[] = "employee_type = ?";
            $params[] = $emp_type;
        }
        // กรองตามส่วนราชการ
        if (!empty($dept)) {
            $where_clauses[] = "department = ?";
            $params[] = $dept;
        }
        // กรองตามสถานะ (ว่าง/มีคนครอง)
        if (!empty($status)) {
            $where_clauses[] = "status = ?";
            $params[] = $status;
        }

        // ประกอบคำสั่ง SQL
        if (count($where_clauses) > 0) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        $query .= " ORDER BY id DESC";

        $stmt = $this->conn->prepare($query);
        
        // ผูกค่า Parameter (Bind Values) แบบ Dynamic
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindValue($i + 1, $params[$i]);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // 2. ดึงข้อมูลตำแหน่งที่ 'ว่าง' (ใช้สำหรับ Dropdown ในหน้าฟอร์มเพิ่มพนักงาน)
    public function readAvailablePositions($current_position = null) {
        $query = "SELECT position_number, position_name, employee_type FROM " . $this->table_name . " WHERE status = 'vacant'";
        
        // ถ้าเป็นการแก้ไขพนักงาน ให้ดึงตำแหน่งเดิมของเขามาแสดงใน Dropdown ด้วย (แม้จะไม่ vacant แล้ว)
        if (!empty($current_position)) {
            $query .= " OR position_number = :current_pos";
        }
        
        $query .= " ORDER BY position_number ASC";
        
        $stmt = $this->conn->prepare($query);
        if (!empty($current_position)) {
            $stmt->bindParam(':current_pos', $current_position);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // 3. ดึงข้อมูลตำแหน่ง 1 รายการ
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 4. เพิ่มข้อมูลกรอบอัตรากำลัง
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (position_number, employee_type, position_name, level, department, salary, status, remark) 
                  VALUES (:position_number, :employee_type, :position_name, :level, :department, :salary, :status, :remark)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':position_number', $data['position_number']);
        $stmt->bindParam(':employee_type', $data['employee_type']);
        $stmt->bindParam(':position_name', $data['position_name']);
        $stmt->bindParam(':level', $data['level']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':salary', $data['salary']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':remark', $data['remark']);
        
        return $stmt->execute();
    }

    // 5. อัปเดตข้อมูลกรอบอัตรากำลัง
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET position_number=:position_number, employee_type=:employee_type, 
                      position_name=:position_name, level=:level, department=:department, 
                      salary=:salary, status=:status, remark=:remark 
                  WHERE id = :id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':position_number', $data['position_number']);
        $stmt->bindParam(':employee_type', $data['employee_type']);
        $stmt->bindParam(':position_name', $data['position_name']);
        $stmt->bindParam(':level', $data['level']);
        $stmt->bindParam(':department', $data['department']);
        $stmt->bindParam(':salary', $data['salary']);
        $stmt->bindParam(':status', $data['status']);
        $stmt->bindParam(':remark', $data['remark']);
        $stmt->bindParam(':id', $id);
        
        return $stmt->execute();
    }

    // 6. ลบข้อมูลกรอบอัตรากำลัง
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // 7. เช็คเลขที่ตำแหน่งซ้ำ (ป้องกันการใช้เลขที่เดียวกัน)
    public function isPositionNumberExists($position_number, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE position_number = :position_number";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id"; // ยกเว้น ID ตัวเองตอนแก้ไข
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':position_number', $position_number);
        if ($exclude_id) {
            $stmt->bindParam(':exclude_id', $exclude_id);
        }
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // 8. อัปเดตสถานะ (ว่าง/มีผู้ครองตำแหน่ง) ผ่านเลขที่ตำแหน่งโดยตรง
    public function updateStatusByPositionNumber($position_number, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE position_number = :position_number";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':position_number', $position_number);
        return $stmt->execute();
    }
}
?>