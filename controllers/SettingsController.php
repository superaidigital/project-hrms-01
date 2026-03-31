<?php
class SettingsController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        
        // --- 🛡️ เช็คสิทธิ์: ต้องเป็น Admin เท่านั้นถึงเข้าหน้านี้ได้ ---
        if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้าตั้งค่าระบบ!";
            $_SESSION['message_type'] = "danger";
            header("Location: index.php?action=dashboard");
            exit();
        }
    }

    // 1. หน้าแผงควบคุมหลัก (Settings Dashboard)
    public function index() {
        // ดึงสถานะ เปิด/ปิด ระบบปัจจุบัน
        $stmt = $this->db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
        $stmt->execute();
        $mode = $stmt->fetch(PDO::FETCH_ASSOC);
        $maintenance_mode = $mode ? $mode['setting_value'] : 'off';

        require_once 'views/settings/index.php';
    }

    // 2. ฟังก์ชัน เปิด/ปิด ระบบ (Maintenance Mode)
    public function toggleMaintenance() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $status = $_POST['maintenance_status'] == 'on' ? 'on' : 'off';
            
            $stmt = $this->db->prepare("UPDATE system_settings SET setting_value = ? WHERE setting_key = 'maintenance_mode'");
            $stmt->execute([$status]);

            $_SESSION['message'] = $status == 'on' ? "เปิดโหมดปิดปรับปรุงระบบแล้ว (ผู้ใช้ทั่วไปจะเข้าไม่ได้)" : "เปิดระบบให้ใช้งานตามปกติแล้ว";
            $_SESSION['message_type'] = "success";
            header("Location: index.php?action=settings");
            exit();
        }
    }

    // 3. ฟังก์ชัน สำรองข้อมูลฐานข้อมูล (Backup Database)
    public function backupDatabase() {
        $tables = [];
        $query = $this->db->query('SHOW TABLES');
        while($row = $query->fetch(PDO::FETCH_NUM)) {
            $tables[] = $row[0];
        }

        $sqlScript = "-- HRMS Sisaket PAO Database Backup\n";
        $sqlScript .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";

        foreach ($tables as $table) {
            // ดึงโครงสร้างตาราง
            $query = $this->db->query('SHOW CREATE TABLE ' . $table);
            $row = $query->fetch(PDO::FETCH_NUM);
            $sqlScript .= "\nDROP TABLE IF EXISTS `$table`;\n" . $row[1] . ";\n\n";

            // ดึงข้อมูลในตาราง
            $query = $this->db->query('SELECT * FROM ' . $table);
            $columnCount = $query->columnCount();
            
            $rowCount = 0;
            while($row = $query->fetch(PDO::FETCH_NUM)) {
                if ($rowCount % 100 == 0) {
                    $sqlScript .= "INSERT INTO `$table` VALUES ";
                }
                
                $sqlScript .= "(";
                for ($j = 0; $j < $columnCount; $j++) {
                    $row[$j] = $row[$j] ? addslashes($row[$j]) : NULL;
                    $row[$j] = str_replace("\n","\\n",$row[$j]);
                    if (isset($row[$j])) {
                        $sqlScript .= '"' . $row[$j] . '"' ;
                    } else {
                        $sqlScript .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ")";

                if (($rowCount + 1) % 100 == 0 || ($rowCount + 1) == $query->rowCount()) {
                    $sqlScript .= ";\n";
                } else {
                    $sqlScript .= ",\n";
                }
                $rowCount++;
            }
            $sqlScript .= "\n";
        }

        // สั่งให้ Browser ดาวน์โหลดไฟล์ .sql
        $backup_file_name = 'HRMS_Backup_' . date('Ymd_His') . '.sql';
        header('Content-Type: application/x-sql');
        header('Content-Disposition: attachment; filename="' . $backup_file_name . '"');
        echo $sqlScript;
        exit();
    }
}
?>