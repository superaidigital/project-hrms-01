<?php
// ==========================================
// ชื่อไฟล์: ManpowerController.php
// ที่อยู่ไฟล์: controllers/ManpowerController.php
// ==========================================

// 🛡️ ฟังก์ชันช่วยเหลือ (Helper) สำหรับป้องกัน XSS (ประกาศไว้ให้ทุก View ใน Controller นี้ใช้งานได้)
if (!function_exists('h')) {
    function h($string) { return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8'); }
}

class ManpowerController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // =========================================================
    // 1. แสดงหน้ารวมกรอบอัตรากำลัง (ตารางรายหน่วยงาน)
    // =========================================================
    public function index() {
        try {
            // ดึงข้อมูลกรอบอัตรากำลังทั้งหมด เรียงตามหน่วยงาน และลำดับการลาก
            $stmt = $this->db->query("SELECT * FROM manpower ORDER BY department ASC, sort_order ASC, position_number ASC");
            $manpowers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // สรุปยอดรวม (ทั้งหมด, ครอง, ว่าง)
            $summaryStmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied,
                    SUM(CASE WHEN status = 'vacant' THEN 1 ELSE 0 END) as vacant
                FROM manpower
            ");
            $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC);

            // ดึงข้อมูล Dropdown สำหรับ Popup เพิ่มตำแหน่ง
            $deptStmt = $this->db->query("SELECT name FROM departments ORDER BY name ASC");
            $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

            $levelStmt = $this->db->query("SELECT name, type FROM position_levels ORDER BY id ASC");
            $position_levels = $levelStmt->fetchAll(PDO::FETCH_ASSOC);

            require_once 'views/manpower/index.php';

        } catch (PDOException $e) {
            $_SESSION['message'] = "เกิดข้อผิดพลาดในการดึงข้อมูล: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            require_once 'views/manpower/index.php';
        }
    }

    // =========================================================
    // 2. แสดงหน้ารายละเอียดกรอบอัตรากำลัง (รายชื่อผู้ครองตำแหน่ง)
    // =========================================================
    public function detail() {
        try {
            // ดึงข้อมูล Dropdown เตรียมไว้สำหรับ Popup แก้ไข
            $deptStmt = $this->db->query("SELECT name FROM departments ORDER BY name ASC");
            $departments = $deptStmt->fetchAll(PDO::FETCH_ASSOC);

            $levelStmt = $this->db->query("SELECT name, type FROM position_levels ORDER BY id ASC");
            $position_levels = $levelStmt->fetchAll(PDO::FETCH_ASSOC);

            // กรณี: ดูแบบทั้งส่วนราชการ (?dept=...)
            if (isset($_GET['dept']) && !empty($_GET['dept'])) {
                $dept_name = trim($_GET['dept']);
                
                // LEFT JOIN ตารางพนักงาน เพื่อดึงรูปและชื่อมาแสดง
                $stmt = $this->db->prepare("
                    SELECT m.*, 
                           e.id AS emp_id, e.prefix AS emp_prefix, 
                           e.first_name AS emp_first_name, e.last_name AS emp_last_name, e.avatar AS emp_avatar
                    FROM manpower m
                    LEFT JOIN employees e ON m.position_number = e.emp_code
                    WHERE m.department = :dept
                    ORDER BY m.sort_order ASC, m.position_number ASC
                ");
                $stmt->execute([':dept' => $dept_name]);
                $dept_manpowers = $stmt->fetchAll(PDO::FETCH_ASSOC);

                require_once 'views/manpower/detail.php';
            } 
            // กรณี: ดูแบบ 1 ตำแหน่ง (?id=...) [Fallback]
            elseif (isset($_GET['id'])) {
                $id = $_GET['id'];
                
                $stmt = $this->db->prepare("SELECT * FROM manpower WHERE id = :id");
                $stmt->execute([':id' => $id]);
                $manpower = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($manpower && $manpower['status'] == 'occupied') {
                    $empStmt = $this->db->prepare("SELECT id, prefix, first_name, last_name, avatar FROM employees WHERE emp_code = :pos LIMIT 1");
                    $empStmt->execute([':pos' => $manpower['position_number']]);
                    $employee = $empStmt->fetch(PDO::FETCH_ASSOC);
                }

                require_once 'views/manpower/detail.php';
            } else {
                header("Location: index.php?action=manpower");
                exit();
            }

        } catch (PDOException $e) {
            $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: index.php?action=manpower");
            exit();
        }
    }

    // =========================================================
    // 3. บันทึกข้อมูลตำแหน่งใหม่
    // =========================================================
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            try {
                // หาค่า sort_order ล่าสุด เพื่อให้ตำแหน่งใหม่ไปต่อท้ายตาราง
                $sortStmt = $this->db->prepare("SELECT MAX(sort_order) as max_sort FROM manpower WHERE department = :dept");
                $sortStmt->execute([':dept' => trim($_POST['department'])]);
                $maxSort = $sortStmt->fetch(PDO::FETCH_ASSOC);
                $new_sort_order = ($maxSort['max_sort'] !== null) ? $maxSort['max_sort'] + 1 : 1;

                $stmt = $this->db->prepare("
                    INSERT INTO manpower 
                    (position_number, position_name, employee_type, level, department, division, status, remark, sort_order) 
                    VALUES (:pos_num, :pos_name, :emp_type, :lvl, :dept, :div, :status, :remark, :sort_order)
                ");

                $stmt->execute([
                    ':pos_num' => trim($_POST['position_number']),
                    ':pos_name' => trim($_POST['position_name']),
                    ':emp_type' => trim($_POST['employee_type']),
                    ':lvl' => trim($_POST['level']),
                    ':dept' => trim($_POST['department']),
                    ':div' => trim($_POST['division'] ?? ''),
                    ':status' => trim($_POST['status']),
                    ':remark' => trim($_POST['remark'] ?? ''),
                    ':sort_order' => $new_sort_order
                ]);

                $_SESSION['message'] = "เพิ่มข้อมูลตำแหน่งใหม่เรียบร้อยแล้ว";
                $_SESSION['message_type'] = "success";

            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { 
                    $_SESSION['message'] = "ไม่สามารถบันทึกได้ เนื่องจากมีเลขที่ตำแหน่งนี้ในระบบแล้ว";
                } else {
                    $_SESSION['message'] = "เกิดข้อผิดพลาด: " . $e->getMessage();
                }
                $_SESSION['message_type'] = "danger";
            }
            
            header("Location: index.php?action=manpower");
            exit();
        }
    }

    // =========================================================
    // 4. บันทึกการแก้ไขข้อมูลตำแหน่ง
    // =========================================================
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $return_url = isset($_POST['return_url']) && !empty($_POST['return_url']) ? $_POST['return_url'] : 'manpower';

            try {
                $stmt = $this->db->prepare("
                    UPDATE manpower 
                    SET position_number = :pos_num, 
                        position_name = :pos_name, 
                        employee_type = :emp_type, 
                        level = :lvl, 
                        department = :dept, 
                        division = :div, 
                        status = :status, 
                        remark = :remark 
                    WHERE id = :id
                ");

                $stmt->execute([
                    ':pos_num' => trim($_POST['position_number']),
                    ':pos_name' => trim($_POST['position_name']),
                    ':emp_type' => trim($_POST['employee_type']),
                    ':lvl' => trim($_POST['level']),
                    ':dept' => trim($_POST['department']),
                    ':div' => trim($_POST['division'] ?? ''),
                    ':status' => trim($_POST['status']),
                    ':remark' => trim($_POST['remark'] ?? ''),
                    ':id' => $id
                ]);

                $_SESSION['message'] = "อัปเดตข้อมูลตำแหน่งเรียบร้อยแล้ว";
                $_SESSION['message_type'] = "success";

            } catch (PDOException $e) {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล: " . $e->getMessage();
                $_SESSION['message_type'] = "danger";
            }
            
            // Redirect กลับไปหน้าเดิม
            header("Location: index.php?action=" . $return_url);
            exit();
        }
    }

    // =========================================================
    // 5. ลบข้อมูลตำแหน่ง
    // =========================================================
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id'];
            $return_url = isset($_POST['return_url']) && !empty($_POST['return_url']) ? $_POST['return_url'] : 'manpower';

            try {
                // ลบได้เฉพาะตำแหน่งที่ "ว่าง" เท่านั้น
                $checkStmt = $this->db->prepare("SELECT status FROM manpower WHERE id = :id");
                $checkStmt->execute([':id' => $id]);
                $mp = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($mp && $mp['status'] == 'vacant') {
                    $stmt = $this->db->prepare("DELETE FROM manpower WHERE id = :id");
                    $stmt->execute([':id' => $id]);

                    $_SESSION['message'] = "ลบข้อมูลตำแหน่งสำเร็จ";
                    $_SESSION['message_type'] = "success";
                    
                    if (strpos($return_url, 'id=') !== false) {
                        $return_url = 'manpower';
                    }
                } else {
                    $_SESSION['message'] = "ไม่อนุญาตให้ลบตำแหน่งที่มีคนครองอยู่ โปรดเปลี่ยนสถานะเป็นว่างก่อน";
                    $_SESSION['message_type'] = "warning";
                }
            } catch (PDOException $e) {
                $_SESSION['message'] = "ไม่สามารถลบข้อมูลได้: " . $e->getMessage();
                $_SESSION['message_type'] = "danger";
            }
            
            header("Location: index.php?action=" . $return_url);
            exit();
        }
    }

    // =========================================================
    // 6. จัดเรียงลำดับ (รับค่าจาก AJAX Drag & Drop)
    // =========================================================
    public function reorder() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $input = json_decode(file_get_contents('php://input'), true);
            if (isset($input['order']) && is_array($input['order'])) {
                try {
                    $this->db->beginTransaction();
                    $stmt = $this->db->prepare("UPDATE manpower SET sort_order = :sort WHERE id = :id");
                    
                    foreach ($input['order'] as $index => $id) {
                        $stmt->execute([':sort' => $index + 1, ':id' => $id]);
                    }
                    $this->db->commit();
                    echo json_encode(['success' => true]);
                } catch (Exception $e) {
                    $this->db->rollBack();
                    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Invalid data format']);
            }
            exit;
        }
    }

    // =========================================================
    // 7. ฟังก์ชันรายงานสรุปและสถิติ (สำหรับหน้า Dashboard ย่อย และพิมพ์รายงาน)
    // =========================================================
    public function summaryReport() {
        try {
            // 7.1 ดึงภาพรวมทั้งหมด (รวม, ครอง, ว่าง) สำหรับกราฟโดนัท
            $stmtTotal = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied,
                    SUM(CASE WHEN status = 'vacant' THEN 1 ELSE 0 END) as vacant
                FROM manpower
            ");
            $overall = $stmtTotal->fetch(PDO::FETCH_ASSOC);

            // 7.2 ดึงสถิติแยกตามส่วนราชการ สำหรับตารางรายงาน
            $stmtDept = $this->db->query("
                SELECT 
                    department,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) as occupied,
                    SUM(CASE WHEN status = 'vacant' THEN 1 ELSE 0 END) as vacant
                FROM manpower
                GROUP BY department
                ORDER BY department ASC
            ");
            $dept_stats = $stmtDept->fetchAll(PDO::FETCH_ASSOC);

            // 7.3 ดึงสถิติแยกตามประเภทบุคลากร สำหรับกราฟแท่ง
            $stmtType = $this->db->query("
                SELECT 
                    IFNULL(NULLIF(employee_type, ''), 'ไม่ระบุประเภท') as emp_type,
                    COUNT(*) as total
                FROM manpower
                GROUP BY employee_type
                ORDER BY total DESC
            ");
            $type_stats = $stmtType->fetchAll(PDO::FETCH_ASSOC);

            // โหลดหน้า View (ถ้าไม่มีไฟล์ให้แจ้งเตือน)
            if (file_exists('views/manpower/summary.php')) {
                require_once 'views/manpower/summary.php';
            } else {
                $_SESSION['message'] = "หน้าต่างรายงานและสถิติกำลังอยู่ระหว่างการพัฒนาครับ";
                $_SESSION['message_type'] = "info";
                header("Location: index.php?action=manpower");
                exit();
            }

        } catch (PDOException $e) {
            $_SESSION['message'] = "เกิดข้อผิดพลาดในการดึงข้อมูลรายงาน: " . $e->getMessage();
            $_SESSION['message_type'] = "danger";
            header("Location: index.php?action=manpower");
            exit();
        }
    }

    // =========================================================
    // 8. ฟังก์ชันดักจับ (Fallback) ป้องกัน Error จากระบบเก่า
    // =========================================================
    public function create() {
        // ระบบใหม่ใช้ Popup ในหน้า Index แล้ว
        header("Location: index.php?action=manpower");
        exit();
    }

    public function edit() {
        // ระบบใหม่ใช้ Popup ในหน้า Detail แล้ว
        header("Location: index.php?action=manpower");
        exit();
    }

    public function searchEmployee() {
        // สำหรับกรณีมีระบบค้นหาผ่าน Select2 AJAX 
        echo json_encode([]);
        exit;
    }

    public function storeAjax() {
        // ดักไว้เผื่อมีการยิง AJAX มาผิด
        echo json_encode(['success' => false, 'message' => 'ระบบใช้การส่งฟอร์มผ่าน Popup แบบปกติแล้ว']);
        exit;
    }
}
?>