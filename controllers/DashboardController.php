<?php
class DashboardController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        
        // ตรวจสอบสิทธิ์! ต้องล็อกอินก่อนถึงจะดูหน้านี้ได้
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
    }

    public function index() {
        // ตัวแปรเก็บค่าสถิติต่างๆ
        $stats = [
            'total_employees' => 0,
            'total_manpower' => 0,
            'total_departments' => 0,
            'total_users' => 0
        ];

        try {
            // 1. นับจำนวนพนักงานทั้งหมด (สมมติตารางชื่อ employees)
            $stmt = $this->db->query("SELECT COUNT(id) as total FROM employees");
            $stats['total_employees'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // 2. นับจำนวนกรอบอัตรากำลังทั้งหมด (สมมติตารางชื่อ manpower)
            $stmt = $this->db->query("SELECT COUNT(id) as total FROM manpower");
            $stats['total_manpower'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // 3. นับจำนวนหน่วยงาน/แผนก (สมมติตารางชื่อ departments)
            $stmt = $this->db->query("SELECT COUNT(id) as total FROM departments");
            $stats['total_departments'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

            // 4. นับจำนวนผู้ใช้งานในระบบ (ที่เปิดใช้งานอยู่)
            $stmt = $this->db->query("SELECT COUNT(id) as total FROM users WHERE is_active = '1'");
            $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;

        } catch (PDOException $e) {
            // กรณีที่บางตารางยังไม่ถูกสร้าง ให้ข้ามไปก่อนเพื่อไม่ให้หน้าจอขาว
        }

        // ดึงหน้า View มาแสดงผลพร้อมส่งข้อมูล $stats ไปให้
        require_once 'views/dashboard/index.php';
    }
}
?>