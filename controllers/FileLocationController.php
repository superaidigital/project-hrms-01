<?php
// ==========================================
// ชื่อไฟล์: FileLocationController.php
// ที่อยู่ไฟล์: controllers/FileLocationController.php
// ==========================================

class FileLocationController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        // ดึงข้อมูลแฟ้มประวัติพนักงาน
        // สมมติฐาน: ระบบนี้อาจจะไว้เก็บ "เลขที่ตู้" หรือ "เลขที่แฟ้ม" กระดาษตัวจริงของพนักงาน
        
        $search = isset($_GET['search']) ? trim($_GET['search']) : '';

        // ดึงพนักงานพร้อมข้อมูลสถานที่เก็บแฟ้ม (สมมติว่ามีตาราง file_locations ผูกกับพนักงาน)
        // หรืออาจจะเก็บอยู่ในตาราง employees ตรงๆ
        $query = "SELECT e.id, e.emp_code, e.first_name, e.last_name, e.national_id, 
                         w.department, fl.cabinet_number, fl.folder_number, fl.status
                  FROM employees e
                  LEFT JOIN emp_work_history w ON w.id = (
                      SELECT id FROM emp_work_history WHERE employee_id = e.id ORDER BY id DESC LIMIT 1
                  )
                  LEFT JOIN file_locations fl ON e.id = fl.employee_id";
                  
        if (!empty($search)) {
            $query .= " WHERE e.first_name LIKE :search OR e.last_name LIKE :search OR e.emp_code LIKE :search";
        }
        
        $query .= " ORDER BY e.first_name ASC";
        
        try {
            $stmt = $this->db->prepare($query);
            if (!empty($search)) {
                $search_param = "%{$search}%";
                $stmt->bindParam(':search', $search_param);
            }
            $stmt->execute();
            $file_locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // กรณีไม่มีตาราง file_locations
            $file_locations = [];
            error_log("File Location Query Error: " . $e->getMessage());
        }

        require_once 'views/file_location/index.php';
    }

    public function print() {
         // สำหรับพิมพ์ Label ติดหน้าแฟ้มประวัติ หรือรายการแฟ้มประวัติ
         // (โค้ดคล้ายๆ กับ ReportController)
         require_once 'views/file_location/print.php';
    }
    
    // หมายเหตุ: โค้ดนี้เป็นการเขียนขึ้นแบบสมมติฐานจากชื่อไฟล์ หากคุณมีโครงสร้างตารางจริง 
    // ให้ปรับ Query ให้ตรงกับโครงสร้างนั้นครับ โดยยึดหลัก Prepared Statement ไว้เสมอ
}
?>