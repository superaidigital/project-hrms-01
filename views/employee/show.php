<!-- 
==========================================
ชื่อไฟล์: show.php
ที่อยู่ไฟล์: views/employee/show.php
==========================================
-->
<?php include 'views/layout/header.php'; ?>

<?php 
// ดึงข้อมูลตัวแปรเพื่อนำมาแสดงผล ป้องกัน Error หากไม่มีข้อมูล
$fam = $empData['family'] ?? [];
$edu = $empData['education'] ?? [];
$trn = $empData['training'] ?? [];
$wk  = $empData['work_history'] ?? [];
$act = $empData['acting'] ?? [];
$ev  = $empData['evaluation'] ?? [];
$lv  = $empData['leave'] ?? [];
$dec = $empData['decoration'] ?? [];
$dis = $empData['disciplinary'] ?? [];
$lic = $empData['license'] ?? [];

// หาตำแหน่งและสังกัด "ล่าสุด" จากประวัติการทำงาน (เอา Array ตัวสุดท้าย)
$currentWork = !empty($wk) ? end($wk) : null;
$currentPosition = $currentWork['position_name'] ?? 'ยังไม่ระบุตำแหน่ง';
$currentLevel = $currentWork['level'] ?? '';
$currentDept = $currentWork['department'] ?? 'ยังไม่ระบุสังกัด';
?>

