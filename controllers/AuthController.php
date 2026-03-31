<?php
class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // ฟังก์ชันแสดงหน้าฟอร์มเข้าสู่ระบบ
    public function loginForm() {
        // หากผู้ใช้ล็อกอินอยู่แล้ว ให้ข้ามหน้าล็อกอินและเด้งไป Dashboard ทันที
        if (isset($_SESSION['user_id'])) {
            header("Location: index.php?action=dashboard");
            exit();
        }
        
        // เรียกไฟล์ View หน้าล็อกอิน
        require_once 'views/auth/login.php';
    }

    // ฟังก์ชันตรวจสอบข้อมูลเมื่อกดปุ่ม "เข้าสู่ระบบ"
    public function loginProcess() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = trim($_POST['username']);
            $password = $_POST['password'];

            // ค้นหาข้อมูลผู้ใช้ในตาราง users
            $query = "SELECT id, username, password, full_name, role, is_active FROM users WHERE username = :username LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            if ($stmt->rowCount() == 1) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // ตรวจสอบว่าบัญชีถูกระงับ (is_active = 0) หรือไม่
                if ($user['is_active'] == '0') {
                    $_SESSION['message'] = "บัญชีนี้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ";
                    $_SESSION['message_type'] = "danger";
                    header("Location: index.php?action=login");
                    exit();
                }

                // ตรวจสอบรหัสผ่านที่เข้ารหัสไว้
                if (password_verify($password, $user['password'])) {
                    // สร้าง Session หลังล็อกอินสำเร็จ
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];

                    $_SESSION['message'] = "ยินดีต้อนรับเข้าสู่ระบบ";
                    $_SESSION['message_type'] = "success";
                    
                    header("Location: index.php?action=dashboard");
                    exit();
                } else {
                    $_SESSION['message'] = "รหัสผ่านไม่ถูกต้อง";
                    $_SESSION['message_type'] = "danger";
                    header("Location: index.php?action=login");
                    exit();
                }
            } else {
                $_SESSION['message'] = "ไม่พบชื่อผู้ใช้งานนี้ในระบบ";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=login");
                exit();
            }
        }
    }

    // ฟังก์ชันออกจากระบบ
    public function logout() {
        session_destroy();
        session_start();
        $_SESSION['message'] = "ออกจากระบบเรียบร้อยแล้ว";
        $_SESSION['message_type'] = "success";
        header("Location: index.php?action=login");
        exit();
    }
}
?>