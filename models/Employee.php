<?php
class Employee {
    private $conn;
    private $table_name = "employees";

    public function __construct($db) {
        $this->conn = $db;
    }

    // ========================================================
    // 1. ฟังก์ชันจัดการข้อมูลหลัก (CRUD)
    // ========================================================

    // นับจำนวนบุคลากรทั้งหมด (รองรับการค้นหาและกรองแผนก) สำหรับแบ่งหน้า
    public function countEmployees($search = "", $dept_type = "") {
        $query = "SELECT COUNT(DISTINCT e.id) as total FROM " . $this->table_name . " e ";
        
        // ถ้ามีการค้นหาหรือกรองแผนก ต้อง Join ตารางประวัติการทำงานเพื่อหาตำแหน่ง/แผนกล่าสุด
        if (!empty($search) || !empty($dept_type)) {
            $query .= " LEFT JOIN emp_work_history w ON w.id = (
                            SELECT id FROM emp_work_history WHERE employee_id = e.id ORDER BY id DESC LIMIT 1
                        )
                        LEFT JOIN departments d ON w.department = d.name ";
        }

        $where_clauses = [];
        $params = [];
        
        if (!empty($search)) {
            $where_clauses[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.emp_code LIKE ? OR w.position_name LIKE ?)";
            $search_param = "%{$search}%";
            array_push($params, $search_param, $search_param, $search_param, $search_param);
        }
        
        if (!empty($dept_type)) {
            $where_clauses[] = "d.type = ?";
            $params[] = $dept_type;
        }

