<?php
// ==========================================
// ชื่อไฟล์: SystemMenu.php
// ที่อยู่ไฟล์: models/SystemMenu.php
// ==========================================

class SystemMenu {
    private $conn;
    private $table_name = "system_menus";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ดึงเฉพาะเมนูที่เปิดใช้งาน (is_active = 1) และกรองตามสิทธิ์ (Role)
    public function readActive($role) {
        // ถ้าเป็น admin ให้เห็นทุกเมนู, ถ้าเป็น user ให้เห็นเฉพาะเมนูที่ตั้งค่า role_access เป็น 'all' หรือ 'user'
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = 1 ";
        
        if ($role !== 'admin') {
            $query .= " AND role_access IN ('all', :role) ";
        }
        
        $query .= " ORDER BY parent_id ASC, sort_order ASC";

        try {
            $stmt = $this->conn->prepare($query);
            if ($role !== 'admin') {
                $stmt->bindParam(":role", $role);
            }
            $stmt->execute();
            return $stmt;
        } catch (PDOException $e) {
            // หากตารางยังไม่ถูกสร้าง ให้คืนค่ากลับเป็น statement เปล่าเพื่อป้องกันเว็บพัง
            $stmt = $this->conn->prepare("SELECT 1 WHERE 1=0");
            $stmt->execute();
            error_log("SystemMenu Error: " . $e->getMessage());
            return $stmt;
        }
    }

    // ดึงเมนูทั้งหมดสำหรับหน้าตั้งค่า (Admin)
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY parent_id ASC, sort_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // เปิด/ปิด การแสดงผลเมนู
    public function toggleActive($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET is_active = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>