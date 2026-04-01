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
            
            // 🛡️ [ป้องกัน Brute Force]: ตรวจสอบการล็อกอินผิดพลาดเกินกำหนด
            if (!isset($_SESSION['login_attempts'])) {
                $_SESSION['login_attempts'] = 0;
            }
            
            // หากล็อกอินผิดเกิน 5 ครั้ง ให้รอ 5 นาที (300 วินาที)
            if ($_SESSION['login_attempts'] >= 5) {
                if (time() - $_SESSION['last_login_time'] < 300) {
                    $_SESSION['message'] = "คุณกรอกรหัสผิดเกินกำหนด กรุณารอ 5 นาทีก่อนลองใหม่";
                    $_SESSION['message_type'] = "danger";
                    header("Location: index.php?action=login");
                    exit();
                } else {
                    // รีเซ็ตการนับเมื่อพ้น 5 นาที
                    $_SESSION['login_attempts'] = 0;
                }
            }

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
                    
                    // 🛡️ [ป้องกัน Session Fixation]: สร้าง Session ID ใหม่ทันทีเมื่อล็อกอินผ่าน
                    session_regenerate_id(true);

                    // สร้าง Session หลังล็อกอินสำเร็จ
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    
                    // รีเซ็ตจำนวนครั้งที่ล็อกอินผิด
                    unset($_SESSION['login_attempts']);
                    unset($_SESSION['last_login_time']);

                    $_SESSION['message'] = "ยินดีต้อนรับเข้าสู่ระบบ";
                    $_SESSION['message_type'] = "success";
                    
                    header("Location: index.php?action=dashboard");
                    exit();
                } else {
                    // รหัสผ่านผิด เพิ่มจำนวนครั้ง
                    $_SESSION['login_attempts']++;
                    $_SESSION['last_login_time'] = time();
                    
                    $_SESSION['message'] = "รหัสผ่านไม่ถูกต้อง (ครั้งที่ " . $_SESSION['login_attempts'] . "/5)";
                    $_SESSION['message_type'] = "danger";
                    header("Location: index.php?action=login");
                    exit();
                }
            } else {
                // ไม่พบผู้ใช้ ก็เพิ่มจำนวนครั้งเช่นกัน ป้องกันการสุ่ม Username
                $_SESSION['login_attempts']++;
                $_SESSION['last_login_time'] = time();
                
                $_SESSION['message'] = "ไม่พบชื่อผู้ใช้งานนี้ในระบบ";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=login");
                exit();
            }
        }
    }

    // ฟังก์ชันออกจากระบบ
    public function logout() {
        // ล้างข้อมูล Session ทั้งหมด
        $_SESSION = array();
        session_destroy();
        
        // เริ่ม Session ใหม่สำหรับแสดงข้อความแจ้งเตือน
        session_start();
        $_SESSION['message'] = "ออกจากระบบเรียบร้อยแล้ว";
        $_SESSION['message_type'] = "success";
        header("Location: index.php?action=login");
        exit();
    }
}
?>