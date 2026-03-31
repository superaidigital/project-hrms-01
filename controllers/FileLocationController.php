<?php
// ==========================================
// ชื่อไฟล์: FileLocationController.php
// ที่อยู่ไฟล์: controllers/FileLocationController.php
// ==========================================

require_once 'models/Employee.php';

class FileLocationController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // หน้าแรก ศูนย์จัดการแฟ้ม
    public function index() {
        $employeeModel = new Employee($this->db);
        $cabinets = $employeeModel->getCabinetSummary();
        require_once 'views/file_location/index.php';
    }

    // จัดการหน้า Print (แยกตามประเภท)
    public function print() {
        $type = $_GET['type'] ?? '';
        $cabinet = $_GET['cabinet'] ?? '';
        $shelf = $_GET['shelf'] ?? '';

        $employeeModel = new Employee($this->db);
        $employees = [];

        if($cabinet != '' && $shelf != '') {
            $employees = $employeeModel->getEmployeesByCabinet($cabinet, $shelf);
        }

        // นำไปสู่หน้าจอสำหรับพิมพ์
        require_once 'views/file_location/print.php';
    }
}
?>