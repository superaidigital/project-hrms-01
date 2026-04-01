<?php
// ==========================================
// ชื่อไฟล์: DashboardController.php
// ที่อยู่ไฟล์: controllers/DashboardController.php
// ==========================================

class DashboardController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        // 1. กำหนดค่าเริ่มต้นให้กับตัวแปร $stats ป้องกัน Error Undefined variable
        $stats = [
            'total_employees'   => 0,
            'total_framework'   => 0,
            'total_manpower'    => 0, // เพิ่มตัวแปรนี้เพื่อรองรับ view
            'total_occupied'    => 0,
            'total_vacant'      => 0,
            'total_departments' => 0, // เพิ่มตัวแปรนี้เพื่อรองรับ view
            'total_users'       => 0  // เพิ่มตัวแปรนี้เพื่อรองรับ view
        ];

        try {
            // 2. ดึงจำนวนกรอบอัตรากำลังทั้งหมด (รวม ว่าง และ คนครอง)
            $stmtMp = $this->db->query("
                SELECT 
                    COUNT(*) as total_framework,
                    SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as total_occupied,
                    SUM(CASE WHEN status = 'vacant' THEN 1 ELSE 0 END) as total_vacant
                FROM manpower
            ");
            $mpData = $stmtMp->fetch(PDO::FETCH_ASSOC);
            
            if ($mpData) {
                $stats['total_framework'] = $mpData['total_framework'] ?? 0;
                $stats['total_manpower']  = $mpData['total_framework'] ?? 0; // เก็บค่าซ้ำไว้สำหรับรองรับตัวแปร 2 ชื่อ
                $stats['total_occupied']  = $mpData['total_occupied'] ?? 0;
                $stats['total_vacant']    = $mpData['total_vacant'] ?? 0;
            }

            // 3. ดึงจำนวนบุคลากรทั้งหมด (จากตาราง employees)
            try {
                $stmtEmp = $this->db->query("SELECT COUNT(*) FROM employees");
                $stats['total_employees'] = $stmtEmp->fetchColumn();
            } catch(PDOException $e) {
                // ถ้ายังไม่มีตาราง ให้ใช้จำนวนคนครองจากกรอบแทนชั่วคราว
                $stats['total_employees'] = $stats['total_occupied'];
            }

            // 4. ดึงจำนวนส่วนราชการ (จากตาราง departments)
            try {
                $stmtDept = $this->db->query("SELECT COUNT(*) FROM departments");
                $stats['total_departments'] = $stmtDept->fetchColumn();
            } catch(PDOException $e) {
                $stats['total_departments'] = 0;
            }

            // 5. ดึงจำนวนผู้ใช้งานระบบ (จากตาราง users)
            try {
                $stmtUser = $this->db->query("SELECT COUNT(*) FROM users");
                $stats['total_users'] = $stmtUser->fetchColumn();
            } catch(PDOException $e) {
                $stats['total_users'] = 0;
            }

        } catch (PDOException $e) {
            // กรณีเชื่อมต่อ DB ดึงสถิติไม่ได้ ให้แจ้ง Error
            $_SESSION['message'] = "ไม่สามารถดึงข้อมูลสถิติได้: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
        }

        // 6. โหลดหน้า View และส่งตัวแปร $stats ไปให้
        require_once 'views/dashboard/index.php';
    }
}
?>