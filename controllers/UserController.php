<?php
require_once 'models/User.php';

class UserController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        
        // ตรวจสอบสิทธิ์! ป้องกันไม่ให้ User ทั่วไปเข้าถึง Controller นี้
        if (session_status() === PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            $_SESSION['message'] = "คุณไม่มีสิทธิ์เข้าถึงเมนูจัดการผู้ใช้งาน!";
            $_SESSION['message_type'] = "danger";
            header("Location: index.php?action=dashboard");
            exit();
        }
    }

    public function index() {
        $userModel = new User($this->db);
        $users = $userModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/user/index.php';
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $userModel = new User($this->db);
            $data = [
                'username' => trim($_POST['username']),
                'password' => $_POST['password'],
                'full_name' => trim($_POST['full_name']),
                'role' => trim($_POST['role']),
                'is_active' => isset($_POST['is_active']) ? '1' : '0'
            ];

            if ($userModel->create($data)) {
                $_SESSION['message'] = "เพิ่มผู้ใช้งานระบบสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาด หรือ Username นี้มีในระบบแล้ว!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=users");
            exit();
        }
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $userModel = new User($this->db);
            $id = $_POST['id'];
            $data = [
                'full_name' => trim($_POST['full_name']),
                'role' => trim($_POST['role']),
                'is_active' => isset($_POST['is_active']) ? '1' : '0'
            ];

            if ($userModel->update($id, $data)) {
                $_SESSION['message'] = "อัปเดตข้อมูลผู้ใช้งานสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=users");
            exit();
        }
    }

    public function resetPassword() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $userModel = new User($this->db);
            $id = $_POST['id'];
            $new_password = $_POST['new_password'];

            if ($userModel->updatePassword($id, $new_password)) {
                $_SESSION['message'] = "รีเซ็ตรหัสผ่านสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการรีเซ็ตรหัสผ่าน!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=users");
            exit();
        }
    }

    public function toggleActive() {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $userModel = new User($this->db);
            $id = $_GET['id'];
            $status = $_GET['status'] == '1' ? '1' : '0';

            // ป้องกันไม่ให้ Admin ปิดการใช้งานตัวเอง
            if ($id == $_SESSION['user_id'] && $status == '0') {
                $_SESSION['message'] = "ไม่สามารถปิดการใช้งานบัญชีของคุณเองได้!";
                $_SESSION['message_type'] = "warning";
            } else if ($userModel->toggleActive($id, $status)) {
                $_SESSION['message'] = "เปลี่ยนสถานะผู้ใช้งานสำเร็จ!";
                $_SESSION['message_type'] = "success";
            }
            header("Location: index.php?action=users");
            exit();
        }
    }

    public function delete() {
        if (isset($_GET['id'])) {
            $userModel = new User($this->db);
            $id = $_GET['id'];

            // ป้องกันไม่ให้ Admin ลบตัวเอง
            if ($id == $_SESSION['user_id']) {
                $_SESSION['message'] = "ไม่สามารถลบบัญชีของคุณเองได้!";
                $_SESSION['message_type'] = "warning";
            } else if ($userModel->delete($id)) {
                $_SESSION['message'] = "ลบผู้ใช้งานออกจากระบบสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการลบข้อมูล!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=users");
            exit();
        }
    }
}
?>