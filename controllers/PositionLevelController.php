<?php
// ==========================================
// ชื่อไฟล์: PositionLevelController.php
// ที่อยู่ไฟล์: controllers/PositionLevelController.php
// ==========================================

require_once 'models/PositionLevel.php';

class PositionLevelController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        
        // ตรวจสอบสิทธิ์ ต้องเป็น admin
        if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้!";
            $_SESSION['message_type'] = "danger";
            header("Location: index.php?action=dashboard");
            exit();
        }
    }

    public function index() {
        $levelModel = new PositionLevel($this->db);
        $stmt = $levelModel->readAll();
        $position_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/position_level/index.php';
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $levelModel = new PositionLevel($this->db);
            $data = [
                'name' => trim($_POST['name']),
                'type' => $_POST['type']
            ];

            if($levelModel->create($data)) {
                $_SESSION['message'] = "เพิ่มข้อมูลระดับตำแหน่งเรียบร้อยแล้ว!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการเพิ่มข้อมูล";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=position_levels");
            exit();
        }
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $levelModel = new PositionLevel($this->db);
            $id = $_POST['id'];
            $data = [
                'name' => trim($_POST['name']),
                'type' => $_POST['type']
            ];

            if($levelModel->update($id, $data)) {
                $_SESSION['message'] = "อัปเดตข้อมูลระดับตำแหน่งสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=position_levels");
            exit();
        }
    }

    public function delete() {
        if(isset($_GET['id'])) {
            $levelModel = new PositionLevel($this->db);
            if($levelModel->delete($_GET['id'])) {
                $_SESSION['message'] = "ลบข้อมูลระดับตำแหน่งเรียบร้อยแล้ว!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบข้อมูล";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=position_levels");
            exit();
        }
    }
}
?>