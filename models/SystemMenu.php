<?php
class SystemMenu {
    private $conn;
    private $table_name = "system_menus";

    public function __construct($db) {
        $this->conn = $db;
    }

    // 1. ดึงข้อมูลแค่ 1 รายการ
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // ดึงเมนูทั้งหมด เรียงตามลำดับ sort_order
    public function readAll() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY sort_order ASC, id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // 2 & 3. ดึงเฉพาะเมนูที่เปิดใช้งาน (และกรองตามสิทธิ์การมองเห็น)
    public function readActive($role_access = null) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE is_active = '1' ";
        
        if ($role_access) {
            $query .= " AND (role_access = 'all' OR role_access = :role_access) ";
        }
        
        $query .= " ORDER BY sort_order ASC, id ASC";
        $stmt = $this->conn->prepare($query);
        
        if ($role_access) {
            $stmt->bindParam(":role_access", $role_access);
        }
        
        $stmt->execute();
        return $stmt;
    }

    // ดึงเมนูย่อย (Sub-menu)
    public function readSubMenus($parent_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE parent_id = :parent_id AND is_active = '1' ORDER BY sort_order ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->execute();
        return $stmt;
    }

    // เพิ่มเมนูใหม่
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (menu_name, icon, action_name, is_active, sort_order, parent_id, role_access) 
                  VALUES (:menu_name, :icon, :action_name, :is_active, :sort_order, :parent_id, :role_access)";
        $stmt = $this->conn->prepare($query);
        
        $parent_id = isset($data['parent_id']) && $data['parent_id'] !== '' ? $data['parent_id'] : 0;
        $role_access = isset($data['role_access']) && $data['role_access'] !== '' ? $data['role_access'] : 'all';

        $stmt->bindParam(":menu_name", $data['menu_name']);
        $stmt->bindParam(":icon", $data['icon']);
        $stmt->bindParam(":action_name", $data['action_name']);
        $stmt->bindParam(":is_active", $data['is_active']);
        $stmt->bindParam(":sort_order", $data['sort_order']);
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->bindParam(":role_access", $role_access);
        
        return $stmt->execute();
    }

    // อัปเดตเมนู
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET menu_name = :menu_name, icon = :icon, action_name = :action_name, 
                      is_active = :is_active, sort_order = :sort_order,
                      parent_id = :parent_id, role_access = :role_access
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        $parent_id = isset($data['parent_id']) && $data['parent_id'] !== '' ? $data['parent_id'] : 0;
        $role_access = isset($data['role_access']) && $data['role_access'] !== '' ? $data['role_access'] : 'all';

        $stmt->bindParam(":menu_name", $data['menu_name']);
        $stmt->bindParam(":icon", $data['icon']);
        $stmt->bindParam(":action_name", $data['action_name']);
        $stmt->bindParam(":is_active", $data['is_active']);
        $stmt->bindParam(":sort_order", $data['sort_order']);
        $stmt->bindParam(":parent_id", $parent_id);
        $stmt->bindParam(":role_access", $role_access);
        $stmt->bindParam(":id", $id);
        
        return $stmt->execute();
    }

    // เปิด/ปิด สถานะเมนู
    public function toggleActive($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET is_active = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // ลบเมนู
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }
}
?>