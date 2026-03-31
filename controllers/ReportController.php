<?php
// ==========================================
// ชื่อไฟล์: ReportController.php
// ที่อยู่ไฟล์: controllers/ReportController.php
// ==========================================

// โหลด mPDF ที่ติดตั้งผ่าน Composer
require_once __DIR__ . '/../vendor/autoload.php';
require_once 'models/Employee.php';

class ReportController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ฟังก์ชันสร้าง PDF ก.พ. 7
    public function printKP7() {
        if(isset($_GET['id'])) {
            $employeeModel = new Employee($this->db);
            $empData = $employeeModel->readOne($_GET['id']);
            
            if($empData) {
                // 1. ตั้งค่า mPDF 
                $mpdf = new \Mpdf\Mpdf([
                    'mode' => 'utf-8',
                    'format' => 'A4',
                    'default_font_size' => 12,
                    'default_font' => 'garuda', // ใช้ฟอนต์ garuda ที่รองรับภาษาไทยในตัว
                    'margin_left' => 15,
                    'margin_right' => 15,
                    'margin_top' => 15,
                    'margin_bottom' => 15,
                ]);

                // 2. นำเข้าข้อมูลและจัดรูปแบบจากไฟล์ View
                ob_start();
                require 'views/report/print_kp7.php';
                $html = ob_get_clean();

                // 3. สั่งให้ mPDF สร้าง PDF จาก HTML
                $mpdf->WriteHTML($html);

                // 4. แสดงผลเป็น PDF (I = เปิดใน Browser ทันที)
                $mpdf->Output('KP7_' . $empData['emp_code'] . '.pdf', 'I');
                exit();
                
            } else {
                $_SESSION['message'] = "ไม่พบข้อมูลสำหรับพิมพ์รายงาน";
                $_SESSION['message_type'] = "warning";
                header("Location: index.php?action=employees");
                exit();
            }
        }
    }
}
?>