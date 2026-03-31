<?php
// ==========================================
// ชื่อไฟล์: ManpowerController.php
// ที่อยู่ไฟล์: controllers/ManpowerController.php
// ==========================================

require_once 'models/Manpower.php';
require_once 'models/Department.php';
require_once 'models/PositionLevel.php';

class ManpowerController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function index() {
        $manpowerModel = new Manpower($this->db);
        $stmt = $manpowerModel->readAll();
        $manpowers = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $totalRequired = count($manpowers);
        $totalOccupied = 0;
        $totalVacant = 0;
        $categories = [];

        foreach ($manpowers as $row) {
            if ($row['status'] == 'occupied') $totalOccupied++;
            else $totalVacant++;

            $deptName = $row['department'];
            if (!isset($categories[$deptName])) {
                $categories[$deptName] = ['total' => 0, 'occupied' => 0, 'vacant' => 0, 'items' => []];
            }
            
            $categories[$deptName]['total']++;
            $categories[$deptName]['items'][] = $row;

            if ($row['status'] == 'occupied') $categories[$deptName]['occupied']++;
            else $categories[$deptName]['vacant']++;
        }

        require_once 'views/manpower/index.php';
    }

    public function detail() {
        if(isset($_GET['dept'])) {
            $dept = $_GET['dept'];
            $manpowerModel = new Manpower($this->db);
            $stmt = $manpowerModel->readByDepartment($dept);
            $deptManpowers = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $deptTotal = count($deptManpowers);
            $deptOccupied = 0;
            $deptVacant = 0;

            foreach ($deptManpowers as $m) {
                if ($m['status'] == 'occupied') $deptOccupied++;
                else $deptVacant++;
            }

            require_once 'views/manpower/detail.php';
        } else {
            header("Location: index.php?action=manpower");
            exit();
        }
    }

    public function create() {
        $deptModel = new Department($this->db);
        $departments = $deptModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        $levelModel = new PositionLevel($this->db);
        $position_levels = $levelModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
        require_once 'views/manpower/create.php';
    }

    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $manpowerModel = new Manpower($this->db);
            $status = trim($_POST['status'] ?? 'vacant');
            $position_number = trim($_POST['position_number'] ?? '');

            if ($manpowerModel->isPositionNumberExists($position_number)) {
                $_SESSION['message'] = "เกิดข้อผิดพลาด! เลขที่ตำแหน่ง '{$position_number}' มีอยู่ในระบบแล้ว กรุณาใช้เลขอื่น";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=manpower_create");
                exit();
            }

            if ($status == 'occupied' && empty($_POST['assigned_employee_id'])) {
                $_SESSION['message'] = "กรุณาเลือกบุคลากรที่จะครองตำแหน่งนี้!";
                $_SESSION['message_type'] = "warning";
                header("Location: index.php?action=manpower_create");
                exit();
            }

            $data = [
                'department' => trim($_POST['department'] ?? ''),
                'division' => trim($_POST['division'] ?? '-'),
                'employee_type' => trim($_POST['employee_type'] ?? ''),
                'position_number' => $position_number,
                'position_name' => trim($_POST['position_name'] ?? ''),
                'level' => trim($_POST['level'] ?? ''),
                'status' => $status,
                'remark' => trim($_POST['remark'] ?? '')
            ];

            if($manpowerModel->create($data)) {
                if ($status == 'occupied' && !empty($_POST['assigned_employee_id'])) {
                    $emp_id = $_POST['assigned_employee_id'];
                    
                    if(!empty($position_number)) {
                        $stmtCheckOldPos = $this->db->prepare("SELECT emp_code FROM employees WHERE id = ?");
                        $stmtCheckOldPos->execute([$emp_id]);
                        $empData = $stmtCheckOldPos->fetch(PDO::FETCH_ASSOC);
                        
                        if ($empData && !empty($empData['emp_code']) && $empData['emp_code'] !== $position_number) {
                            $stmtFreeOld = $this->db->prepare("UPDATE manpower SET status = 'vacant' WHERE position_number = ?");
                            $stmtFreeOld->execute([$empData['emp_code']]);
                        }

                        $stmtAssign = $this->db->prepare("UPDATE employees SET emp_code = ? WHERE id = ?");
                        $stmtAssign->execute([$position_number, $emp_id]);
                    }
                }

                $_SESSION['message'] = "เพิ่มข้อมูลตำแหน่งในกรอบอัตรากำลังสำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=manpower_detail&dept=" . urlencode($data['department']));
                exit();
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาด! ไม่สามารถบันทึกข้อมูลได้";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=manpower_create");
                exit();
            }
        }
    }

    public function edit() {
        if(isset($_GET['id']) && !empty($_GET['id'])) {
            $manpowerModel = new Manpower($this->db);
            $manpower = $manpowerModel->readOne($_GET['id']);
            
            if(!$manpower) {
                $_SESSION['message'] = "ไม่พบข้อมูลตำแหน่งที่คุณต้องการแก้ไข";
                $_SESSION['message_type'] = "warning";
                header("Location: index.php?action=manpower");
                exit();
            }

            $deptModel = new Department($this->db);
            $departments = $deptModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
            $levelModel = new PositionLevel($this->db);
            $position_levels = $levelModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

            $occupant = null;
            if ($manpower['status'] == 'occupied' && !empty($manpower['position_number'])) {
                $pos_num = $manpower['position_number'];
                $stmt = $this->db->prepare("SELECT id, national_id, prefix, first_name, last_name, avatar FROM employees WHERE emp_code = ? LIMIT 1");
                $stmt->execute([$pos_num]);
                $occupant = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            require_once 'views/manpower/edit.php';
        } else {
            header("Location: index.php?action=manpower");
            exit();
        }
    }

    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $manpowerModel = new Manpower($this->db);
            $id = $_POST['id'];
            
            $oldManpower = $manpowerModel->readOne($id);
            if(!$oldManpower) {
                header("Location: index.php?action=manpower");
                exit();
            }

            $old_status = $oldManpower['status'];
            $new_status = trim($_POST['status'] ?? 'vacant');
            $old_position_number = $oldManpower['position_number'];
            $new_position_number = trim($_POST['position_number'] ?? '');

            if ($manpowerModel->isPositionNumberExists($new_position_number, $id)) {
                $_SESSION['message'] = "เกิดข้อผิดพลาด! เลขที่ตำแหน่ง '{$new_position_number}' มีอยู่ในระบบแล้ว กรุณาใช้เลขอื่น";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=manpower_edit&id=" . $id);
                exit();
            }

            if ($new_status == 'occupied' && empty($_POST['assigned_employee_id']) && $old_status == 'vacant') {
                $_SESSION['message'] = "กรุณาเลือกบุคลากรที่จะครองตำแหน่งนี้!";
                $_SESSION['message_type'] = "warning";
                header("Location: index.php?action=manpower_edit&id=" . $id);
                exit();
            }

            $data = [
                'department' => trim($_POST['department'] ?? ''),
                'division' => trim($_POST['division'] ?? '-'),
                'employee_type' => trim($_POST['employee_type'] ?? ''),
                'position_number' => $new_position_number,
                'position_name' => trim($_POST['position_name'] ?? ''),
                'level' => trim($_POST['level'] ?? ''),
                'status' => $new_status,
                'remark' => trim($_POST['remark'] ?? '')
            ];

            if($manpowerModel->update($id, $data)) {
                
                $target_emp_id = null;
                if ($new_status == 'occupied') {
                    if (!empty($_POST['assigned_employee_id'])) {
                        $target_emp_id = $_POST['assigned_employee_id'];
                    } else if ($old_status == 'occupied' && !empty($old_position_number)) {
                        $stmtGetOcc = $this->db->prepare("SELECT id FROM employees WHERE emp_code = ? LIMIT 1");
                        $stmtGetOcc->execute([$old_position_number]);
                        if ($row = $stmtGetOcc->fetch(PDO::FETCH_ASSOC)) {
                            $target_emp_id = $row['id'];
                        }
                    }
                }

                if (!empty($old_position_number)) {
                    $stmtClear = $this->db->prepare("UPDATE employees SET emp_code = NULL WHERE emp_code = ?");
                    $stmtClear->execute([$old_position_number]);
                }

                if (!empty($new_position_number) && $old_position_number !== $new_position_number) {
                    $stmtClearNew = $this->db->prepare("UPDATE employees SET emp_code = NULL WHERE emp_code = ?");
                    $stmtClearNew->execute([$new_position_number]);
                }

                if ($new_status == 'occupied' && $target_emp_id && !empty($new_position_number)) {
                    $stmtCheckOldPos = $this->db->prepare("SELECT emp_code FROM employees WHERE id = ?");
                    $stmtCheckOldPos->execute([$target_emp_id]);
                    $empData = $stmtCheckOldPos->fetch(PDO::FETCH_ASSOC);
                    
                    if ($empData && !empty($empData['emp_code']) && $empData['emp_code'] !== $new_position_number) {
                        $stmtFreeOld = $this->db->prepare("UPDATE manpower SET status = 'vacant' WHERE position_number = ?");
                        $stmtFreeOld->execute([$empData['emp_code']]);
                    }

                    $stmtAssign = $this->db->prepare("UPDATE employees SET emp_code = ? WHERE id = ?");
                    $stmtAssign->execute([$new_position_number, $target_emp_id]);
                }

                $_SESSION['message'] = "บันทึกข้อมูลและอัปเดตสถานะสำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=manpower_detail&dept=" . urlencode($data['department']));
                exit();
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=manpower_edit&id=" . $id);
                exit();
            }
        }
    }

    public function delete() {
        if(isset($_GET['id'])) {
            $manpowerModel = new Manpower($this->db);
            $id = $_GET['id'];
            $manpower = $manpowerModel->readOne($id);
            if (!$manpower) {
                header("Location: index.php?action=manpower");
                exit();
            }

            $dept = $manpower['department'];
            if($manpower['status'] == 'occupied') {
                $_SESSION['message'] = "ไม่สามารถลบได้! เนื่องจากมีบุคลากรครองตำแหน่งนี้อยู่";
                $_SESSION['message_type'] = "warning";
                header("Location: index.php?action=manpower_detail&dept=" . urlencode($dept));
                exit();
            }

            if($manpowerModel->delete($id)) {
                $_SESSION['message'] = "ลบตำแหน่งออกจากกรอบอัตรากำลังสำเร็จ!";
                $_SESSION['message_type'] = "success";
            }
            header("Location: index.php?action=manpower_detail&dept=" . urlencode($dept));
            exit();
        }
    }

    public function searchEmployee() {
        if (ob_get_length()) { ob_clean(); }

        if(isset($_GET['q'])) {
            $query = "%" . trim($_GET['q']) . "%";
            $stmt = $this->db->prepare("SELECT id, national_id, prefix, first_name, last_name, emp_code FROM employees WHERE first_name LIKE ? OR last_name LIKE ? OR national_id LIKE ? LIMIT 20");
            $stmt->execute([$query, $query, $query]);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($results, JSON_UNESCAPED_UNICODE);
            exit();
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([]);
            exit();
        }
    }

    public function storeAjax() {
        if (ob_get_length()) { ob_clean(); }
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $manpowerModel = new Manpower($this->db);
            $position_number = trim($_POST['position_number'] ?? '');

            if ($manpowerModel->isPositionNumberExists($position_number)) {
                echo json_encode(['status' => 'error', 'message' => "เลขที่ตำแหน่ง '{$position_number}' มีอยู่ในระบบแล้ว"]);
                exit();
            }

            $data = [
                'department' => trim($_POST['department'] ?? ''),
                'division' => trim($_POST['division'] ?? '-'),
                'employee_type' => trim($_POST['employee_type'] ?? ''),
                'position_number' => $position_number,
                'position_name' => trim($_POST['position_name'] ?? ''),
                'level' => trim($_POST['level'] ?? ''),
                'status' => 'vacant', 
                'remark' => 'เพิ่มผ่านหน้าเพิ่มประวัติบุคลากร'
            ];

            if($manpowerModel->create($data)) {
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'เพิ่มตำแหน่งใหม่เรียบร้อยแล้ว', 
                    'data' => [
                        'position_number' => $data['position_number'],
                        'position_name' => $data['position_name'],
                        'employee_type' => $data['employee_type']
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดในการบันทึกฐานข้อมูล']);
            }
        }
        exit();
    }

    // =========================================================
    // 🌟 รายงานสรุปอัตรากำลังแยกประเภท (ระบบแยกตามโครงสร้างที่ต้องการ)
    // 1. ส่วนกลาง (เช่น กองยุทธศาสตร์และงบประมาณ)
    // 2. สังกัดโรงเรียน (เช่น โรงเรียนราษีไศล)
    // 3. รพ.สต. แยกเป็นอำเภอ (เช่น อำเภอเมือง -> รพ.สต.หนองไผ่)
    // =========================================================
    public function summaryReport() {
        // รับค่าปีงบประมาณ
        $year = isset($_GET['year']) ? $_GET['year'] : (date('Y') + 543);

        // ดึงข้อมูลจัดกลุ่มตามชื่อหน่วยงาน (department) จากตาราง manpower
        $sql = "
            SELECT 
                department AS department_name,
                CASE 
                    WHEN department LIKE '%โรงเรียน%' OR department LIKE '%วิทยาลัย%' THEN 'school'
                    WHEN department LIKE '%รพ.สต.%' OR department LIKE '%โรงพยาบาล%' OR department LIKE '%อนามัย%' OR department LIKE '%สาธารณสุข%' THEN 'health'
                    ELSE 'central'
                END AS dept_type,
                COUNT(id) AS framework_count,
                SUM(CASE WHEN status = 'occupied' THEN 1 ELSE 0 END) AS actual_count
            FROM manpower
            GROUP BY department
            ORDER BY department ASC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // อาเรย์สำหรับจัดเก็บข้อมูลแต่ละหมวดหมู่
        $central = []; 
        $school = []; 
        $health = [];
        $health_grouped = []; // อาเรย์สำหรับเก็บ รพ.สต. ที่แยกตามอำเภอแล้ว
        $total_fw = 0; 
        $total_actual = 0;

        foreach($data as $row) {
            $total_fw += $row['framework_count'];
            $total_actual += $row['actual_count'];

            if($row['dept_type'] == 'school') {
                // เก็บเข้ากลุ่ม โรงเรียน
                $school[] = $row;
            } elseif($row['dept_type'] == 'health') {
                // เก็บเข้ากลุ่ม รพ.สต. ก่อนเพื่อไว้ทำผลรวม
                $health[] = $row; 

                // โลจิกดึงชื่อ "อำเภอ" ออกมาจากชื่อของ รพ.สต.
                $amphoe = 'อำเภออื่นๆ';
                if (preg_match('/(อำเภอ|อ\.)\s*([ก-๙a-zA-Z0-9]+)/u', $row['department_name'], $matches)) {
                    $name = trim($matches[2]);
                    if($name == 'เมืองศรีสะเกษ' || $name == 'เมือง') {
                        $name = 'เมืองศรีสะเกษ'; 
                    }
                    $amphoe = 'อำเภอ' . $name;
                }

                // นำไปใส่ในกลุ่มอำเภอนั้นๆ
                if(!isset($health_grouped[$amphoe])) {
                    $health_grouped[$amphoe] = [];
                }
                $health_grouped[$amphoe][] = $row;

            } else {
                // นอกเหนือจาก โรงเรียน และ รพ.สต. ให้เข้ากลุ่ม "ส่วนราชการส่วนกลาง" ทั้งหมด
                // เช่น กองยุทธศาสตร์และงบประมาณ, สำนักปลัด, กองช่าง
                $central[] = $row;
            }
        }

        // เรียงลำดับชื่ออำเภอตามตัวอักษร ก-ฮ
        ksort($health_grouped);

        $current_controller = 'manpower';
        $current_action = 'summaryReport';

        // เรียกใช้งาน View
        require_once 'views/layout/header.php';
        require_once 'views/manpower/summary_report.php';
        require_once 'views/layout/footer.php';
    }
}
?>