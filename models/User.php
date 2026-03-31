<?php
class User {
    private $conn;
    private $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ดึงข้อมูลผู้ใช้งานทั้งหมด
    public function readAll() {
        $query = "SELECT id, username, full_name, role, is_active, created_at 
                  FROM " . $this->table_name . " 
                  ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // เพิ่มผู้ใช้งานใหม่
    public function create($data) {
        // เช็คว่า Username ซ้ำหรือไม่
        $checkQuery = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(":username", $data['username']);
        $checkStmt->execute();
        if($checkStmt->rowCount() > 0) return false; // Username ซ้ำ

        $query = "INSERT INTO " . $this->table_name . " 
                  (username, password, full_name, role, is_active) 
                  VALUES (:username, :password, :full_name, :role, :is_active)";
        $stmt = $this->conn->prepare($query);

        // เข้ารหัสผ่านก่อนบันทึก
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);

        $stmt->bindParam(":username", $data['username']);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":full_name", $data['full_name']);
        $stmt->bindParam(":role", $data['role']);
        $stmt->bindParam(":is_active", $data['is_active']);

        return $stmt->execute();
    }

    // อัปเดตข้อมูลทั่วไป (ไม่รวมรหัสผ่าน)
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET full_name = :full_name, role = :role, is_active = :is_active 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":full_name", $data['full_name']);
        $stmt->bindParam(":role", $data['role']);
        $stmt->bindParam(":is_active", $data['is_active']);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // รีเซ็ตรหัสผ่านใหม่
    public function updatePassword($id, $new_password) {
        $query = "UPDATE " . $this->table_name . " SET password = :password WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt->bindParam(":password", $hashed_password);
        $stmt->bindParam(":id", $id);

        return $stmt->execute();
    }

    // เปิด/ปิด สถานะผู้ใช้งาน
    public function toggleActive($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET is_active = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // ลบผู้ใช้งาน
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>