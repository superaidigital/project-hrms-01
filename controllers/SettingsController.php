<?php
// ==========================================
// ชื่อไฟล์: SettingsController.php
// ที่อยู่ไฟล์: controllers/SettingsController.php
// ==========================================

class SettingsController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // 🔒 ตรวจสอบสิทธิ์: หน้าตั้งค่าเข้าได้เฉพาะ Admin เท่านั้น
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้าตั้งค่าระบบ";
            $_SESSION['message_type'] = "danger";
            header("Location: index.php?action=dashboard");
            exit();
        }
    }

    // 1. แสดงหน้าตั้งค่า (Load View)
    public function index() {
        // กำหนดค่าเริ่มต้น (Fallback) ป้องกัน Error Undefined variable
        $maintenance_mode = 'off';
        $system_name = 'HRMS Sisaket';

        try {
            // ดึงข้อมูลการตั้งค่าทั้งหมดจากตาราง system_settings
            $stmt = $this->db->query("SELECT setting_key, setting_value FROM system_settings");
            $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // คืนค่าเป็น Array แบบ [key => value]

            if (isset($settings['maintenance_mode'])) {
                $maintenance_mode = $settings['maintenance_mode'];
            }
            if (isset($settings['system_name'])) {
                $system_name = $settings['system_name'];
            }

        } catch (PDOException $e) {
            // กรณีตาราง system_settings ยังไม่ถูกสร้างในฐานข้อมูล ให้ใช้ค่าเริ่มต้นไปก่อน
        }

        require_once 'views/settings/index.php';
    }

    // 2. บันทึกการตั้งค่า (Update)
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $new_maintenance_mode = isset($_POST['maintenance_mode']) ? $_POST['maintenance_mode'] : 'off';
            $new_system_name = isset($_POST['system_name']) ? trim($_POST['system_name']) : 'HRMS Sisaket';

            try {
                // สร้างตารางอัตโนมัติหากยังไม่มี (เพื่อป้องกัน Error)
                $this->db->exec("CREATE TABLE IF NOT EXISTS system_settings (
                    setting_key VARCHAR(50) PRIMARY KEY,
                    setting_value TEXT NOT NULL
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

                // บันทึกโหมดปิดปรับปรุง (Maintenance Mode)
                $stmt = $this->db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('maintenance_mode', :val) ON DUPLICATE KEY UPDATE setting_value = :val");
                $stmt->execute([':val' => $new_maintenance_mode]);

                // บันทึกชื่อระบบ (System Name) เผื่อมีการตั้งชื่อระบบในอนาคต
                $stmt2 = $this->db->prepare("INSERT INTO system_settings (setting_key, setting_value) VALUES ('system_name', :val) ON DUPLICATE KEY UPDATE setting_value = :val");
                $stmt2->execute([':val' => $new_system_name]);

                $_SESSION['message'] = "บันทึกการตั้งค่าระบบเรียบร้อยแล้ว";
                $_SESSION['message_type'] = "success";

            } catch (PDOException $e) {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการบันทึกการตั้งค่า: " . $e->getMessage();
                $_SESSION['message_type'] = "danger";
            }
            
            header("Location: index.php?action=settings");
            exit();
        }
    }
}
?>