<style>
    .nav-tabs .nav-link { color: #64748b; font-weight: 600; border: none; border-bottom: 3px solid transparent; padding: 12px 20px; transition: all 0.3s; }
    .nav-tabs .nav-link:hover { color: #0d9488; background-color: #f8fafc; border-color: transparent; }
    .nav-tabs .nav-link.active { color: #0d9488; border-bottom: 3px solid #0d9488; background-color: transparent; }
    .info-label { font-size: 0.75rem; color: #64748b; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px; margin-bottom: 0.25rem; }
    .info-value { font-size: 1rem; color: #1e293b; font-weight: 500; border-bottom: 1px solid #f1f5f9; padding-bottom: 0.75rem; margin-bottom: 1rem; }
    .custom-table th { background-color: #f8fafc; color: #475569; font-weight: 600; font-size: 0.85rem; }
    .custom-table td { vertical-align: middle; color: #334155; }
</style>

<div class="mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
    <div>
        <a href="index.php?action=employees" class="btn btn-light border shadow-sm fw-semibold text-secondary mb-2">
            <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้ารายการ
        </a>
        <h2 class="fw-bold text-dark mb-0"><i class="fa-regular fa-address-card text-teal me-2" style="color: #0d9488;"></i> ประวัติข้าราชการ / พนักงาน (ก.พ. 7)</h2>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=edit&id=<?php echo htmlspecialchars($empData['id'] ?? ''); ?>" class="btn btn-outline-primary shadow-sm bg-white fw-bold">
            <i class="fa-solid fa-pen-to-square me-1"></i> แก้ไขประวัติ
        </a>
        <a href="index.php?action=print_kp7&id=<?php echo htmlspecialchars($empData['id'] ?? ''); ?>" class="btn text-white shadow-sm fw-bold" style="background-color: #0d9488;" target="_blank">
            <i class="fa-solid fa-print me-1"></i> พิมพ์ ก.พ. 7 (A4)
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- คอลัมน์ซ้าย: รูปโปรไฟล์และข้อมูลสรุป -->
    <div class="col-md-4 col-lg-3">
        <div class="modern-card border-0 shadow-sm text-center p-4 sticky-top" style="top: 80px;">
            <div class="position-relative d-inline-block mb-3">
                <?php if(!empty($empData['avatar']) && file_exists("uploads/" . $empData['avatar'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($empData['avatar']); ?>" class="rounded-circle shadow-sm border border-4 border-white" style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="rounded-circle shadow-sm border border-4 border-white bg-light d-flex align-items-center justify-content-center mx-auto text-secondary" style="width: 150px; height: 150px; font-size: 3rem;">
                        <i class="fa-solid fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <h5 class="fw-bold text-dark mb-1">
                <?php echo htmlspecialchars(($empData['prefix'] ?? '') . ($empData['first_name'] ?? '') . ' ' . ($empData['last_name'] ?? '')); ?>
            </h5>
            <p class="text-teal fw-semibold small mb-3" style="color: #0d9488;">
                <?php echo htmlspecialchars($currentPosition ?? ''); ?> 
                <?php echo $currentLevel ? "(" . htmlspecialchars($currentLevel) . ")" : ""; ?>
            </p>

            <div class="d-grid gap-2">
                <div class="bg-light p-2 rounded text-start">
                    <div class="small text-muted mb-1">รหัสพนักงาน</div>
                    <div class="fw-bold text-dark"><i class="fa-solid fa-id-badge text-secondary me-2"></i> <?php echo htmlspecialchars($empData['emp_code'] ?? '-'); ?></div>
                </div>
                <div class="bg-light p-2 rounded text-start">
                    <div class="small text-muted mb-1">สังกัดปัจจุบัน</div>
                    <div class="fw-bold text-dark"><i class="fa-solid fa-building text-secondary me-2"></i> <?php echo htmlspecialchars($currentDept ?? '-'); ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- คอลัมน์ขวา: รายละเอียดประวัติต่างๆ -->
    <div class="col-md-8 col-lg-9">
        <div class="modern-card border-0 shadow-sm p-0 overflow-hidden h-100">
            
            <ul class="nav nav-tabs bg-light px-3 pt-2 border-bottom-0" id="profileTabs" role="tablist">
                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-personal" type="button"><i class="fa-solid fa-user text-teal me-1"></i> ส่วนบุคคล</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edu" type="button"><i class="fa-solid fa-graduation-cap text-teal me-1"></i> การศึกษา</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-work" type="button"><i class="fa-solid fa-briefcase text-teal me-1"></i> การทำงาน</button></li>
                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-other" type="button"><i class="fa-solid fa-star text-teal me-1"></i> อื่นๆ</button></li>
            </ul>

            <div class="tab-content p-4 p-md-5" id="profileTabsContent">
                
                <!-- Tab 1: ส่วนบุคคล & ครอบครัว -->
                <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                    <h5 class="fw-bold text-dark mb-4 border-bottom pb-2">ข้อมูลส่วนบุคคล</h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="info-label">ชื่อ - นามสกุล</div>
                            <div class="info-value"><?php echo htmlspecialchars(($empData['prefix'] ?? '') . ($empData['first_name'] ?? '') . ' ' . ($empData['last_name'] ?? '')); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-label">เลขประจำตัวประชาชน</div>
                            <div class="info-value"><?php echo htmlspecialchars($empData['national_id'] ?? '-'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="info-label">เพศ</div>
                            <div class="info-value"><?php echo htmlspecialchars($empData['gender'] ?? '-'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="info-label">วัน/เดือน/ปี เกิด (พ.ศ.)</div>
                            <div class="info-value"><?php echo htmlspecialchars($empData['dob'] ?? '-'); ?></div>
                        </div>
                        <div class="col-sm-4">
                            <div class="info-label">อายุ</div>
                            <div class="info-value">
                                <?php 
                                    if(!empty($empData['dob'])) {
                                        $parts = explode('/', $empData['dob']);
                                        if(count($parts) == 3) {
                                            $yearBE = (int)$parts[2];
                                            $currentYearBE = (int)date('Y') + 543;
                                            echo ($currentYearBE - $yearBE) . " ปี";
                                        } else { echo "-"; }
                                    } else { echo "-"; }
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-label">เบอร์ติดต่อ</div>
                            <div class="info-value"><?php echo htmlspecialchars($empData['phone'] ?? '-'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-label">อีเมล</div>
                            <div class="info-value"><?php echo htmlspecialchars($empData['email'] ?? '-'); ?></div>
                        </div>
                    </div>

                    <h5 class="fw-bold text-dark mt-4 mb-4 border-bottom pb-2">ข้อมูลครอบครัว</h5>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="info-label">ชื่อ-สกุล บิดา</div>
                            <div class="info-value"><?php echo htmlspecialchars($fam['father_name'] ?? '-'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-label">ชื่อ-สกุล มารดา</div>
                            <div class="info-value"><?php echo htmlspecialchars($fam['mother_name'] ?? '-'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-label">ชื่อ-สกุล คู่สมรส</div>
                            <div class="info-value"><?php echo htmlspecialchars($fam['spouse_name'] ?? '-'); ?></div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-label">จำนวนบุตร</div>
                            <div class="info-value"><?php echo htmlspecialchars($fam['children_count'] ?? '0'); ?> คน</div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: การศึกษา/อบรม -->
                <div class="tab-pane fade" id="tab-edu" role="tabpanel">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">ประวัติการศึกษา</h5>
                    <div class="table-responsive mb-5">
                        <table class="table custom-table table-hover border">
                            <thead>
                                <tr><th>วุฒิการศึกษา</th><th>สาขาวิชา/วิชาเอก</th><th>สถานศึกษา</th><th>ปีที่สำเร็จ</th></tr>
                            </thead>
                            <tbody>
                                <?php if(empty($edu)): ?>
                                    <tr><td colspan="4" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                <?php else: ?>
                                    <?php foreach($edu as $e): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($e['degree_level'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($e['major'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($e['institution'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($e['graduation_year'] ?? '-'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">ประวัติการฝึกอบรม/ดูงาน</h5>
                    <div class="table-responsive">
                        <table class="table custom-table table-hover border">
                            <thead>
                                <tr><th>หลักสูตร / โครงการ</th><th>หน่วยงานผู้จัด</th><th>ปี พ.ศ.</th></tr>
                            </thead>
                            <tbody>
                                <?php if(empty($trn)): ?>
                                    <tr><td colspan="3" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                <?php else: ?>
                                    <?php foreach($trn as $t): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($t['course_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($t['institution'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($t['training_year'] ?? '-'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: ประวัติการทำงาน -->
                <div class="tab-pane fade" id="tab-work" role="tabpanel">
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">ประวัติการรับราชการ / การทำงาน</h5>
                    <div class="table-responsive mb-5">
                        <table class="table custom-table table-hover border">
                            <thead>
                                <tr>
                                    <th>วัน/เดือน/ปี</th>
                                    <th>เลขคำสั่ง</th>
                                    <th>ตำแหน่ง</th>
                                    <th>ระดับ</th>
                                    <th>เงินเดือน</th>
                                    <th>สังกัด</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($wk)): ?>
                                    <tr><td colspan="6" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                <?php else: ?>
                                    <?php foreach($wk as $w): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($w['start_date'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($w['order_number'] ?? '-'); ?></td>
                                        <td class="fw-bold text-teal"><?php echo htmlspecialchars($w['position_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($w['level'] ?? '-'); ?></td>
                                        <td><?php echo number_format((float)($w['salary'] ?? 0), 2); ?></td>
                                        <td><?php echo htmlspecialchars($w['department'] ?? '-'); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-12">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">รักษาราชการแทน / รักษาการในตำแหน่ง</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>ตำแหน่ง</th><th>เลขที่คำสั่ง</th><th>วันที่เริ่ม</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($act)): ?><tr><td colspan="3" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                        <?php else: foreach($act as $a): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($a['acting_position'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($a['order_number'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($a['start_date'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">การประเมินผลปฏิบัติงาน</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>ปี พ.ศ.</th><th>รอบที่</th><th>คะแนน (ร้อยละ)</th><th>ระดับผลการประเมิน</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($ev)): ?><tr><td colspan="4" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                        <?php else: foreach($ev as $e): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($e['eval_year'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($e['eval_round'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($e['score_percent'] ?? '0'); ?>%</td>
                                                <td><span class="badge bg-success"><?php echo htmlspecialchars($e['result_level'] ?? '-'); ?></span></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: สิทธิประโยชน์/วินัย/ลา -->
                <div class="tab-pane fade" id="tab-other" role="tabpanel">
                    
                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">ประวัติการลา (แยกตามปีงบประมาณ)</h5>
                    <div class="table-responsive mb-4">
                        <table class="table custom-table table-hover border text-center">
                            <thead class="bg-light">
                                <tr><th class="text-start">ปี พ.ศ.</th><th>ลาป่วย (วัน)</th><th>ลากิจ (วัน)</th><th>ลาพักผ่อน (วัน)</th><th>มาสาย (ครั้ง)</th></tr>
                            </thead>
                            <tbody>
                                <?php if(empty($lv)): ?><tr><td colspan="5" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                <?php else: foreach($lv as $l): ?>
                                    <tr>
                                        <td class="text-start fw-bold"><?php echo htmlspecialchars($l['leave_year'] ?? '-'); ?></td>
                                        <td class="text-danger"><?php echo htmlspecialchars($l['sick_leave'] ?? '0'); ?></td>
                                        <td class="text-warning"><?php echo htmlspecialchars($l['personal_leave'] ?? '0'); ?></td>
                                        <td class="text-success"><?php echo htmlspecialchars($l['vacation_leave'] ?? '0'); ?></td>
                                        <td class="text-secondary"><?php echo htmlspecialchars($l['late_count'] ?? '0'); ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">เครื่องราชอิสริยาภรณ์</h5>
                    <div class="table-responsive mb-4">
                        <table class="table custom-table table-hover border">
                            <thead><tr><th>ชั้นเครื่องราชอิสริยาภรณ์</th><th>ปี พ.ศ.</th><th>อ้างอิงราชกิจจานุเบกษา</th></tr></thead>
                            <tbody>
                                <?php if(empty($dec)): ?><tr><td colspan="3" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                <?php else: foreach($dec as $d): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($d['decor_name'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($d['received_year'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($d['gazette_info'] ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold text-dark mb-3 border-bottom pb-2">ใบอนุญาตประกอบวิชาชีพ</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>ชื่อใบอนุญาต</th><th>เลขที่</th><th>วันออก-วันหมดอายุ</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($lic)): ?><tr><td colspan="3" class="text-center text-muted">ไม่มีข้อมูล</td></tr>
                                        <?php else: foreach($lic as $l): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($l['license_name'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($l['license_number'] ?? '-'); ?></td>
                                                <td><small class="text-muted"><?php echo htmlspecialchars(($l['issue_date'] ?? '-') . ' - ' . ($l['expiry_date'] ?? '-')); ?></small></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h5 class="fw-bold text-danger mb-3 border-bottom border-danger pb-2">ประวัติทางวินัย</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border border-danger border-opacity-25">
                                    <thead><tr><th>วันที่</th><th>ประเภทโทษ</th><th>รายละเอียด</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($dis)): ?><tr><td colspan="3" class="text-center text-success"><i class="fa-solid fa-circle-check me-1"></i> ไม่มีประวัติการกระทำผิดวินัย</td></tr>
                                        <?php else: foreach($dis as $d): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($d['incident_date'] ?? '-'); ?></td>
                                                <td class="text-danger fw-bold"><?php echo htmlspecialchars($d['punishment_type'] ?? '-'); ?></td>
                                                <td><?php echo htmlspecialchars($d['description'] ?? '-'); ?></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>