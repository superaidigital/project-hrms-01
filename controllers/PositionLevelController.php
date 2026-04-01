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
    }

    public function index() {
        $levelModel = new PositionLevel($this->db);
        $stmt = $levelModel->readAll();
        $position_levels = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/position_level/index.php';
    }

    public function create() {
        // หากต้องการให้แสดงฟอร์มสร้างแบบแยกหน้า
        // แต่ดูจาก index.php ส่วนใหญ่หน้าตารางมักจะมี Modal สำหรับสร้าง
        require_once 'views/position_level/form.php'; 
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $levelModel = new PositionLevel($this->db);
            
            // 🛡️ [ความปลอดภัย]: ตัดช่องว่างหน้าหลัง ป้องกันเผลอพิมพ์เว้นวรรค
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];

            // 🛡️ [เช็คข้อมูลซ้ำ]: ป้องกันการเพิ่มระดับตำแหน่งซ้ำกัน
            $checkStmt = $this->db->prepare("SELECT id FROM position_levels WHERE name = :name LIMIT 1");
            $checkStmt->execute([':name' => $data['name']]);
            if ($checkStmt->rowCount() > 0) {
                $_SESSION['message'] = "ชื่อระดับตำแหน่งนี้มีอยู่ในระบบแล้ว!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=position_levels"); // กลับไปหน้า index (หรือหน้า form)
                exit();
            }

            if ($levelModel->create($data)) {
                $_SESSION['message'] = "เพิ่มข้อมูลระดับตำแหน่งสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=position_levels");
            exit();
        }
    }

    public function edit() {
        if (isset($_GET['id'])) {
            $levelModel = new PositionLevel($this->db);
            $position_level = $levelModel->readOne($_GET['id']);
            
            if (!$position_level) {
                header("Location: index.php?action=position_levels");
                exit();
            }
            
            require_once 'views/position_level/form.php';
        }
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $levelModel = new PositionLevel($this->db);
            $id = $_POST['id'];
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'description' => trim($_POST['description'] ?? '')
            ];

            // 🛡️ [เช็คข้อมูลซ้ำ]: ป้องกันแก้ชื่อไปซ้ำกับระดับอื่น
            $checkStmt = $this->db->prepare("SELECT id FROM position_levels WHERE name = :name AND id != :id LIMIT 1");
            $checkStmt->execute([':name' => $data['name'], ':id' => $id]);
            if ($checkStmt->rowCount() > 0) {
                $_SESSION['message'] = "ชื่อระดับตำแหน่งนี้ซ้ำกับข้อมูลอื่นในระบบ!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=position_levels");
                exit();
            }

            if ($levelModel->update($id, $data)) {
                $_SESSION['message'] = "แก้ไขข้อมูลระดับตำแหน่งสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=position_levels");
            exit();
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $levelModel = new PositionLevel($this->db);
            
            // 🛡️ [แก้บั๊กก่อนลบ]: เช็คก่อนว่ามีตำแหน่ง (Manpower) ผูกกับระดับนี้อยู่ไหม (ถ้ามีห้ามลบ)
            $levelData = $levelModel->readOne($id);
            if ($levelData) {
                $checkStmt = $this->db->prepare("SELECT id FROM manpower WHERE level = :level_name LIMIT 1");
                $checkStmt->execute([':level_name' => $levelData['name']]);
                
                if ($checkStmt->rowCount() > 0) {
                     $_SESSION['message'] = "ไม่สามารถลบได้ เนื่องจากมีกรอบอัตรากำลังถูกกำหนดในระดับนี้อยู่!";
                     $_SESSION['message_type'] = "danger";
                     header("Location: index.php?action=position_levels");
                     exit();
                }
            }

            if ($levelModel->delete($id)) {
                $_SESSION['message'] = "ลบข้อมูลระดับตำแหน่งสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาด ไม่สามารถลบข้อมูลได้!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=position_levels");
            exit();
        }
    }
}
?>