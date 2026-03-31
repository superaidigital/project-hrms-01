<?php require_once 'views/layout/header.php'; ?>

<!-- 🌟 เพิ่ม CSS ของ Tom Select สำหรับทำช่องค้นหา Dropdown 🌟 -->
<link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i> เพิ่มประวัติบุคลากรใหม่ (ก.พ. 7)</h5>
                    <a href="index.php?action=employees" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i> กลับหน้ารวม</a>
                </div>
                <div class="card-body">
                    
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <form action="index.php?action=store" method="POST" enctype="multipart/form-data" id="kp7Form">
                        
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
                                        <input type="text" name="national_id" class="form-control" required maxlength="13">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">คำนำหน้า <span class="text-danger">*</span></label>
                                        <select name="prefix" class="form-select" required>
                                            <option value="นาย">นาย</option>
                                            <option value="นาง">นาง</option>
                                            <option value="นางสาว">นางสาว</option>
                                            <option value="ว่าที่ ร.ต.">ว่าที่ ร.ต.</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">ชื่อจริง <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <label class="form-label">เพศ</label>
                                        <select name="gender" class="form-select">
                                            <option value="ชาย">ชาย</option>
                                            <option value="หญิง">หญิง</option>
                                            <option value="ไม่ระบุ">ไม่ระบุ</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">วันเกิด (วว/ดด/ปปปป) <span class="text-danger">*</span></label>
                                        <input type="text" name="dob" class="form-control" required placeholder="เช่น 15/04/2530">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">เบอร์โทรศัพท์</label>
                                        <input type="text" name="phone" class="form-control" maxlength="10">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">อีเมล</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                </div>

                                <hr>
                                <h6 class="text-primary mt-4 mb-3">ตำแหน่งและการบรรจุ (จากกรอบอัตรากำลัง)</h6>
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label class="form-label">ระบุตำแหน่งที่ว่าง (สามารถพิมพ์ค้นหาได้) <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <!-- 🌟 เปลี่ยน Select เป็นแบบพิมพ์ค้นหาได้ 🌟 -->
                                            <div style="flex: 1; min-width: 0;">
                                                <select name="emp_code" id="emp_code_select" required>
                                                    <option value="">-- พิมพ์ค้นหา หรือเลือกตำแหน่งที่ว่างในระบบ --</option>
                                                    <?php foreach($manpowers as $mp): ?>
                                                        <option value="<?= htmlspecialchars($mp['position_number']) ?>">
                                                            <?= htmlspecialchars($mp['position_number']) ?> - <?= htmlspecialchars($mp['position_name']) ?> (<?= htmlspecialchars($mp['employee_type']) ?>) [<?= htmlspecialchars($mp['department']) ?>]
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <!-- ปุ่มกดเพิ่มตำแหน่งด่วน -->
                                            <button class="btn btn-outline-success" type="button" data-bs-toggle="modal" data-bs-target="#addManpowerModal" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                                <i class="fas fa-plus"></i> ไม่พบเลขตำแหน่ง? (เพิ่มด่วน)
                                            </button>
                                        </div>
                                        <small class="text-muted">ระบบจะเปลี่ยนสถานะตำแหน่งที่เลือกให้เป็น "มีคนครอง" อัตโนมัติเมื่อบันทึก</small>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">อัปโหลดรูปประจำตัว</label>
                                        <input type="file" name="avatar" class="form-control" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <!-- ================= TAB 2: ครอบครัว ================= -->
                            <div class="tab-pane fade" id="family" role="tabpanel">
                                <div class="row mb-3 mt-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-สกุล บิดา</label>
                                        <input type="text" name="father_name" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-สกุล มารดา</label>
                                        <input type="text" name="mother_name" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">ชื่อ-สกุล คู่สมรส</label>
                                        <input type="text" name="spouse_name" class="form-control">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">จำนวนบุตร (คน)</label>
                                        <input type="number" name="children_count" class="form-control" value="0" min="0">
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
                                            <tr>
                                                <td><input type="text" name="edu_degree[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="edu_major[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="edu_inst[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="edu_year[]" class="form-control form-control-sm" maxlength="4"></td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                            </tr>
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
                                            <tr>
                                                <td><input type="text" name="trn_course[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="trn_inst[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="trn_year[]" class="form-control form-control-sm" maxlength="4"></td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                            </tr>
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
                                            <tr>
                                                <td><input type="text" name="wk_date[]" class="form-control form-control-sm" placeholder="วว/ดด/ปปปป"></td>
                                                <td><input type="text" name="wk_order[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="wk_pos[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="wk_num[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="wk_level[]" class="form-control form-control-sm"></td>
                                                <td><input type="number" step="0.01" name="wk_salary[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="wk_agency[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="wk_dept[]" class="form-control form-control-sm"></td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                            </tr>
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
                                                    <tr>
                                                        <td><input type="text" name="act_pos[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="act_order[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="act_date[]" class="form-control form-control-sm"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                    </tr>
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
                                                    <tr>
                                                        <td><input type="text" name="lv_year[]" class="form-control form-control-sm"></td>
                                                        <td><input type="number" name="lv_sick[]" class="form-control form-control-sm" value="0"></td>
                                                        <td><input type="number" name="lv_personal[]" class="form-control form-control-sm" value="0"></td>
                                                        <td><input type="number" name="lv_vacation[]" class="form-control form-control-sm" value="0"></td>
                                                        <td><input type="number" name="lv_late[]" class="form-control form-control-sm" value="0"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                    </tr>
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
                                            <tr>
                                                <td><input type="text" name="dec_name[]" class="form-control form-control-sm"></td>
                                                <td><input type="text" name="dec_year[]" class="form-control form-control-sm" maxlength="4"></td>
                                                <td><input type="text" name="dec_info[]" class="form-control form-control-sm"></td>
                                                <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                            </tr>
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
                                                    <tr>
                                                        <td><input type="text" name="dis_date[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="dis_type[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="dis_desc[]" class="form-control form-control-sm"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                    </tr>
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
                                                    <tr>
                                                        <td><input type="text" name="lic_name[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="lic_num[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="lic_issue[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="lic_expire[]" class="form-control form-control-sm"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                    </tr>
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
                                                    <tr>
                                                        <td><input type="text" name="ev_year[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="ev_round[]" class="form-control form-control-sm"></td>
                                                        <td><input type="number" step="0.01" name="ev_score[]" class="form-control form-control-sm"></td>
                                                        <td><input type="text" name="ev_level[]" class="form-control form-control-sm"></td>
                                                        <td class="text-center"><button type="button" class="btn btn-sm btn-danger" onclick="removeRow(this)"><i class="fas fa-trash"></i></button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ปุ่มบันทึกข้อมูล -->
                        <div class="text-end mt-4 pt-3 border-top">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> บันทึกประวัติบุคลากรใหม่</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- 🌟 Modal (Popup) สำหรับเพิ่มตำแหน่งกรอบอัตรากำลังแบบด่วน 🌟 -->
<div class="modal fade" id="addManpowerModal" tabindex="-1" aria-labelledby="addManpowerModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="addManpowerModalLabel"><i class="fas fa-plus-circle"></i> เพิ่มตำแหน่งในกรอบอัตรากำลัง (ด่วน)</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="ajaxManpowerForm">
          <div class="modal-body">
                <div id="modalAlert" class="alert d-none"></div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">สังกัดสำนัก/กอง <span class="text-danger">*</span></label>
                        <select name="department" class="form-select" required>
                            <option value="">-- เลือกสำนัก/กอง --</option>
                            <?php foreach($departments as $dept): ?>
                                <option value="<?= htmlspecialchars($dept['name']) ?>"><?= htmlspecialchars($dept['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ฝ่าย/ส่วน/งาน</label>
                        <input type="text" name="division" class="form-control" placeholder="เช่น ฝ่ายบริหารงานทั่วไป (ถ้าไม่มีให้ข้าม)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">ประเภทบุคลากร <span class="text-danger">*</span></label>
                        <select name="employee_type" class="form-select" required>
                            <option value="">-- เลือกประเภท --</option>
                            <option value="ข้าราชการ อบจ.">ข้าราชการ อบจ.</option>
                            <option value="ข้าราชการครู อบจ.">ข้าราชการครู อบจ.</option>
                            <option value="ลูกจ้างประจำ">ลูกจ้างประจำ</option>
                            <option value="พนักงานจ้างตามภารกิจ">พนักงานจ้างตามภารกิจ</option>
                            <option value="พนักงานจ้างทั่วไป">พนักงานจ้างทั่วไป</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">เลขที่ตำแหน่ง <span class="text-danger">*</span></label>
                        <input type="text" name="position_number" class="form-control" required placeholder="เช่น 01-1-001">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">ชื่อสายงาน/ตำแหน่ง <span class="text-danger">*</span></label>
                        <input type="text" name="position_name" class="form-control" required placeholder="เช่น นักทรัพยากรบุคคล">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-12">
                        <label class="form-label">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                        <select name="level" class="form-select" required>
                            <option value="">-- เลือกระดับ --</option>
                            <?php foreach($position_levels as $lvl): ?>
                                <option value="<?= htmlspecialchars($lvl['name']) ?>"><?= htmlspecialchars($lvl['name']) ?> (<?= htmlspecialchars($lvl['type']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
            <button type="submit" class="btn btn-success" id="btnSaveManpower"><i class="fas fa-save"></i> ยืนยันการเพิ่มตำแหน่ง</button>
          </div>
      </form>
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

let selectInstance; // ประกาศตัวแปรรับค่า Tom Select

document.addEventListener('DOMContentLoaded', function() {
    // 🌟 เริ่มการทำงานของช่องค้นหาตำแหน่ง (Tom Select)
    selectInstance = new TomSelect("#emp_code_select", {
        create: false, // ป้องกันผู้ใช้พิมพ์สร้างเองแบบมั่วๆ ต้องสร้างผ่าน Popup เท่านั้น
        sortField: {
            field: "text",
            direction: "asc"
        },
        placeholder: "-- พิมพ์ค้นหา หรือเลือกตำแหน่งที่ว่าง --"
    });

    const ajaxForm = document.getElementById('ajaxManpowerForm');
    const modalAlert = document.getElementById('modalAlert');
    const btnSave = document.getElementById('btnSaveManpower');

    // จัดการข้อมูลจากการส่งฟอร์ม Popup (AJAX)
    ajaxForm.addEventListener('submit', function(e) {
        e.preventDefault(); 
        
        btnSave.disabled = true;
        btnSave.innerHTML = '<i class="fas fa-spinner fa-spin"></i> กำลังบันทึก...';
        modalAlert.className = 'alert d-none';

        const formData = new FormData(ajaxForm);

        fetch('index.php?action=manpower_store_ajax', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.status === 'success') {
                modalAlert.className = 'alert alert-success';
                modalAlert.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
                
                // 🌟 ดึงข้อมูลที่เพิ่มสำเร็จไปใส่ในช่องค้นหา (Tom Select) และเลือกให้อัตโนมัติ 🌟
                const newOptionText = `${data.data.position_number} - ${data.data.position_name} (${data.data.employee_type}) [เพิ่มใหม่]`;
                
                selectInstance.addOption({
                    value: data.data.position_number,
                    text: newOptionText
                });
                selectInstance.addItem(data.data.position_number); // บังคับเลือกค่าที่เพิ่งสร้างใหม่

                setTimeout(() => {
                    const myModalEl = document.getElementById('addManpowerModal');
                    const modal = bootstrap.Modal.getInstance(myModalEl);
                    modal.hide();
                    ajaxForm.reset(); 
                    modalAlert.className = 'alert d-none';
                }, 1000);

            } else {
                modalAlert.className = 'alert alert-danger';
                modalAlert.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + data.message;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalAlert.className = 'alert alert-danger';
            modalAlert.innerHTML = '<i class="fas fa-exclamation-triangle"></i> เกิดข้อผิดพลาดในการเชื่อมต่อเซิร์ฟเวอร์';
        })
        .finally(() => {
            btnSave.disabled = false;
            btnSave.innerHTML = '<i class="fas fa-save"></i> ยืนยันการเพิ่มตำแหน่ง';
        });
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>