<?php
// ==========================================
// ชื่อไฟล์: DepartmentController.php
// ที่อยู่ไฟล์: controllers/DepartmentController.php
// ==========================================

require_once 'models/Department.php';

class DepartmentController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        $deptModel = new Department($this->db);
        $stmt = $deptModel->readAll();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/department/index.php';
    }

    public function create() {
        require_once 'views/department/form.php';
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $deptModel = new Department($this->db);
            
            // 🛡️ [ความปลอดภัย]: ตัดช่องว่างหน้าหลัง ป้องกันเผลอพิมพ์เว้นวรรค
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => trim($_POST['type'] ?? '')
            ];

            // 🛡️ [เช็คข้อมูลซ้ำ]: ป้องกันการเพิ่มแผนกชื่อซ้ำกัน
            $checkStmt = $this->db->prepare("SELECT id FROM departments WHERE name = :name LIMIT 1");
            $checkStmt->execute([':name' => $data['name']]);
            if ($checkStmt->rowCount() > 0) {
                $_SESSION['message'] = "ชื่อส่วนราชการนี้มีอยู่ในระบบแล้ว!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=department_create");
                exit();
            }

            if ($deptModel->create($data)) {
                $_SESSION['message'] = "เพิ่มข้อมูลส่วนราชการสำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=departments");
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=department_create");
            }
            exit();
        }
    }

    public function edit() {
        if (isset($_GET['id'])) {
            $deptModel = new Department($this->db);
            $department = $deptModel->readOne($_GET['id']);
            
            if (!$department) {
                header("Location: index.php?action=departments");
                exit();
            }
            
            require_once 'views/department/form.php';
        }
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $deptModel = new Department($this->db);
            $id = $_POST['id'];
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'type' => trim($_POST['type'] ?? '')
            ];

            // 🛡️ [เช็คข้อมูลซ้ำ]: ป้องกันแก้ชื่อไปซ้ำกับแผนกอื่น
            $checkStmt = $this->db->prepare("SELECT id FROM departments WHERE name = :name AND id != :id LIMIT 1");
            $checkStmt->execute([':name' => $data['name'], ':id' => $id]);
            if ($checkStmt->rowCount() > 0) {
                $_SESSION['message'] = "ชื่อส่วนราชการนี้ซ้ำกับข้อมูลอื่นในระบบ!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=department_edit&id=" . $id);
                exit();
            }

            if ($deptModel->update($id, $data)) {
                $_SESSION['message'] = "แก้ไขข้อมูลส่วนราชการสำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=departments");
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=department_edit&id=" . $id);
            }
            exit();
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $deptModel = new Department($this->db);
            
            // 🛡️ [แก้บั๊กก่อนลบ]: เช็คก่อนว่ามีพนักงานสังกัดแผนกนี้อยู่ไหม (ถ้ามีห้ามลบ)
            // หมายเหตุ: ขึ้นอยู่กับตาราง emp_work_history หรือ manpower ว่าเก็บชื่อแผนกไว้ที่ไหน
            $deptData = $deptModel->readOne($id);
            if ($deptData) {
                $checkStmt = $this->db->prepare("SELECT id FROM manpower WHERE department = :dept_name LIMIT 1");
                $checkStmt->execute([':dept_name' => $deptData['name']]);
                
                if ($checkStmt->rowCount() > 0) {
                     $_SESSION['message'] = "ไม่สามารถลบได้ เนื่องจากมีกรอบอัตรากำลังสังกัดส่วนราชการนี้อยู่!";
                     $_SESSION['message_type'] = "danger";
                     header("Location: index.php?action=departments");
                     exit();
                }
            }

            if ($deptModel->delete($id)) {
                $_SESSION['message'] = "ลบข้อมูลส่วนราชการสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาด ไม่สามารถลบข้อมูลได้!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=departments");
            exit();
        }
    }
}
?>