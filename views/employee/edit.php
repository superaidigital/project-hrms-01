<!-- 
==========================================
ชื่อไฟล์: edit.php
ที่อยู่ไฟล์: views/employee/edit.php
==========================================
-->
<?php require_once 'views/layout/header.php'; ?>
<?php 
if (!function_exists('h')) {
    function h($string) { return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8'); }
}

// โหลดตัวแปรกันเหนียวเผื่อบางตารางไม่มีข้อมูล
$fam = $empData['family'] ?? [];
$edu = $empData['education'] ?? [];
$trn = $empData['training'] ?? [];
$wk  = $empData['work_history'] ?? [];
$act = $empData['acting'] ?? [];
$lv  = $empData['leave'] ?? [];
$dec = $empData['decoration'] ?? [];
$dis = $empData['disciplinary'] ?? [];
$lic = $empData['license'] ?? [];
$ev  = $empData['evaluation'] ?? [];
?>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-user-edit me-2"></i> แก้ไขประวัติบุคลากร (ก.พ. 7)</h5>
                    <a href="index.php?action=employees" class="btn btn-sm btn-light border"><i class="fas fa-arrow-left"></i> กลับหน้ารวม</a>
                </div>
                <div class="card-body">
                    
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= h($_SESSION['message_type']) ?> alert-dismissible fade show" role="alert">
                            <?= h($_SESSION['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <form action="index.php?action=update" method="POST" enctype="multipart/form-data" id="kp7Form">
                        <input type="hidden" name="id" value="<?= h($empData['id']) ?>">
                        
                        <!-- ระบบ Tabs สำหรับแบ่งหมวดหมู่ข้อมูล -->
                        <ul class="nav nav-tabs mb-4" id="kp7Tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active fw-bold" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab"><i class="fas fa-user"></i> ข้อมูลส่วนบุคคล & ตำแหน่ง</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="family-tab" data-bs-toggle="tab" data-bs-target="#family" type="button" role="tab"><i class="fas fa-users"></i> ครอบครัว</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="edu-tab" data-bs-toggle="tab" data-bs-target="#edu" type="button" role="tab"><i class="fas fa-graduation-cap"></i> การศึกษา & อบรม</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="work-tab" data-bs-toggle="tab" data-bs-target="#work" type="button" role="tab"><i class="fas fa-briefcase"></i> ประวัติการทำงาน & ลา</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link fw-bold" id="other-tab" data-bs-toggle="tab" data-bs-target="#other" type="button" role="tab"><i class="fas fa-award"></i> เครื่องราชฯ & อื่นๆ</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="kp7TabsContent">
                            
                            <!-- ================= TAB 1: ข้อมูลส่วนตัว ================= -->
                            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">เลขประจำตัวประชาชน <span class="text-danger">*</span></label>
                                        <input type="text" name="national_id" class="form-control" value="<?= h($empData['national_id']) ?>" required maxlength="13">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">คำนำหน้า <span class="text-danger">*</span></label>
                                        <select name="prefix" class="form-select" required>
                                            <option value="นาย" <?= $empData['prefix']=='นาย'?'selected':'' ?>>นาย</option>
                                            <option value="นาง" <?= $empData['prefix']=='นาง'?'selected':'' ?>>นาง</option>
                                            <option value="นางสาว" <?= $empData['prefix']=='นางสาว'?'selected':'' ?>>นางสาว</option>
                                            <option value="ว่าที่ ร.ต." <?= $empData['prefix']=='ว่าที่ ร.ต.'?'selected':'' ?>>ว่าที่ ร.ต.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">ชื่อจริง <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" value="<?= h($empData['first_name']) ?>" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" value="<?= h($empData['last_name']) ?>" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">เพศ</label>
                                        <select name="gender" class="form-select">
                                            <option value="ชาย" <?= $empData['gender']=='ชาย'?'selected':'' ?>>ชาย</option>
                                            <option value="หญิง" <?= $empData['gender']=='หญิง'?'selected':'' ?>>หญิง</option>
                                            <option value="ไม่ระบุ" <?= $empData['gender']=='ไม่ระบุ'?'selected':'' ?>>ไม่ระบุ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">วันเกิด (วว/ดด/ปปปป) <span class="text-danger">*</span></label>
                                        <input type="text" name="dob" class="form-control" value="<?= h($empData['dob']) ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="text" name="phone" class="form-control" value="<?= h($empData['phone']) ?>" maxlength="10">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">อีเมล</label>
                                        <input type="email" name="email" class="form-control" value="<?= h($empData['email']) ?>">
                                    </div>
                                </div>

                                <hr>
                                <h6 class="text-primary mt-4 mb-3">ตำแหน่งและการบรรจุ (จากกรอบอัตรากำลัง)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">ระบุตำแหน่งที่ว่าง (สามารถพิมพ์ค้นหาได้) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <div style="flex: 1; min-width: 0;">
                                                <select name="emp_code" id="emp_code_select" required>
                                                    <option value="">-- พิมพ์ค้นหา หรือเลือกตำแหน่ง --</option>
                                                    <?php foreach($manpowers as $mp): ?>
                                                        <option value="<?= h($mp['position_number']) ?>" <?= ($empData['emp_code'] == $mp['position_number']) ? 'selected' : '' ?>>
                                                            <?= h($mp['position_number']) ?> - <?= h($mp['position_name']) ?> (<?= h($mp['employee_type'] ?? '') ?>) [<?= h($mp['department'] ?? 'ไม่ระบุ') ?>]
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <!-- ไม่ให้เพิ่มตำแหน่งด่วนในหน้า Edit เพื่อความปลอดภัยของข้อมูล ให้ไปทำที่เมนูกรอบอัตรากำลังแทน -->
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">อัปโหลดรูปประจำตัวใหม่ (เว้นว่างไว้หากใช้รูปเดิม)</label>
                                        <div class="d-flex align-items-center gap-2 mb-2">
                                            <?php if(!empty($empData['avatar'])): ?>
                                                <img src="uploads/<?= h($empData['avatar']) ?>" class="rounded object-fit-cover shadow-sm" width="50" height="50">
                                            <?php endif; ?>
                                            <input type="file" name="avatar" class="form-control" accept="image/*">
                                        </div>
                                        <?php if(!empty($empData['avatar'])): ?>
                                            <div class="form-check mt-1">
                                                <input class="form-check-input" type="checkbox" name="remove_avatar" value="1" id="removeAvatar">
                                                <label class="form-check-label text-danger" for="removeAvatar"><small>ลบรูปโปรไฟล์ปัจจุบันทิ้ง</small></label>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= TAB 2: ครอบครัว ================= -->
                            <div class="tab-pane fade" id="family" role="tabpanel">
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-สกุล บิดา</label>
                                        <input type="text" name="father_name" class="form-control" value="<?= h($fam['father_name'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-สกุล มารดา</label>
                                        <input type="text" name="mother_name" class="form-control" value="<?= h($fam['mother_name'] ?? '') ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-สกุล คู่สมรส</label>
                                        <input type="text" name="spouse_name" class="form-control" value="<?= h($fam['spouse_name'] ?? '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">จำนวนบุตร (คน)</label>
                                        <input type="number" name="children_count" class="form-control" value="<?= h($fam['children_count'] ?? '0') ?>" min="0">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= TAB 3: การศึกษา & อบรม ================= -->
                            <div class="tab-pane fade" id="edu" role="tabpanel">
                                <h6 class="text-primary mt-3">ประวัติการศึกษา</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-sm" id="table_education">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>ระดับการศึกษา</th><th>สาขาวิชา/เอก</th><th>สถาบันการศึกษา</th><th>ปีที่สำเร็จการศึกษา</th>
                                                <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_education')"><i class="fas fa-plus"></i></button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($edu)): foreach($edu as $e): ?>
                                                <tr>
                                                    <td><input type="text" name="edu_degree[]" class="form-control form-control-sm" value="<?= h($e['degree_level']) ?>"></td>
                                                    <td><input type="text" name="edu_major[]" class="form-control form-control-sm" value="<?= h($e['major']) ?>"></td>
                                                    <td><input type="text" name="edu_inst[]" class="form-control form-control-sm" value="<?= h($e['institution']) ?>"></td>
                                                    <td><input type="text" name="edu_year[]" class="form-control form-control-sm" maxlength="4" value="<?= h($e['graduation_year']) ?>"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="edu_degree[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="edu_major[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="edu_inst[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="edu_year[]" class="form-control form-control-sm" maxlength="4"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <h6 class="text-primary mt-3">ประวัติการฝึกอบรม</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="table_training">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>หลักสูตร/โครงการ</th><th>หน่วยงานที่จัด</th><th>ปีที่อบรม</th>
                                                <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_training')"><i class="fas fa-plus"></i></button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($trn)): foreach($trn as $t): ?>
                                                <tr>
                                                    <td><input type="text" name="trn_course[]" class="form-control form-control-sm" value="<?= h($t['course_name']) ?>"></td>
                                                    <td><input type="text" name="trn_inst[]" class="form-control form-control-sm" value="<?= h($t['institution']) ?>"></td>
                                                    <td><input type="text" name="trn_year[]" class="form-control form-control-sm" maxlength="4" value="<?= h($t['training_year']) ?>"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="trn_course[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="trn_inst[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="trn_year[]" class="form-control form-control-sm" maxlength="4"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- ================= TAB 4: การทำงาน & ลา ================= -->
                            <div class="tab-pane fade" id="work" role="tabpanel">
                                <h6 class="text-primary mt-3">ประวัติการทำงาน / การเลื่อนขั้น</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-sm" id="table_work">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>วัน/เดือน/ปี</th><th>เลขที่คำสั่ง</th><th>ตำแหน่ง</th><th>เลขที่ตำแหน่ง</th>
                                                <th>ระดับ</th><th>เงินเดือน</th><th>หน่วยงาน</th><th>สำนัก/กอง</th>
                                                <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_work')"><i class="fas fa-plus"></i></button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($wk)): foreach($wk as $w): ?>
                                                <tr>
                                                    <td><input type="text" name="wk_date[]" class="form-control form-control-sm" value="<?= h($w['start_date']) ?>"></td>
                                                    <td><input type="text" name="wk_order[]" class="form-control form-control-sm" value="<?= h($w['order_number']) ?>"></td>
                                                    <td><input type="text" name="wk_pos[]" class="form-control form-control-sm" value="<?= h($w['position_name']) ?>"></td>
                                                    <td><input type="text" name="wk_num[]" class="form-control form-control-sm" value="<?= h($w['position_number']) ?>"></td>
                                                    <td><input type="text" name="wk_level[]" class="form-control form-control-sm" value="<?= h($w['level']) ?>"></td>
                                                    <td><input type="number" step="0.01" name="wk_salary[]" class="form-control form-control-sm" value="<?= h($w['salary']) ?>"></td>
                                                    <td><input type="text" name="wk_agency[]" class="form-control form-control-sm" value="<?= h($w['agency']) ?>"></td>
                                                    <td><input type="text" name="wk_dept[]" class="form-control form-control-sm" value="<?= h($w['department']) ?>"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="wk_date[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="wk_order[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="wk_pos[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="wk_num[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="wk_level[]" class="form-control form-control-sm"></td>
                                                    <td><input type="number" step="0.01" name="wk_salary[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="wk_agency[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="wk_dept[]" class="form-control form-control-sm"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">ประวัติการรักษาราชการแทน</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="table_acting">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>ตำแหน่งที่รักษาการ</th><th>คำสั่ง/ลงวันที่</th><th>ตั้งแต่วันที่</th>
                                                        <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_acting')"><i class="fas fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(!empty($act)): foreach($act as $a): ?>
                                                        <tr>
                                                            <td><input type="text" name="act_pos[]" class="form-control form-control-sm" value="<?= h($a['acting_position']) ?>"></td>
                                                            <td><input type="text" name="act_order[]" class="form-control form-control-sm" value="<?= h($a['order_number']) ?>"></td>
                                                            <td><input type="text" name="act_date[]" class="form-control form-control-sm" value="<?= h($a['start_date']) ?>"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endforeach; else: ?>
                                                        <tr>
                                                            <td><input type="text" name="act_pos[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="act_order[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="act_date[]" class="form-control form-control-sm"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary">ประวัติการลาราชการ</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="table_leave">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>ปี พ.ศ.</th><th>ลาป่วย</th><th>ลากิจ</th><th>ลาพักผ่อน</th><th>มาสาย</th>
                                                        <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_leave')"><i class="fas fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(!empty($lv)): foreach($lv as $l): ?>
                                                        <tr>
                                                            <td><input type="text" name="lv_year[]" class="form-control form-control-sm" value="<?= h($l['leave_year']) ?>"></td>
                                                            <td><input type="number" name="lv_sick[]" class="form-control form-control-sm" value="<?= h($l['sick_leave']) ?>"></td>
                                                            <td><input type="number" name="lv_personal[]" class="form-control form-control-sm" value="<?= h($l['personal_leave']) ?>"></td>
                                                            <td><input type="number" name="lv_vacation[]" class="form-control form-control-sm" value="<?= h($l['vacation_leave']) ?>"></td>
                                                            <td><input type="number" name="lv_late[]" class="form-control form-control-sm" value="<?= h($l['late_count']) ?>"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endforeach; else: ?>
                                                        <tr>
                                                            <td><input type="text" name="lv_year[]" class="form-control form-control-sm"></td>
                                                            <td><input type="number" name="lv_sick[]" class="form-control form-control-sm" value="0"></td>
                                                            <td><input type="number" name="lv_personal[]" class="form-control form-control-sm" value="0"></td>
                                                            <td><input type="number" name="lv_vacation[]" class="form-control form-control-sm" value="0"></td>
                                                            <td><input type="number" name="lv_late[]" class="form-control form-control-sm" value="0"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ================= TAB 5: อื่นๆ ================= -->
                            <div class="tab-pane fade" id="other" role="tabpanel">
                                
                                <h6 class="text-primary mt-3">ประวัติการได้รับเครื่องราชอิสริยาภรณ์</h6>
                                <div class="table-responsive mb-4">
                                    <table class="table table-bordered table-sm" id="table_decoration">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>ชื่อเครื่องราชอิสริยาภรณ์</th><th>ปีที่ได้รับ</th><th>ราชกิจจานุเบกษา เล่ม/ตอน/หน้า</th>
                                                <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_decoration')"><i class="fas fa-plus"></i></button></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if(!empty($dec)): foreach($dec as $d): ?>
                                                <tr>
                                                    <td><input type="text" name="dec_name[]" class="form-control form-control-sm" value="<?= h($d['decor_name']) ?>"></td>
                                                    <td><input type="text" name="dec_year[]" class="form-control form-control-sm" maxlength="4" value="<?= h($d['received_year']) ?>"></td>
                                                    <td><input type="text" name="dec_info[]" class="form-control form-control-sm" value="<?= h($d['gazette_info']) ?>"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endforeach; else: ?>
                                                <tr>
                                                    <td><input type="text" name="dec_name[]" class="form-control form-control-sm"></td>
                                                    <td><input type="text" name="dec_year[]" class="form-control form-control-sm" maxlength="4"></td>
                                                    <td><input type="text" name="dec_info[]" class="form-control form-control-sm"></td>
                                                    <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <h6 class="text-primary">ประวัติการดำเนินการทางวินัย</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="table_disciplinary">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>วันที่/คำสั่ง</th><th>ประเภทโทษ</th><th>รายละเอียด</th>
                                                        <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_disciplinary')"><i class="fas fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(!empty($dis)): foreach($dis as $d): ?>
                                                        <tr>
                                                            <td><input type="text" name="dis_date[]" class="form-control form-control-sm" value="<?= h($d['incident_date']) ?>"></td>
                                                            <td><input type="text" name="dis_type[]" class="form-control form-control-sm" value="<?= h($d['punishment_type']) ?>"></td>
                                                            <td><input type="text" name="dis_desc[]" class="form-control form-control-sm" value="<?= h($d['description']) ?>"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endforeach; else: ?>
                                                        <tr>
                                                            <td><input type="text" name="dis_date[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="dis_type[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="dis_desc[]" class="form-control form-control-sm"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h6 class="text-primary">ใบอนุญาตประกอบวิชาชีพ</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="table_license">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>ชื่อใบอนุญาต</th><th>เลขที่</th><th>วันออก</th><th>วันหมดอายุ</th>
                                                        <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_license')"><i class="fas fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(!empty($lic)): foreach($lic as $l): ?>
                                                        <tr>
                                                            <td><input type="text" name="lic_name[]" class="form-control form-control-sm" value="<?= h($l['license_name']) ?>"></td>
                                                            <td><input type="text" name="lic_num[]" class="form-control form-control-sm" value="<?= h($l['license_number']) ?>"></td>
                                                            <td><input type="text" name="lic_issue[]" class="form-control form-control-sm" value="<?= h($l['issue_date']) ?>"></td>
                                                            <td><input type="text" name="lic_expire[]" class="form-control form-control-sm" value="<?= h($l['expiry_date']) ?>"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endforeach; else: ?>
                                                        <tr>
                                                            <td><input type="text" name="lic_name[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="lic_num[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="lic_issue[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="lic_expire[]" class="form-control form-control-sm"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <h6 class="text-primary">ประวัติการประเมินผลการปฏิบัติราชการ</h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm" id="table_evaluation">
                                                <thead class="bg-light">
                                                    <tr>
                                                        <th>ปี พ.ศ.</th><th>รอบการประเมิน</th><th>คะแนน (%)</th><th>ระดับผลการประเมิน</th>
                                                        <th width="50" class="text-center"><button type="button" class="btn btn-sm btn-success" onclick="addRow('table_evaluation')"><i class="fas fa-plus"></i></button></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if(!empty($ev)): foreach($ev as $e): ?>
                                                        <tr>
                                                            <td><input type="text" name="ev_year[]" class="form-control form-control-sm" value="<?= h($e['eval_year']) ?>"></td>
                                                            <td><input type="text" name="ev_round[]" class="form-control form-control-sm" value="<?= h($e['eval_round']) ?>"></td>
                                                            <td><input type="number" step="0.01" name="ev_score[]" class="form-control form-control-sm" value="<?= h($e['score_percent']) ?>"></td>
                                                            <td><input type="text" name="ev_level[]" class="form-control form-control-sm" value="<?= h($e['result_level']) ?>"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endforeach; else: ?>
                                                        <tr>
                                                            <td><input type="text" name="ev_year[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="ev_round[]" class="form-control form-control-sm"></td>
                                                            <td><input type="number" step="0.01" name="ev_score[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="ev_level[]" class="form-control form-control-sm"></td>
                                                            <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                        </tr>
                                                    <?php endif; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ปุ่มบันทึกข้อมูล -->
                        <div class="text-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold text-dark"><i class="fas fa-save"></i> บันทึกการแก้ไขข้อมูล</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🌟 นำเข้าไฟล์ JS ของ Tom Select 🌟 -->
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

<script>
// ฟังก์ชันเพิ่ม-ลด แถวตารางในแท็บต่างๆ
function addRow(tableId) {
    const table = document.getElementById(tableId).getElementsByTagName('tbody')[0];
    const firstRow = table.rows[0];
    const newRow = firstRow.cloneNode(true);
    const inputs = newRow.querySelectorAll('input, select');
    inputs.forEach(input => {
        if(input.type === 'number') input.value = '';
        else input.value = '';
    });
    table.appendChild(newRow);
}

function removeRow(btn) {
    const row = btn.closest('tr');
    const tbody = row.closest('tbody');
    if (tbody.rows.length > 1) {
        tbody.removeChild(row);
    } else {
        const inputs = row.querySelectorAll('input, select');
        inputs.forEach(input => {
            if(input.type === 'number') input.value = '';
            else input.value = '';
        });
        alert('เคลียร์ข้อมูลแล้ว (ต้องมีอย่างน้อย 1 แถวในตาราง)');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // 🌟 เริ่มการทำงานของช่องค้นหาตำแหน่ง (Tom Select)
    new TomSelect("#emp_code_select", {
        create: false,
        sortField: { field: "text", direction: "asc" },
        placeholder: "-- พิมพ์ค้นหา หรือเลือกตำแหน่ง --"
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>