        if (count($where_clauses) > 0) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }

        $stmt = $this->conn->prepare($query);
        
        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindValue($i + 1, $params[$i]);
        }
        
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] ?? 0;
    }

    // ดึงข้อมูลบุคลากร 1 คน พร้อมประวัติทั้งหมด (สำหรับหน้า View/Edit ก.พ. 7)
    public function readOne($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);

        if($emp) {
            // ดึงข้อมูลจากตารางย่อยต่างๆ มารวมใน Array เดียว
            $emp['family'] = $this->getRelationData("emp_family", $id, true); // true = มีแค่แถวเดียว
            $emp['education'] = $this->getRelationData("emp_education", $id);
            $emp['training'] = $this->getRelationData("emp_training", $id);
            $emp['work_history'] = $this->getRelationData("emp_work_history", $id, false, "start_date ASC");
            $emp['leave'] = $this->getRelationData("emp_leave", $id, false, "leave_year DESC");
            $emp['acting'] = $this->getRelationData("emp_acting", $id);
            $emp['evaluation'] = $this->getRelationData("emp_evaluation", $id, false, "eval_year DESC, eval_round DESC");
            $emp['decoration'] = $this->getRelationData("emp_decoration", $id, false, "received_year ASC");
            $emp['disciplinary'] = $this->getRelationData("emp_disciplinary", $id, false, "incident_date DESC");
            $emp['license'] = $this->getRelationData("emp_license", $id);
        }

        return $emp;
    }

    // เพิ่มบุคลากรใหม่ลงฐานข้อมูล
    public function create($data) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (emp_code, national_id, prefix, gender, dob, first_name, last_name, phone, email, avatar, status) 
                  VALUES (:emp_code, :national_id, :prefix, :gender, :dob, :first_name, :last_name, :phone, :email, :avatar, :status)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":emp_code", $data['emp_code']);
        $stmt->bindParam(":national_id", $data['national_id']);
        $stmt->bindParam(":prefix", $data['prefix']);
        $stmt->bindParam(":gender", $data['gender']);
        $stmt->bindParam(":dob", $data['dob']);
        $stmt->bindParam(":first_name", $data['first_name']);
        $stmt->bindParam(":last_name", $data['last_name']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":avatar", $data['avatar']);
        $stmt->bindParam(":status", $data['status']);

        if($stmt->execute()) {
            return $this->conn->lastInsertId(); // คืนค่า ID ที่เพิ่งสร้าง
        }
        return false;
    }

    // อัปเดตข้อมูลทั่วไปของบุคลากร
    public function update($id, $data) {
        $query = "UPDATE " . $this->table_name . " 
                  SET emp_code=:emp_code, national_id=:national_id, prefix=:prefix, gender=:gender, 
                      dob=:dob, first_name=:first_name, last_name=:last_name, phone=:phone, email=:email";
        
        // อัปเดตรูปภาพเฉพาะเมื่อมีการอัปโหลดรูปใหม่มา (หรือสั่งลบโดยส่ง null)
        if(array_key_exists('avatar', $data)) {
            $query .= ", avatar=:avatar";
        }
        
        $query .= " WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":emp_code", $data['emp_code']);
        $stmt->bindParam(":national_id", $data['national_id']);
        $stmt->bindParam(":prefix", $data['prefix']);
        $stmt->bindParam(":gender", $data['gender']);
        $stmt->bindParam(":dob", $data['dob']);
        $stmt->bindParam(":first_name", $data['first_name']);
        $stmt->bindParam(":last_name", $data['last_name']);
        $stmt->bindParam(":phone", $data['phone']);
        $stmt->bindParam(":email", $data['email']);
        $stmt->bindParam(":id", $id);
        
        if(array_key_exists('avatar', $data)) {
            $stmt->bindParam(":avatar", $data['avatar']);
        }

        return $stmt->execute();
    }

    // ลบข้อมูลบุคลากร (จะทำการลบข้อมูลในตารางย่อยทั้งหมดด้วย)
    public function delete($id) {
        try {
            $this->conn->beginTransaction();

            // 1. ลบข้อมูลจากตารางย่อยทั้งหมด
            $related_tables = ["emp_family", "emp_education", "emp_training", "emp_work_history", "emp_leave", "emp_acting", "emp_evaluation", "emp_decoration", "emp_disciplinary", "emp_license"];
            foreach($related_tables as $table) {
                $stmt = $this->conn->prepare("DELETE FROM {$table} WHERE employee_id = :id");
                $stmt->bindParam(':id', $id);
                $stmt->execute();
            }

            // 2. ลบข้อมูลหลัก
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            $this->conn->commit();
            return true;
        } catch (PDOException $e) {
            $this->conn->rollBack();
            return false;
        }
    }


    // ========================================================
    // 2. ฟังก์ชันตรวจสอบความถูกต้อง และ อัปเดตสถานะ
    // ========================================================

    // เช็คว่าเลขบัตรประชาชนนี้มีอยู่ในระบบแล้วหรือยัง
    public function isNationalIdExists($national_id, $exclude_id = null) {
        $query = "SELECT id FROM " . $this->table_name . " WHERE national_id = :national_id";
        if ($exclude_id) {
            $query .= " AND id != :exclude_id"; // ยกเว้น ID ของตัวเอง (กรณีแก้ไขข้อมูล)
        }
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':national_id', $national_id);
        if ($exclude_id) $stmt->bindParam(':exclude_id', $exclude_id);
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    // อัปเดตสถานะการทำงาน (ปฏิบัติงาน, ลาออก, เกษียณ ฯลฯ)
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":id", $id);
        return $stmt->execute();
    }

    // เปิด/ปิดการใช้งานระบบบัญชี (Active / Inactive)
    public function toggleActive($id, $status) {
        try {
            // อย่าลืมรันคำสั่ง SQL เพิ่มคอลัมน์นี้ในฐานข้อมูล: 
            // ALTER TABLE `employees` ADD `is_active` TINYINT(1) NOT NULL DEFAULT '1' AFTER `status`;
            $query = "UPDATE " . $this->table_name . " SET is_active = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":id", $id);
            return $stmt->execute();
        } catch(PDOException $e) {
            // หากเกิด Error (เช่น ตารางไม่มีคอลัมน์) จะเข้า catch ป้องกันหน้าจอขาว
            error_log("Error toggling employee active status: " . $e->getMessage());
            return false;
        }
    }


    // ========================================================
    // 3. ฟังก์ชันจัดการข้อมูลตารางย่อย (Relations)
    // ========================================================

    // Helper: ดึงข้อมูลจากตารางย่อย
    private function getRelationData($table, $emp_id, $singleRow = false, $orderBy = "") {
        try {
            $query = "SELECT * FROM {$table} WHERE employee_id = :emp_id";
            if (!empty($orderBy)) $query .= " ORDER BY " . $orderBy;
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":emp_id", $emp_id);
            $stmt->execute();
            return $singleRow ? $stmt->fetch(PDO::FETCH_ASSOC) : $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            return $singleRow ? [] : []; // คืนค่าว่างถ้ายังไม่มีตาราง
        }
    }

    // อัปเดตข้อมูลครอบครัว (ตารางที่มีแค่ 1 แถวต่อพนักงาน 1 คน)
    public function updateFamily($emp_id, $data) {
        try {
            $checkStmt = $this->conn->prepare("SELECT id FROM emp_family WHERE employee_id = :emp_id");
            $checkStmt->execute([':emp_id' => $emp_id]);
            
            if($checkStmt->rowCount() > 0) {
                // อัปเดตของเดิม
                $query = "UPDATE emp_family SET father_name=:father_name, mother_name=:mother_name, spouse_name=:spouse_name, children_count=:children_count WHERE employee_id=:emp_id";
            } else {
                // เพิ่มใหม่
                $query = "INSERT INTO emp_family (employee_id, father_name, mother_name, spouse_name, children_count) VALUES (:emp_id, :father_name, :mother_name, :spouse_name, :children_count)";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":emp_id", $emp_id);
            $stmt->bindParam(":father_name", $data['father_name']);
            $stmt->bindParam(":mother_name", $data['mother_name']);
            $stmt->bindParam(":spouse_name", $data['spouse_name']);
            $stmt->bindParam(":children_count", $data['children_count']);
            return $stmt->execute();
        } catch(PDOException $e) {
            return false;
        }
    }

    // อัปเดตข้อมูลตารางที่มีหลายแถว (ลบของเก่าทิ้งทั้งหมด แล้วบันทึกของใหม่เข้าไป)
    public function updateRelations($table_name, $emp_id, $dataArrays, $columnsArray) {
        try {
            // 1. ลบข้อมูลเก่าทั้งหมดของพนักงานคนนี้
            $delStmt = $this->conn->prepare("DELETE FROM {$table_name} WHERE employee_id = :emp_id");
            $delStmt->bindParam(":emp_id", $emp_id);
            $delStmt->execute();

            // 2. ถ้าไม่มีข้อมูลส่งมาเลย หรือข้อมูลชุดแรกไม่ใช่ Array ให้จบการทำงาน ป้องกันบั๊ก Fatal Error
            if(empty($dataArrays) || !isset($dataArrays[0]) || !is_array($dataArrays[0])) return true;

            // 3. วนลูปบันทึกข้อมูลใหม่ทีละแถว
            $rowCount = count($dataArrays[0]); // จำนวนแถวที่เพิ่มเข้ามา
            for($i = 0; $i < $rowCount; $i++) {
                
                // เช็คว่าแถวนี้ว่างทุกช่องไหม (ถ้าช่องข้อมูลว่างหมด ให้ข้ามไป ไม่บันทึกขยะ)
                $isEmptyRow = true;
                foreach($dataArrays as $colData) {
                    if(isset($colData[$i]) && !empty(trim($colData[$i]))) {
                        $isEmptyRow = false;
                        break;
                    }
                }
                if($isEmptyRow) continue;

                // เตรียมคำสั่ง SQL
                $colNames = implode(",", $columnsArray);
                $placeholders = implode(",", array_fill(0, count($columnsArray), "?"));
                
                $sql = "INSERT INTO {$table_name} (employee_id, {$colNames}) VALUES (?, {$placeholders})";
                $stmt = $this->conn->prepare($sql);
                
                // นำค่าใส่ลงใน Parameter
                $params = [$emp_id];
                foreach($dataArrays as $colData) {
                    $params[] = trim($colData[$i] ?? '');
                }
                
                $stmt->execute($params);
            }
            return true;
        } catch(PDOException $e) {
            // กรณีตารางยังไม่ถูกสร้างในฐานข้อมูล ให้ข้ามการ Error ไป
            error_log("Error updating relations for table {$table_name}: " . $e->getMessage());
            return false; 
        }
    }

}
?>