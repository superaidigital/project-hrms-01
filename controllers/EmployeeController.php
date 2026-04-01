<?php
// ==========================================
// ชื่อไฟล์: EmployeeController.php
// ที่อยู่ไฟล์: controllers/EmployeeController.php
// ==========================================

require_once 'models/Employee.php';
require_once 'models/Department.php';
require_once 'models/PositionLevel.php';
require_once 'models/Manpower.php'; 

class EmployeeController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // 🚨 [แก้ไขบั๊กที่ 4]: เพิ่มความปลอดภัยในการอัปโหลดรูปโปรไฟล์ (ตรวจ MIME Type)
    private function uploadAvatar($file) {
        if ($file['error'] == 0) {
            $target_dir = "uploads/";
            if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
            
            $file_extension = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            // 1. เช็คนามสกุลไฟล์แบบเข้มงวด
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($file_extension, $allowed_extensions)) {
                return null; // นามสกุลไม่อนุญาต
            }

            // 2. เช็ค MIME Type จากไฟล์จริง (ไม่ใช่แค่ชื่อนามสกุล)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime_type = finfo_file($finfo, $file["tmp_name"]);
            finfo_close($finfo);
            $allowed_mimes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($mime_type, $allowed_mimes)) {
                return null; // เนื้อหาไฟล์ไม่ใช่รูปภาพจริง
            }

            $new_filename = uniqid() . '.' . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($file["tmp_name"], $target_file)) {
                return $new_filename;
            }
        }
        return null;
    }

    // 1. หน้าแสดงรายการบุคลากรทั้งหมด (รองรับการค้นหาและแบ่งหน้า)
    public function index() {
        $employeeModel = new Employee($this->db);
        $records_per_page = 10;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if($page < 1) $page = 1;
        $from_record_num = ($records_per_page * $page) - $records_per_page;
        
        $search_term = isset($_GET['search']) ? trim($_GET['search']) : "";
        $dept_type = isset($_GET['dept_type']) ? trim($_GET['dept_type']) : "";
        
        // ดึงประเภทส่วนราชการมาแสดงใน Dropdown
        $stmt_types = $this->db->prepare("SELECT DISTINCT type FROM departments WHERE type IS NOT NULL AND type != ''");
        $stmt_types->execute();
        $department_types = $stmt_types->fetchAll(PDO::FETCH_ASSOC);

        $query = "SELECT e.*, w.position_name AS position, w.department, w.level, m.employee_type, d.type as department_type 
                  FROM employees e
                  LEFT JOIN emp_work_history w ON w.id = (
                      SELECT id FROM emp_work_history WHERE employee_id = e.id ORDER BY id DESC LIMIT 1
                  )
                  LEFT JOIN manpower m ON m.position_number COLLATE utf8mb4_unicode_ci = e.emp_code COLLATE utf8mb4_unicode_ci
                  LEFT JOIN departments d ON w.department = d.name";
                  
        $where_clauses = [];
        $params = [];
        
        if(!empty($search_term)) {
            $where_clauses[] = "(e.first_name LIKE ? OR e.last_name LIKE ? OR e.emp_code LIKE ? OR w.position_name LIKE ?)";
            $search_param = "%{$search_term}%";
            array_push($params, $search_param, $search_param, $search_param, $search_param);
        }
        
        if(!empty($dept_type)) {
            $where_clauses[] = "d.type = ?";
            $params[] = $dept_type;
        }

        if(!empty($where_clauses)) {
            $query .= " WHERE " . implode(" AND ", $where_clauses);
        }
        
        $query .= " ORDER BY e.id DESC LIMIT ?, ?";
        
        $stmt = $this->db->prepare($query);
        
        $param_index = 1;
        foreach($params as $param) {
            $stmt->bindValue($param_index++, $param);
        }
        $stmt->bindValue($param_index++, $from_record_num, PDO::PARAM_INT);
        $stmt->bindValue($param_index, $records_per_page, PDO::PARAM_INT);
        
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $total_rows = $employeeModel->countEmployees($search_term, $dept_type);
        $total_pages = ceil($total_rows / $records_per_page);

        require_once 'views/employee/index.php';
    }

    // 2. หน้าฟอร์มเพิ่มบุคลากร
    public function create() {
        $deptModel = new Department($this->db);
        $departments = $deptModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

        $levelModel = new PositionLevel($this->db);
        $position_levels = $levelModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

        $manpowerModel = new Manpower($this->db);
        $manpowers = $manpowerModel->readAvailablePositions()->fetchAll(PDO::FETCH_ASSOC);

        require_once 'views/employee/create.php';
    }

    // 3. บันทึกบุคลากรใหม่
    public function store() {
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $employeeModel = new Employee($this->db);
            $manpowerModel = new Manpower($this->db); 

            $national_id = trim($_POST['national_id'] ?? '');

            // เช็คเลขบัตรประชาชนซ้ำ
            if (!empty($national_id) && $employeeModel->isNationalIdExists($national_id)) {
                $_SESSION['message'] = "เกิดข้อผิดพลาด! เลขประจำตัวประชาชน '{$national_id}' มีประวัติอยู่ในระบบแล้ว";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=create");
                exit();
            }

            $avatar_filename = null;
            if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $avatar_filename = $this->uploadAvatar($_FILES['avatar']);
            }

            $selected_position = trim($_POST['emp_code'] ?? '');

            $data = [
                'emp_code' => $selected_position,
                'national_id' => $national_id,
                'prefix' => $_POST['prefix'] ?? '',
                'gender' => $_POST['gender'] ?? '',
                'dob' => trim($_POST['dob'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'avatar' => $avatar_filename,
                'status' => 'ปฏิบัติงาน' // ค่าเริ่มต้น
            ];

            $new_emp_id = $employeeModel->create($data);

            if($new_emp_id) {
                $this->saveRelations($employeeModel, $new_emp_id, $_POST);
                // จองกรอบอัตรากำลัง
                if(!empty($selected_position)) {
                    $manpowerModel->updateStatusByPositionNumber($selected_position, 'occupied');
                }
                $_SESSION['message'] = "เพิ่มบุคลากรและจองตำแหน่งสำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=employees");
                exit();
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการบันทึกข้อมูล!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=create");
                exit();
            }
        }
    }

    // 4. แสดงรายละเอียดประวัติบุคลากร (ก.พ. 7)
    public function show() {
        if(isset($_GET['id'])) {
            $employeeModel = new Employee($this->db);
            $empData = $employeeModel->readOne($_GET['id']);
            
            $empType = '- ไม่ระบุ -';
            if(!empty($empData['emp_code'])) {
                $stmt = $this->db->prepare("SELECT employee_type FROM manpower WHERE position_number = ?");
                $stmt->execute([$empData['emp_code']]);
                $mp = $stmt->fetch(PDO::FETCH_ASSOC);
                if($mp) $empType = $mp['employee_type'];
            }
            $empData['employee_type'] = $empType;

            if($empData) require_once 'views/employee/show.php';
            else { header("Location: index.php?action=employees"); exit(); }
        } else { header("Location: index.php?action=employees"); exit(); }
    }

    // 5. หน้าฟอร์มแก้ไขข้อมูลบุคลากร
    public function edit() {
        if(isset($_GET['id'])) {
            $employeeModel = new Employee($this->db);
            $empData = $employeeModel->readOne($_GET['id']);
            
            $deptModel = new Department($this->db);
            $departments = $deptModel->readAll()->fetchAll(PDO::FETCH_ASSOC);

            $levelModel = new PositionLevel($this->db);
            $position_levels = $levelModel->readAll()->fetchAll(PDO::FETCH_ASSOC);
            
            $manpowerModel = new Manpower($this->db);
            $manpowers = $manpowerModel->readAvailablePositions($empData['emp_code'])->fetchAll(PDO::FETCH_ASSOC);

            if($empData) require_once 'views/employee/edit.php';
            else { header("Location: index.php?action=employees"); exit(); }
        } else { header("Location: index.php?action=employees"); exit(); }
    }

    // 6. บันทึกการแก้ไขข้อมูล
    public function update() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $employeeModel = new Employee($this->db);
            $manpowerModel = new Manpower($this->db);
            $id = $_POST['id'];
            $national_id = trim($_POST['national_id'] ?? '');

            // เช็คเลขบัตรประชาชนซ้ำ (ยกเว้นของตัวเอง)
            if (!empty($national_id) && $employeeModel->isNationalIdExists($national_id, $id)) {
                $_SESSION['message'] = "เกิดข้อผิดพลาด! เลขประจำตัวประชาชน '{$national_id}' ซ้ำกับบุคลากรท่านอื่นในระบบ";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=edit&id=" . $id);
                exit();
            }

            $oldData = $employeeModel->readOne($id);
            $old_position = $oldData['emp_code'];
            $new_position = trim($_POST['emp_code'] ?? '');

            $avatar_filename = null;
            if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
                $avatar_filename = $this->uploadAvatar($_FILES['avatar']);
                if($avatar_filename && !empty($oldData['avatar']) && file_exists("uploads/" . $oldData['avatar'])) {
                    unlink("uploads/" . $oldData['avatar']); // ลบรูปเก่า
                }
            }
            
            // 💡 [ข้อเสนอแนะ 5]: ฟีเจอร์ลบรูปโปรไฟล์
            if(isset($_POST['remove_avatar']) && $_POST['remove_avatar'] == '1') {
                if(!empty($oldData['avatar']) && file_exists("uploads/" . $oldData['avatar'])) {
                    unlink("uploads/" . $oldData['avatar']);
                }
                $avatar_filename = null; // บังคับให้เป็น null เพื่อลบออกจาก DB
            } elseif ($avatar_filename === null && isset($oldData['avatar'])) {
               // ถ้าไม่ได้อัพโหลดรูปใหม่ และไม่ได้ติ๊กลบ ให้ใช้รูปเดิม
               $avatar_filename = $oldData['avatar'];
            }

            $data = [
                'emp_code' => $new_position,
                'national_id' => $national_id,
                'prefix' => $_POST['prefix'] ?? '',
                'gender' => $_POST['gender'] ?? '',
                'dob' => trim($_POST['dob'] ?? ''),
                'first_name' => trim($_POST['first_name'] ?? ''),
                'last_name' => trim($_POST['last_name'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'avatar' => $avatar_filename
            ];

            if($employeeModel->update($id, $data)) {
                $this->saveRelations($employeeModel, $id, $_POST);

                // อัปเดตสถานะกรอบอัตรากำลัง กรณีมีการเปลี่ยนตำแหน่ง
                if($old_position !== $new_position) {
                    if(!empty($old_position)) $manpowerModel->updateStatusByPositionNumber($old_position, 'vacant');
                    if(!empty($new_position)) $manpowerModel->updateStatusByPositionNumber($new_position, 'occupied');
                }

                $_SESSION['message'] = "อัปเดตข้อมูลประวัติ (ก.พ. 7) สำเร็จ!";
                $_SESSION['message_type'] = "success";
                header("Location: index.php?action=employees");
                exit();
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการแก้ไขข้อมูล!";
                $_SESSION['message_type'] = "danger";
                header("Location: index.php?action=edit&id=" . $id);
                exit();
            }
        }
    }

    // 7. ฟังก์ชันตัวช่วยสำหรับบันทึกข้อมูลที่เกี่ยวข้อง (Relations) แบบตารางย่อยต่างๆ
    // 🚨 [แก้ไขบั๊กที่ 3]: เพิ่มการเช็ค is_array() ก่อนนำไปบันทึก
    private function saveRelations($model, $emp_id, $postData) {
        // ประวัติครอบครัว
        $familyData = [
            'father_name' => $postData['father_name'] ?? '', 
            'mother_name' => $postData['mother_name'] ?? '', 
            'spouse_name' => $postData['spouse_name'] ?? '', 
            'children_count' => (int)($postData['children_count'] ?? 0)
        ];
        $model->updateFamily($emp_id, $familyData);
        
        // ประวัติการศึกษา
        if(isset($postData['edu_degree']) && is_array($postData['edu_degree'])) {
            $model->updateRelations("emp_education", $emp_id, [$postData['edu_degree'], $postData['edu_major'], $postData['edu_inst'], $postData['edu_year']], ['degree_level', 'major', 'institution', 'graduation_year']);
        }
        
        // ประวัติการฝึกอบรม
        if(isset($postData['trn_course']) && is_array($postData['trn_course'])) {
            $model->updateRelations("emp_training", $emp_id, [$postData['trn_course'], $postData['trn_inst'], $postData['trn_year']], ['course_name', 'institution', 'training_year']);
        }
        
        // ประวัติการทำงาน (ก.พ. 7)
        if(isset($postData['wk_date']) && is_array($postData['wk_date'])) {
            $model->updateRelations("emp_work_history", $emp_id, 
                [$postData['wk_date'], $postData['wk_order'], $postData['wk_pos'], $postData['wk_num'], $postData['wk_level'], $postData['wk_salary'], $postData['wk_agency'], $postData['wk_dept']], 
                ['start_date', 'order_number', 'position_name', 'position_number', 'level', 'salary', 'agency', 'department']);
        }
        
        // ข้อมูลอื่นๆ
        if(isset($postData['lv_year']) && is_array($postData['lv_year'])) $model->updateRelations("emp_leave", $emp_id, [$postData['lv_year'], $postData['lv_sick'], $postData['lv_personal'], $postData['lv_vacation'], $postData['lv_late']], ['leave_year', 'sick_leave', 'personal_leave', 'vacation_leave', 'late_count']);
        if(isset($postData['act_pos']) && is_array($postData['act_pos'])) $model->updateRelations("emp_acting", $emp_id, [$postData['act_pos'], $postData['act_order'], $postData['act_date']], ['acting_position', 'order_number', 'start_date']);
        if(isset($postData['ev_year']) && is_array($postData['ev_year'])) $model->updateRelations("emp_evaluation", $emp_id, [$postData['ev_year'], $postData['ev_round'], $postData['ev_score'], $postData['ev_level']], ['eval_year', 'eval_round', 'score_percent', 'result_level']);
        if(isset($postData['dec_name']) && is_array($postData['dec_name'])) $model->updateRelations("emp_decoration", $emp_id, [$postData['dec_name'], $postData['dec_year'], $postData['dec_info']], ['decor_name', 'received_year', 'gazette_info']);
        if(isset($postData['dis_date']) && is_array($postData['dis_date'])) $model->updateRelations("emp_disciplinary", $emp_id, [$postData['dis_date'], $postData['dis_type'], $postData['dis_desc']], ['incident_date', 'punishment_type', 'description']);
        if(isset($postData['lic_name']) && is_array($postData['lic_name'])) $model->updateRelations("emp_license", $emp_id, [$postData['lic_name'], $postData['lic_num'], $postData['lic_issue'], $postData['lic_expire']], ['license_name', 'license_number', 'issue_date', 'expiry_date']);
    }

    // 8. อัปเดตสถานะการทำงาน (กรณีไม่ได้ใช้ AJAX)
    public function updateStatus() {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
            $employeeModel = new Employee($this->db);
            $id = $_POST['id'];
            $status = $_POST['status'];

            if ($employeeModel->updateStatus($id, $status)) {
                // หากสถานะคือ ลาออก, เกษียณอายุ, โอนย้าย หรือเสียชีวิต ให้คืนกรอบอัตรากำลังเป็น vacant
                if (in_array($status, ['ลาออก', 'เกษียณอายุ', 'โอนย้าย', 'เสียชีวิต'])) {
                    $manpowerModel = new Manpower($this->db);
                    $empData = $employeeModel->readOne($id);
                    if (!empty($empData['emp_code'])) {
                        $manpowerModel->updateStatusByPositionNumber($empData['emp_code'], 'vacant');
                    }
                }
                
                $_SESSION['message'] = "อัปเดตสถานะบุคลากรสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัปเดตสถานะ!";
                $_SESSION['message_type'] = "danger";
            }
            header("Location: index.php?action=employees");
            exit();
        }
    }

    // 9. ลบข้อมูลบุคลากร
    public function delete() {
        if(isset($_GET['id'])) {
            $employeeModel = new Employee($this->db);
            $manpowerModel = new Manpower($this->db);
            $id = $_GET['id'];
            $empData = $employeeModel->readOne($id);
            $position_to_free = $empData['emp_code'];

            if(!empty($empData['avatar']) && file_exists("uploads/" . $empData['avatar'])) {
                unlink("uploads/" . $empData['avatar']);
            }
            if($employeeModel->delete($id)) {
                // คืนกรอบอัตรากำลังให้ว่าง
                if(!empty($position_to_free)) {
                    $manpowerModel->updateStatusByPositionNumber($position_to_free, 'vacant');
                }
                $_SESSION['message'] = "ลบข้อมูลประวัติ และคืนกรอบอัตรากำลังสำเร็จ!"; 
                $_SESSION['message_type'] = "success";
            }
            header("Location: index.php?action=employees"); 
            exit();
        }
    }

    // 10. ฟังก์ชันสำหรับเปิด/ปิดสถานะการใช้งาน (Login) ของพนักงาน (is_active)
    public function toggleActive() {
        if (isset($_GET['id']) && isset($_GET['status'])) {
            $id = $_GET['id'];
            $status = $_GET['status'] == '1' ? '1' : '0';

            require_once 'models/Employee.php';
            $empModel = new Employee($this->db);

            if ($empModel->toggleActive($id, $status)) {
                $_SESSION['message'] = "อัปเดตสถานะบุคลากรสำเร็จ!";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "เกิดข้อผิดพลาด ไม่สามารถเปลี่ยนสถานะได้!";
                $_SESSION['message_type'] = "danger";
            }
            
            header("Location: index.php?action=employees");
            exit();
        }
    }

    // 11. ฟังก์ชันสำหรับรับค่า AJAX และตอบกลับเป็น JSON สำหรับ Dropdown สถานะ 🌟
    public function updateStatusAjax() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id']) && isset($_POST['status'])) {
            $id = $_POST['id'];
            $status = trim($_POST['status']);

            require_once 'models/Employee.php';
            $empModel = new Employee($this->db);

            if ($empModel->updateStatus($id, $status)) {
                // หากสถานะเข้าข่ายพ้นจากตำแหน่ง ให้เคลียร์กรอบอัตรากำลัง
                if (in_array($status, ['ลาออก', 'เกษียณอายุ', 'โอนย้าย', 'เสียชีวิต'])) {
                    $manpowerModel = new Manpower($this->db);
                    $empData = $empModel->readOne($id);
                    if (!empty($empData['emp_code'])) {
                        $manpowerModel->updateStatusByPositionNumber($empData['emp_code'], 'vacant');
                    }
                }
                
                echo json_encode(['status' => 'success', 'message' => 'อัปเดตสถานะสำเร็จ']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'เกิดข้อผิดพลาดที่ฐานข้อมูล']);
            }
            exit();
        }
        
        echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit();
    }

    // 12. ส่งออกรายชื่อบุคลากรเป็นไฟล์ Excel (.xls) 🌟
    public function exportExcel() {
        $query = "SELECT e.*, w.position_name AS position, w.department, w.level, m.employee_type 
                  FROM employees e
                  LEFT JOIN emp_work_history w ON w.id = (
                      SELECT id FROM emp_work_history WHERE employee_id = e.id ORDER BY id DESC LIMIT 1
                  )
                  LEFT JOIN manpower m ON m.position_number COLLATE utf8mb4_unicode_ci = e.emp_code COLLATE utf8mb4_unicode_ci
                  ORDER BY e.first_name ASC";
                  
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // กำหนด Header สำหรับการสร้างไฟล์ Excel (ใช้ HTML Table พื้นฐาน)
        $filename = "employee_export_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // จำเป็นต้องใส่ BOM เพื่อให้ Excel แสดงผลภาษาไทยได้ถูกต้อง
        echo "\xEF\xBB\xBF";

        // พิมพ์ตาราง HTML ที่จะถูกแปลงเป็น Excel
        echo "<table border='1'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>รหัสพนักงาน</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>เลขบัตรประชาชน</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>คำนำหน้า</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>ชื่อ</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>นามสกุล</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>เพศ</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>ประเภทบุคลากร</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>ตำแหน่ง</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>ระดับ</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>ส่วนราชการ</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>เบอร์โทรศัพท์</th>";
        echo "<th style='background-color:#0d9488; color:#ffffff;'>สถานะการทำงาน</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        if(empty($employees)) {
            echo "<tr><td colspan='12' align='center'>ไม่มีข้อมูลบุคลากร</td></tr>";
        } else {
            foreach($employees as $emp) {
                echo "<tr>";
                // ใช้ ="ตัวเลข" เพื่อป้องกัน Excel ลบเลข 0 ข้างหน้าทิ้ง
                echo "<td>=\"" . ($emp['emp_code'] ?? '') . "\"</td>";
                echo "<td>=\"" . ($emp['national_id'] ?? '') . "\"</td>";
                echo "<td>" . htmlspecialchars($emp['prefix'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($emp['first_name'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($emp['last_name'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($emp['gender'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($emp['employee_type'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($emp['position'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($emp['level'] ?? '') . "</td>";
                echo "<td>" . htmlspecialchars($emp['department'] ?? '') . "</td>";
                echo "<td>=\"" . ($emp['phone'] ?? '') . "\"</td>";
                echo "<td>" . htmlspecialchars($emp['status'] ?? 'ปฏิบัติงาน') . "</td>";
                echo "</tr>";
            }
        }

        echo "</tbody>";
        echo "</table>";
        exit();
    }
}
?>