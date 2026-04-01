<?php
// ==========================================
// ชื่อไฟล์: index.php
// ที่อยู่ไฟล์: /index.php (Root Directory)
// ==========================================

// 1. เปิดโชว์ Error (ช่วยป้องกันหน้าจอขาว จะได้รู้ว่าพังที่ไหน) **ลบออกได้เมื่อระบบเสร็จสมบูรณ์**
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 2. เริ่มต้น Session สำหรับเก็บข้อมูลผู้ใช้งานและข้อความแจ้งเตือนต่างๆ
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. ดึงไฟล์ตั้งค่าฐานข้อมูล
require_once 'config/database.php';

// 4. สร้างออบเจกต์ฐานข้อมูล
$database = new Database();
$db = $database->getConnection();

// 🚨 [แก้ไขบั๊กที่ 1]: ป้องกัน Fatal Error หน้าจอขาว หากเชื่อมต่อฐานข้อมูลไม่สำเร็จ
if ($db === null) {
    die("<div style='text-align:center; margin-top:100px; font-family: sans-serif;'>
            <h1 style='color:#e74c3c;'>เกิดข้อผิดพลาด! ไม่สามารถเชื่อมต่อฐานข้อมูลได้</h1>
            <p>กรุณาตรวจสอบการตั้งค่าในไฟล์ <code>config/database.php</code> หรือตรวจสอบสถานะเซิร์ฟเวอร์ Database (MySQL)</p>
         </div>");
}

// 5. รับค่า action จาก URL 
$action = isset($_GET['action']) ? $_GET['action'] : '';

// ==========================================
// ลำดับที่ 1: ตรวจสอบการล็อกอินก่อนเป็นอันดับแรก!
// ==========================================
// ถ้ายังไม่ได้ล็อกอิน และไม่ได้กำลังพยายามล็อกอิน ให้เด้งไปหน้า login ทันที
if ($action != 'login' && !isset($_SESSION['user_id'])) {
    header("Location: index.php?action=login");
    exit();
}

// ตั้งค่าหน้าเริ่มต้น หากล็อกอินแล้วแต่ไม่ได้ระบุ action
if ($action == '' && isset($_SESSION['user_id'])) {
    $action = 'dashboard';
}

// ==========================================
// ลำดับที่ 2: ตรวจสอบสถานะ Maintenance Mode (เฉพาะคนที่ล็อกอินแล้ว)
// ==========================================
if ($action != 'login' && $action != 'logout') {
    try {
        $stmtMode = $db->prepare("SELECT setting_value FROM system_settings WHERE setting_key = 'maintenance_mode'");
        $stmtMode->execute();
        $modeResult = $stmtMode->fetch(PDO::FETCH_ASSOC);
        $maintenance_mode = $modeResult ? $modeResult['setting_value'] : 'off';

        // ถ้าเปิด Maintenance Mode และไม่ใช่ Admin ให้บล็อกการเข้าใช้งาน
        if ($maintenance_mode == 'on' && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin')) {
            die("<div style='text-align:center; margin-top:100px; font-family: sans-serif;'>
                    <h1 style='color:#e74c3c;'>ขออภัย ระบบกำลังปิดปรับปรุง</h1>
                    <p>เรากำลังทำการอัปเดตระบบ กรุณากลับมาใช้งานใหม่ในภายหลัง</p>
                    <a href='index.php?action=logout' style='padding:10px 20px; background:#3498db; color:#fff; text-decoration:none; border-radius:5px;'>ออกจากระบบ</a>
                 </div>");
        }
    } catch (PDOException $e) {
        // กรณีที่ตาราง system_settings ยังไม่ถูกสร้าง ให้ระบบข้ามไปก่อน (ป้องกันจอขาว)
        $maintenance_mode = 'off';
    }
}

// ==========================================
// ส่วนกำหนดเส้นทาง (Routing) ของระบบ
// ==========================================
switch ($action) {
    // ---- การยืนยันตัวตน (Authentication) ----
    case 'login':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController($db);
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $auth->loginProcess();
        } else {
            $auth->loginForm();
        }
        break;

    case 'logout':
        require_once 'controllers/AuthController.php';
        $auth = new AuthController($db);
        $auth->logout();
        break;

    // ---- หน้าหลัก (Dashboard) ----
    case 'dashboard':
        require_once 'controllers/DashboardController.php';
        $dashboard = new DashboardController($db);
        $dashboard->index();
        break;

    // ---- จัดการบุคลากร (Employees) ----
    case 'employees':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        $emp->index();
        break;
        
    // 🌟 Route สำหรับรับค่า AJAX อัปเดตสถานะการทำงาน
    case 'employee_update_status':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        $emp->updateStatusAjax();
        break;

    case 'create':
    case 'employee_create':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        $emp->create();
        break;

    case 'store':
    case 'employee_store':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        $emp->store();
        break;

    case 'show':
    case 'employee_detail':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        if (method_exists($emp, 'show')) { $emp->show(); } 
        elseif (method_exists($emp, 'detail')) { $emp->detail(); }
        break;

    case 'edit':
    case 'employee_edit':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        $emp->edit();
        break;

    case 'update':
    case 'employee_update':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        $emp->update();
        break;

    case 'delete':
    case 'employee_delete':
        require_once 'controllers/EmployeeController.php';
        $emp = new EmployeeController($db);
        $emp->delete();
        break;

    // ---- จัดการกรอบอัตรากำลัง (Manpower) ----
    case 'manpower':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->index();
        break;

    case 'manpower_detail':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->detail();
        break;

    case 'manpower_create':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->create();
        break;

    case 'manpower_store':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->store();
        break;

    case 'manpower_edit':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->edit();
        break;

    case 'manpower_update':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->update();
        break;

    case 'manpower_delete':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->delete();
        break;

    // 🌟 เพิ่มใหม่: Route สำหรับรับค่า AJAX สลับลำดับข้อมูล (Drag & Drop)
    case 'manpower_reorder':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->reorder();
        break;

    case 'search_employee':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->searchEmployee();
        break;

    case 'manpower_store_ajax':
        require_once 'controllers/ManpowerController.php';
        $manpower = new ManpowerController($db);
        $manpower->storeAjax();
        break;
        
    case 'summary_report':
    case 'manpower_summary':
        require_once 'controllers/ManpowerController.php';
        $controller = new ManpowerController($db); 
        $controller->summaryReport();
        break;

    // ---- ตั้งค่า: หน่วยงาน (Departments) ----
    case 'departments':
        require_once 'controllers/DepartmentController.php';
        $dept = new DepartmentController($db);
        $dept->index();
        break;

    case 'department_create':
        require_once 'controllers/DepartmentController.php';
        $dept = new DepartmentController($db);
        $dept->create();
        break;

    case 'department_store':
        require_once 'controllers/DepartmentController.php';
        $dept = new DepartmentController($db);
        $dept->store();
        break;

    case 'department_edit':
        require_once 'controllers/DepartmentController.php';
        $dept = new DepartmentController($db);
        $dept->edit();
        break;

    case 'department_update':
        require_once 'controllers/DepartmentController.php';
        $dept = new DepartmentController($db);
        $dept->update();
        break;

    case 'department_delete':
        require_once 'controllers/DepartmentController.php';
        $dept = new DepartmentController($db);
        $dept->delete();
        break;

    // ---- ตั้งค่า: ระดับตำแหน่ง (Position Levels) ----
    case 'position_levels':
        require_once 'controllers/PositionLevelController.php';
        $level = new PositionLevelController($db);
        $level->index();
        break;

    // ---- จัดการผู้ใช้งาน (Users) ----
    case 'users':
        require_once 'controllers/UserController.php';
        $user = new UserController($db);
        $user->index();
        break;

    case 'user_store':
        require_once 'controllers/UserController.php';
        $user = new UserController($db);
        $user->store();
        break;

    case 'user_update':
        require_once 'controllers/UserController.php';
        $user = new UserController($db);
        $user->update();
        break;

    case 'user_reset_pwd':
        require_once 'controllers/UserController.php';
        $user = new UserController($db);
        $user->resetPassword();
        break;

    case 'user_toggle':
        require_once 'controllers/UserController.php';
        $user = new UserController($db);
        $user->toggleActive();
        break;

    // ---- จัดการเมนู (Menus) ----
    case 'menus':
        require_once 'controllers/MenuController.php';
        $menu = new MenuController($db);
        $menu->index();
        break;

    case 'menu_toggle':
        require_once 'controllers/MenuController.php';
        $menu = new MenuController($db);
        $menu->toggleActive();
        break;

    // ---- ตั้งค่าระบบหลัก (Settings) ----
    case 'settings':
        require_once 'controllers/SettingsController.php';
        $setting = new SettingsController($db);
        $setting->index();
        break;

    // ---- พิมพ์รายงาน (Reports) ----
    case 'print_kp7':
        require_once 'controllers/ReportController.php';
        $report = new ReportController($db);
        $report->printKP7();
        break;

    default:
        // หากไม่มี action ใดตรงกับข้างบนเลย ให้ไปที่หน้าสรุปภาพรวม หรือ Dashboard
        require_once 'controllers/DashboardController.php';
        $dashboard = new DashboardController($db);
        $dashboard->index();
        break;
}
?>