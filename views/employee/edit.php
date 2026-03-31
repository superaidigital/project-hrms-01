<!-- 
==========================================
ชื่อไฟล์: edit.php
ที่อยู่ไฟล์: views/employee/edit.php
==========================================
-->
<?php include 'views/layout/header.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
<style>
    .ts-control { background-color: #f8f9fa !important; border: 1px solid #dee2e6 !important; padding: 0.45rem 0.75rem !important; border-radius: 0.375rem !important; }
    .ts-wrapper.form-select { padding: 0 !important; border: none !important; }
    .nav-tabs .nav-link { color: #64748b; font-weight: 600; border: none; border-bottom: 3px solid transparent; padding: 15px 20px; transition: all 0.3s; }
    .nav-tabs .nav-link:hover { color: #0d9488; background-color: #f8fafc; border-color: transparent; }
    .nav-tabs .nav-link.active { color: #0d9488; border-bottom: 3px solid #0d9488; background-color: transparent; }
    .tab-content { padding-top: 30px; }
    .dynamic-table th { background-color: #f1f5f9; color: #475569; font-size: 0.85rem; }
    .dynamic-table td { vertical-align: middle; padding: 0.5rem; }
    .dynamic-table input { border: 1px solid #e2e8f0; }
    .dynamic-table input:focus { border-color: #0d9488; box-shadow: 0 0 0 0.2rem rgba(13, 148, 136, 0.25); }
    .btn-add-row { border: 1px dashed #0d9488; color: #0d9488; background: #f0fdfa; font-weight: 600; width: 100%; transition: all 0.2s; }
    .btn-add-row:hover { background: #ccfbf1; color: #0f766e; border-color: #0f766e; }
</style>

<?php 
$fam = $empData['family'] ?? [];
$edu = !empty($empData['education']) ? $empData['education'] : [[]];
$trn = !empty($empData['training']) ? $empData['training'] : [[]];
$wk  = !empty($empData['work_history']) ? $empData['work_history'] : [[]];
$act = !empty($empData['acting']) ? $empData['acting'] : [[]];
$ev  = !empty($empData['evaluation']) ? $empData['evaluation'] : [[]];
$lv  = !empty($empData['leave']) ? $empData['leave'] : [[]];
$dec = !empty($empData['decoration']) ? $empData['decoration'] : [[]];
$dis = !empty($empData['disciplinary']) ? $empData['disciplinary'] : [[]];
$lic = !empty($empData['license']) ? $empData['license'] : [[]];
?>

<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="index.php?action=employees" class="btn btn-light border shadow-sm fw-semibold text-secondary mb-3">
            <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้ารายการ
        </a>
        <h2 class="fw-bold text-dark"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> แก้ไขประวัติ (ก.พ. 7) แบบละเอียด</h2>
        <p class="text-muted mb-0">คุณกำลังแก้ไขข้อมูลของ: <span class="text-primary fw-bold"><?php echo htmlspecialchars($empData['prefix'].$empData['first_name'].' '.$empData['last_name']); ?></span></p>
    </div>
</div>

<div class="modern-card p-0 border-0 shadow-sm overflow-hidden">
    <form action="index.php?action=update" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $empData['id']; ?>">
        
        <ul class="nav nav-tabs bg-light px-3 pt-2" id="editTabs" role="tablist">
            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-main" type="button"><i class="fa-solid fa-id-card me-1"></i> ข้อมูลส่วนบุคคล</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-family" type="button"><i class="fa-solid fa-people-roof me-1"></i> ครอบครัว</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edu" type="button"><i class="fa-solid fa-user-graduate me-1"></i> การศึกษา/อบรม</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-work" type="button"><i class="fa-solid fa-briefcase me-1"></i> ประวัติรับราชการ</button></li>
            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-other" type="button"><i class="fa-solid fa-star me-1"></i> สิทธิ/วินัย/ลา</button></li>
        </ul>

        <div class="tab-content px-4 px-md-5 pb-5" id="editTabsContent">
            
            <!-- Tab 1: ข้อมูลส่วนบุคคล -->
            <div class="tab-pane fade show active" id="tab-main" role="tabpanel">
                <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">ข้อมูลส่วนบุคคลพื้นฐาน</h5>
                <div class="row g-4 mb-3">
                    
                    <!-- อัปเดต: เปลี่ยนเป็นดึง "เลขประจำตำแหน่ง" จากกรอบอัตรากำลัง -->
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold text-uppercase">เลขประจำตำแหน่ง (จากกรอบอัตรากำลัง) <span class="text-danger">*</span></label>
                        <select id="select-emp-code" class="form-select bg-light" name="emp_code" required>
                            <option value="">- พิมพ์ค้นหาเลขตำแหน่ง หรือ ชื่อตำแหน่ง -</option>
                            <?php foreach($manpowers as $mp): ?>
                                <option value="<?php echo htmlspecialchars($mp['position_number']); ?>" <?php echo ($empData['emp_code'] == $mp['position_number']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($mp['position_number'] . ' : ' . $mp['position_name'] . ' (' . $mp['department'] . ')'); ?> 
                                    <?php echo $mp['status'] == 'vacant' ? '[ว่าง]' : '[มีผู้ครอง]'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold text-uppercase">เลขประจำตัวประชาชน <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" name="national_id" required maxlength="13" value="<?php echo htmlspecialchars($empData['national_id']); ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label text-muted small fw-bold text-uppercase">คำนำหน้า</label>
                        <select class="form-select bg-light" name="prefix">
                            <option value="นาย" <?php echo ($empData['prefix'] == 'นาย') ? 'selected' : ''; ?>>นาย</option>
                            <option value="นาง" <?php echo ($empData['prefix'] == 'นาง') ? 'selected' : ''; ?>>นาง</option>
                            <option value="นางสาว" <?php echo ($empData['prefix'] == 'นางสาว') ? 'selected' : ''; ?>>นางสาว</option>
                            <option value="ว่าที่ ร.ต." <?php echo ($empData['prefix'] == 'ว่าที่ ร.ต.') ? 'selected' : ''; ?>>ว่าที่ ร.ต.</option>
                            <option value="ว่าที่ ร.ต. หญิง" <?php echo ($empData['prefix'] == 'ว่าที่ ร.ต. หญิง') ? 'selected' : ''; ?>>ว่าที่ ร.ต. หญิง</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold text-uppercase">ชื่อ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" name="first_name" required value="<?php echo htmlspecialchars($empData['first_name']); ?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label text-muted small fw-bold text-uppercase">นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" name="last_name" required value="<?php echo htmlspecialchars($empData['last_name']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">เพศ</label>
                        <select class="form-select bg-light" name="gender">
                            <option value="ชาย" <?php echo ($empData['gender'] == 'ชาย') ? 'selected' : ''; ?>>ชาย</option>
                            <option value="หญิง" <?php echo ($empData['gender'] == 'หญิง') ? 'selected' : ''; ?>>หญิง</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">วัน/เดือน/ปี เกิด (พ.ศ.)</label>
                        <input type="text" class="form-control bg-light" name="dob" placeholder="เช่น 15/05/2530" value="<?php echo htmlspecialchars($empData['dob']); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted small fw-bold text-uppercase">เบอร์ติดต่อ</label>
                        <input type="text" class="form-control bg-light" name="phone" value="<?php echo htmlspecialchars($empData['phone'] ?? ''); ?>" placeholder="08x-xxx-xxxx">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label text-muted small fw-bold text-uppercase">Email</label>
                        <input type="email" class="form-control bg-light" name="email" value="<?php echo htmlspecialchars($empData['email'] ?? ''); ?>" placeholder="example@email.com">
                    </div>

                    <div class="col-md-12 d-flex align-items-center gap-3">
                        <div class="flex-grow-1">
                            <label class="form-label text-muted small fw-bold text-uppercase">อัปโหลดรูปถ่ายใหม่ (ถ้ามี)</label>
                            <input type="file" class="form-control bg-light" name="avatar" accept="image/*">
                        </div>
                        <?php if(!empty($empData['avatar']) && file_exists("uploads/" . $empData['avatar'])): ?>
                            <img src="uploads/<?php echo htmlspecialchars($empData['avatar']); ?>" class="img-thumbnail rounded" style="height: 60px; width: 60px; object-fit: cover;">
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Tab 2: ครอบครัว -->
            <div class="tab-pane fade" id="tab-family" role="tabpanel">
                <h5 class="fw-bold text-teal mb-4 border-bottom pb-2">ข้อมูลครอบครัว</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">ชื่อ-สกุล บิดา</label>
                        <input type="text" class="form-control bg-light" name="father_name" value="<?php echo htmlspecialchars($fam['father_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-muted small">ชื่อ-สกุล มารดา</label>
                        <input type="text" class="form-control bg-light" name="mother_name" value="<?php echo htmlspecialchars($fam['mother_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-bold text-muted small">ชื่อ-สกุล คู่สมรส (และชื่อที่เคยเปลี่ยน)</label>
                        <input type="text" class="form-control bg-light" name="spouse_name" value="<?php echo htmlspecialchars($fam['spouse_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">จำนวนบุตร (คน)</label>
                        <input type="number" class="form-control bg-light" name="children_count" value="<?php echo htmlspecialchars($fam['children_count'] ?? '0'); ?>">
                    </div>
                </div>
            </div>

            <!-- Tab 3: การศึกษา/อบรม -->
            <div class="tab-pane fade" id="tab-edu" role="tabpanel">
                <h5 class="fw-bold text-teal mb-3 border-bottom pb-2">ประวัติการศึกษา</h5>
                <div class="table-responsive mb-5">
                    <table class="table table-bordered dynamic-table mb-2">
                        <thead>
                            <tr>
                                <th>วุฒิการศึกษา (ระดับ)</th>
                                <th>สาขาวิชา/วิชาเอก</th>
                                <th>สถานศึกษา</th>
                                <th style="width: 120px;">ปีที่สำเร็จ</th>
                                <th style="width: 60px;" class="text-center"><i class="fa-solid fa-gear"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-edu">
                            <?php foreach($edu as $item): ?>
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" name="edu_degree[]" value="<?php echo htmlspecialchars($item['degree_level'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="edu_major[]" value="<?php echo htmlspecialchars($item['major'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="edu_inst[]" value="<?php echo htmlspecialchars($item['institution'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="edu_year[]" value="<?php echo htmlspecialchars($item['graduation_year'] ?? ''); ?>"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-add-row" onclick="addRowEdu()"><i class="fa-solid fa-plus me-1"></i> เพิ่มประวัติการศึกษา</button>
                </div>

                <h5 class="fw-bold text-teal mb-3 border-bottom pb-2">ประวัติการฝึกอบรม / ดูงาน</h5>
                <div class="table-responsive mb-2">
                    <table class="table table-bordered dynamic-table mb-2">
                        <thead>
                            <tr>
                                <th>หลักสูตร / โครงการ</th>
                                <th>หน่วยงานผู้จัด</th>
                                <th style="width: 120px;">ปี พ.ศ.</th>
                                <th style="width: 60px;" class="text-center"><i class="fa-solid fa-gear"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-trn">
                            <?php foreach($trn as $item): ?>
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" name="trn_course[]" value="<?php echo htmlspecialchars($item['course_name'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="trn_inst[]" value="<?php echo htmlspecialchars($item['institution'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="trn_year[]" value="<?php echo htmlspecialchars($item['training_year'] ?? ''); ?>"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-add-row" onclick="addRowTrn()"><i class="fa-solid fa-plus me-1"></i> เพิ่มประวัติฝึกอบรม</button>
                </div>
            </div>

            <!-- Tab 4: ประวัติรับราชการ (อัปเดตเพิ่มเลขคำสั่ง) -->
            <div class="tab-pane fade" id="tab-work" role="tabpanel">
                <h5 class="fw-bold text-teal mb-3 border-bottom pb-2">ประวัติการรับราชการ / การทำงาน</h5>
                <div class="table-responsive mb-5">
                    <table class="table table-bordered dynamic-table mb-2">
                        <thead>
                            <tr>
                                <th style="width: 100px;">วัน/เดือน/ปี</th>
                                <th>เลขคำสั่ง</th>
                                <th>ตำแหน่ง</th>
                                <th style="width: 100px;">เลขที่</th>
                                <th style="width: 120px;">ระดับ</th>
                                <th style="width: 120px;">เงินเดือน</th>
                                <th>สังกัด</th>
                                <th style="width: 50px;" class="text-center"><i class="fa-solid fa-gear"></i></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-work">
                            <?php foreach($wk as $item): ?>
                            <tr>
                                <td><input type="text" class="form-control form-control-sm" name="wk_date[]" value="<?php echo htmlspecialchars($item['start_date'] ?? ''); ?>" placeholder="วว/ดด/ปป"></td>
                                <td><input type="text" class="form-control form-control-sm" name="wk_order[]" value="<?php echo htmlspecialchars($item['order_number'] ?? ''); ?>" placeholder="ที่ .../25xx"></td>
                                <td><input type="text" class="form-control form-control-sm" name="wk_pos[]" value="<?php echo htmlspecialchars($item['position_name'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="wk_num[]" value="<?php echo htmlspecialchars($item['position_number'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="wk_level[]" value="<?php echo htmlspecialchars($item['level'] ?? ''); ?>"></td>
                                <td><input type="number" class="form-control form-control-sm" name="wk_salary[]" value="<?php echo htmlspecialchars($item['salary'] ?? ''); ?>"></td>
                                <td><input type="text" class="form-control form-control-sm" name="wk_dept[]" value="<?php echo htmlspecialchars($item['department'] ?? ''); ?>"></td>
                                <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-add-row" onclick="addRowWork()"><i class="fa-solid fa-plus me-1"></i> เพิ่มประวัติรับราชการ</button>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <h6 class="fw-bold text-teal border-bottom pb-2">การรักษาราชการแทน / รักษาการ</h6>
                        <table class="table table-bordered dynamic-table mb-2">
                            <thead>
                                <tr>
                                    <th>ตำแหน่งที่รักษาการ</th>
                                    <th>เลขที่คำสั่ง</th>
                                    <th>วันที่เริ่ม</th>
                                    <th style="width: 50px;" class="text-center"><i class="fa-solid fa-gear"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-act">
                                <?php foreach($act as $item): ?>
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm" name="act_pos[]" value="<?php echo htmlspecialchars($item['acting_position'] ?? ''); ?>"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="act_order[]" value="<?php echo htmlspecialchars($item['order_number'] ?? ''); ?>"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="act_date[]" value="<?php echo htmlspecialchars($item['start_date'] ?? ''); ?>"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-add-row" onclick="addRowAct()"><i class="fa-solid fa-plus me-1"></i> เพิ่มแถว</button>
                    </div>

                    <div class="col-12 col-lg-6">
                        <h6 class="fw-bold text-teal border-bottom pb-2">ประเมินผลปฏิบัติงาน</h6>
                        <table class="table table-bordered dynamic-table mb-2">
                            <thead>
                                <tr>
                                    <th>ปี พ.ศ.</th>
                                    <th>รอบที่</th>
                                    <th>ร้อยละ</th>
                                    <th>ระดับผล (ดีเด่น)</th>
                                    <th style="width: 50px;" class="text-center"><i class="fa-solid fa-gear"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-ev">
                                <?php foreach($ev as $item): ?>
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm" name="ev_year[]" value="<?php echo htmlspecialchars($item['eval_year'] ?? ''); ?>"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="ev_round[]" value="<?php echo htmlspecialchars($item['eval_round'] ?? ''); ?>"></td>
                                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="ev_score[]" value="<?php echo htmlspecialchars($item['score_percent'] ?? ''); ?>"></td>
                                    <td><input type="text" class="form-control form-control-sm" name="ev_level[]" value="<?php echo htmlspecialchars($item['result_level'] ?? ''); ?>"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-add-row" onclick="addRowEv()"><i class="fa-solid fa-plus me-1"></i> เพิ่มแถว</button>
                    </div>
                </div>
            </div>

            <!-- Tab 5: สิทธิประโยชน์/อื่นๆ (เหมือนเดิม) -->
            <div class="tab-pane fade" id="tab-other" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-lg-6">
                        <h6 class="fw-bold text-teal border-bottom pb-2">ประวัติการลา (รายปีงบประมาณ)</h6>
                        <table class="table table-bordered dynamic-table mb-2">
                            <thead>
                                <tr>
                                    <th>ปี พ.ศ.</th><th>ป่วย</th><th>กิจ</th><th>พักผ่อน</th><th>มาสาย</th><th style="width: 50px;" class="text-center"><i class="fa-solid fa-trash"></i></th>
                                </tr>
                            </thead>
                            <tbody id="tbody-lv">
                                <?php foreach($lv as $item): ?>
                                <tr>
                                    <td><input type="text" class="form-control form-control-sm" name="lv_year[]" value="<?php echo htmlspecialchars($item['leave_year'] ?? ''); ?>"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="lv_sick[]" value="<?php echo htmlspecialchars($item['sick_leave'] ?? ''); ?>"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="lv_personal[]" value="<?php echo htmlspecialchars($item['personal_leave'] ?? ''); ?>"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="lv_vacation[]" value="<?php echo htmlspecialchars($item['vacation_leave'] ?? ''); ?>"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="lv_late[]" value="<?php echo htmlspecialchars($item['late_count'] ?? ''); ?>"></td>
                                    <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-add-row" onclick="addRowLv()"><i class="fa-solid fa-plus me-1"></i> เพิ่มประวัติการลา</button>
                    </div>
                    <!-- (ส่วนอื่นๆ ใน Tab 5 ย่อไว้ให้เหมือนเดิม เพื่อประหยัดความยาวโค้ด) -->
                    <div class="col-12 col-lg-6">
                        <h6 class="fw-bold text-teal border-bottom pb-2">เครื่องราชอิสริยาภรณ์</h6>
                        <table class="table table-bordered dynamic-table mb-2">
                            <thead><tr><th>ชั้นเครื่องราชฯ</th><th>ปี พ.ศ.</th><th>อ้างอิงราชกิจจาฯ</th><th style="width: 50px;" class="text-center"><i class="fa-solid fa-trash"></i></th></tr></thead>
                            <tbody id="tbody-dec">
                                <?php foreach($dec as $item): ?>
                                <tr><td><input type="text" class="form-control form-control-sm" name="dec_name[]" value="<?php echo htmlspecialchars($item['decor_name'] ?? ''); ?>"></td><td><input type="text" class="form-control form-control-sm" name="dec_year[]" value="<?php echo htmlspecialchars($item['received_year'] ?? ''); ?>"></td><td><input type="text" class="form-control form-control-sm" name="dec_info[]" value="<?php echo htmlspecialchars($item['gazette_info'] ?? ''); ?>"></td><td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td></tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-sm btn-add-row" onclick="addRowDec()"><i class="fa-solid fa-plus me-1"></i> เพิ่มเครื่องราชฯ</button>
                    </div>
                </div>
            </div>

        </div> <!-- End Tab Content -->

        <div class="bg-light p-4 border-top d-flex justify-content-end gap-2 sticky-bottom">
            <a href="index.php?action=employees" class="btn btn-outline-secondary fw-bold px-4">ยกเลิก</a>
            <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm">
                <i class="fa-solid fa-save me-1"></i> บันทึกข้อมูลประวัติ
            </button>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // ตั้งค่าให้ช่องค้นหาเลขตำแหน่ง ใช้ Tom Select ได้
        new TomSelect("#select-emp-code", { 
            create: false, 
            maxOptions: null,
            placeholder: "- พิมพ์ค้นหาเลขตำแหน่ง หรือ ชื่อตำแหน่ง -" 
        });
    });

    function removeRow(btn) { btn.closest('tr').remove(); }

    function addRowEdu() {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="edu_degree[]"></td><td><input type="text" class="form-control form-control-sm" name="edu_major[]"></td><td><input type="text" class="form-control form-control-sm" name="edu_inst[]"></td><td><input type="text" class="form-control form-control-sm" name="edu_year[]"></td><td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>`;
        document.getElementById('tbody-edu').appendChild(tr);
    }
    function addRowTrn() {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="trn_course[]"></td><td><input type="text" class="form-control form-control-sm" name="trn_inst[]"></td><td><input type="text" class="form-control form-control-sm" name="trn_year[]"></td><td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>`;
        document.getElementById('tbody-trn').appendChild(tr);
    }
    // อัปเดต addRowWork ให้เพิ่มช่อง เลขคำสั่ง
    function addRowWork() {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="wk_date[]" placeholder="วว/ดด/ปป"></td>
                        <td><input type="text" class="form-control form-control-sm" name="wk_order[]" placeholder="ที่ .../25xx"></td>
                        <td><input type="text" class="form-control form-control-sm" name="wk_pos[]"></td>
                        <td><input type="text" class="form-control form-control-sm" name="wk_num[]"></td>
                        <td><input type="text" class="form-control form-control-sm" name="wk_level[]"></td>
                        <td><input type="number" class="form-control form-control-sm" name="wk_salary[]"></td>
                        <td><input type="text" class="form-control form-control-sm" name="wk_dept[]"></td>
                        <td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>`;
        document.getElementById('tbody-work').appendChild(tr);
    }
    function addRowAct() {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="act_pos[]"></td><td><input type="text" class="form-control form-control-sm" name="act_order[]"></td><td><input type="text" class="form-control form-control-sm" name="act_date[]"></td><td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>`;
        document.getElementById('tbody-act').appendChild(tr);
    }
    function addRowEv() {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="ev_year[]"></td><td><input type="text" class="form-control form-control-sm" name="ev_round[]"></td><td><input type="number" step="0.01" class="form-control form-control-sm" name="ev_score[]"></td><td><input type="text" class="form-control form-control-sm" name="ev_level[]"></td><td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>`;
        document.getElementById('tbody-ev').appendChild(tr);
    }
    function addRowLv() {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="lv_year[]"></td><td><input type="number" class="form-control form-control-sm" name="lv_sick[]" value="0"></td><td><input type="number" class="form-control form-control-sm" name="lv_personal[]" value="0"></td><td><input type="number" class="form-control form-control-sm" name="lv_vacation[]" value="0"></td><td><input type="number" class="form-control form-control-sm" name="lv_late[]" value="0"></td><td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>`;
        document.getElementById('tbody-lv').appendChild(tr);
    }
    function addRowDec() {
        const tr = document.createElement('tr');
        tr.innerHTML = `<td><input type="text" class="form-control form-control-sm" name="dec_name[]"></td><td><input type="text" class="form-control form-control-sm" name="dec_year[]"></td><td><input type="text" class="form-control form-control-sm" name="dec_info[]"></td><td class="text-center"><button type="button" class="btn btn-sm btn-light text-danger border" onclick="removeRow(this)"><i class="fa-solid fa-trash"></i></button></td>`;
        document.getElementById('tbody-dec').appendChild(tr);
    }
</script>
<?php include 'views/layout/footer.php'; ?>