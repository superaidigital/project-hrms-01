<?php
// ==========================================
// ชื่อไฟล์: MenuController.php
// ที่อยู่ไฟล์: controllers/MenuController.php
// ==========================================

require_once 'models/SystemMenu.php';

class MenuController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
         // ตรวจสอบสิทธิ์ Admin (ถ้ามีระบบจัดการเมนู ควรให้เฉพาะ Admin)
         if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            $_SESSION['message_type'] = "danger";
            header("Location: index.php?action=dashboard");
            exit();
        }

        $menuModel = new SystemMenu($this->db);
        $stmt = $menuModel->readAll();
        $menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/menu/index.php';
    }

    public function toggleActive() {
        if (isset($_GET['id']) && isset($_GET['status'])) {
             // ตรวจสอบสิทธิ์ Admin 
             if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
                header("Location: index.php?action=dashboard");
                exit();
            }

            $id = $_GET['id'];
            // ป้องกันค่า status ไม่ถูกต้อง
            $status = $_GET['status'] == '1' ? '1' : '0'; 

            $menuModel = new SystemMenu($this->db);

            if ($menuModel->toggleActive($id, $status)) {
                $_SESSION['message'] = "อัปเดตสถานะเมนูสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาด ไม่สามารถเปลี่ยนสถานะได้!";
                $_SESSION['message_type'] = "danger";
            }
            
            header("Location: index.php?action=menus");
            exit();
        }
    }
    
    // หมายเหตุ: หากมีฟังก์ชัน create, store, edit, update, delete ก็ให้ทำในลักษณะเดียวกัน
    // โดยเน้นการป้องกันข้อมูลซ้ำ (URL/Route) และสิทธิ์การเข้าถึง (Admin)
}
?>