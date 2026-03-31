<?php
require_once 'models/Department.php';

class DepartmentController {
    private $db;

    public function __construct($db) { $this->db = $db; }

    public function index() {
        $deptModel = new Department($this->db);
        $stmt = $deptModel->readAll();
        $departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total = count($departments);
        $count_central = 0; $count_school = 0; $count_health = 0;
        foreach($departments as $d) {
            if($d['dept_type'] == 'central') $count_central++;
            if($d['dept_type'] == 'school') $count_school++;
            if($d['dept_type'] == 'health') $count_health++;
        }
        require_once 'views/department/index.php';
    }

    public function create() { require_once 'views/department/form.php'; }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $deptModel = new Department($this->db);
            $name = trim($_POST['name'] ?? '');

            if ($deptModel->isNameExists($name)) {
                $_SESSION['message'] = "ชื่อหน่วยงานนี้มีอยู่ในระบบแล้ว!";
                $_SESSION['message_type'] = "warning";
                header("Location: index.php?action=department_create");
                exit();
            }

            $data = [
                'name' => $name,
                'dept_type' => $_POST['dept_type'] ?? 'central',
                'amphoe' => $_POST['amphoe'] ?? null,
                'dept_code' => $_POST['dept_code'] ?? null,
                'short_name' => $_POST['short_name'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'email' => $_POST['email'] ?? null,
                'address' => $_POST['address'] ?? null,
                'latitude' => $_POST['latitude'] ?? null,   // เพิ่มบรรทัดนี้
                'longitude' => $_POST['longitude'] ?? null, // เพิ่มบรรทัดนี้
                'status' => $_POST['status'] ?? 'active'
            ];

            if($deptModel->create($data)) {
                $_SESSION['message'] = "เพิ่มหน่วยงานใหม่สำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=departments");
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการบันทึก!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=department_create");
            }
            exit();
        }
    }

    public function edit() {
        if(isset($_GET['id']) && !empty($_GET['id'])) {
            $deptModel = new Department($this->db);
            $department = $deptModel->readOne($_GET['id']);
            if(!$department) {
                $_SESSION['message'] = "ไม่พบข้อมูลหน่วยงานนี้";
                $_SESSION['message_type'] = "danger";
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
            $name = trim($_POST['name'] ?? '');

            if ($deptModel->isNameExists($name, $id)) {
                $_SESSION['message'] = "ชื่อหน่วยงานนี้ซ้ำกับข้อมูลอื่นในระบบ!";
                $_SESSION['message_type'] = "warning";
                header("Location: index.php?action=department_edit&id=".$id);
                exit();
            }

            $data = [
                'name' => $name,
                'dept_type' => $_POST['dept_type'] ?? 'central',
                'amphoe' => $_POST['amphoe'] ?? null,
                'dept_code' => $_POST['dept_code'] ?? null,
                'short_name' => $_POST['short_name'] ?? null,
                'phone' => $_POST['phone'] ?? null,
                'email' => $_POST['email'] ?? null,
                'address' => $_POST['address'] ?? null,
                'latitude' => $_POST['latitude'] ?? null,   // เพิ่มบรรทัดนี้
                'longitude' => $_POST['longitude'] ?? null, // เพิ่มบรรทัดนี้
                'status' => $_POST['status'] ?? 'active'
            ];

            if($deptModel->update($id, $data)) {
                $_SESSION['message'] = "แก้ไขข้อมูลสำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=departments");
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=department_edit&id=".$id);
            }
            exit();
        }
    }

    public function delete() {
        if(isset($_GET['id'])) {
            $deptModel = new Department($this->db);
            $id = $_GET['id'];
            try {
                if($deptModel->delete($id)) {
                    $_SESSION['message'] = "ลบข้อมูลสำเร็จ!";
                    $_SESSION['message_type'] = "success";
                }
            } catch (PDOException $e) {
                $_SESSION['message'] = "ไม่สามารถลบได้ เนื่องจากถูกใช้งานอยู่ในกรอบอัตรากำลัง";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=departments");
            exit();
        }
    }
}
?>