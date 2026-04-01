<!-- 
==========================================
ชื่อไฟล์: show.php
ที่อยู่ไฟล์: views/employee/show.php
==========================================
-->
<?php include 'views/layout/header.php'; ?>

<?php 
// 🛡️ [ป้องกัน XSS]: ฟังก์ชันตัวช่วยสำหรับครอบ htmlspecialchars ช่วยให้โค้ดอ่านง่ายขึ้น
if (!function_exists('h')) {
    function h($string) {
        return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

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
    .tab-content { padding-top: 25px; }
    .custom-table th { background-color: #f8fafc; color: #475569; font-weight: 600; }
    .profile-header { background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%); color: white; border-radius: 1rem 1rem 0 0; padding: 2rem; }
    .profile-avatar { width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    .avatar-placeholder { width: 120px; height: 120px; border-radius: 50%; background-color: #e2e8f0; color: #64748b; display: flex; align-items: center; justify-content: center; font-size: 3rem; font-weight: bold; border: 4px solid white; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
</style>

<div class="container-fluid py-4">
    <!-- Header Controls -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-dark mb-0"><i class="fa-solid fa-address-card text-primary me-2"></i> ก.พ. 7 - ประวัติข้าราชการ/พนักงาน</h4>
        <div class="btn-group shadow-sm">
            <a href="index.php?action=employees" class="btn btn-light border"><i class="fa-solid fa-arrow-left"></i> กลับหน้ารวม</a>
            <a href="index.php?action=edit&id=<?= h($empData['id']) ?>" class="btn btn-warning text-dark"><i class="fa-solid fa-pen-to-square"></i> แก้ไข</a>
            <a href="index.php?action=print_kp7&id=<?= h($empData['id']) ?>" target="_blank" class="btn btn-primary"><i class="fa-solid fa-print"></i> พิมพ์ ก.พ. 7</a>
        </div>
    </div>

    <!-- Profile Summary Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="profile-header d-flex flex-column flex-md-row align-items-center gap-4">
            <div>
                <?php if(!empty($empData['avatar']) && file_exists("uploads/" . $empData['avatar'])): ?>
                    <img src="uploads/<?= h($empData['avatar']) ?>" alt="Profile" class="profile-avatar">
                <?php else: ?>
                    <div class="avatar-placeholder"><?= h(mb_substr($empData['first_name'], 0, 1, 'UTF-8')) ?></div>
                <?php endif; ?>
            </div>
            <div class="text-center text-md-start">
                <h2 class="fw-bold mb-1"><?= h($empData['prefix'] . $empData['first_name'] . ' ' . $empData['last_name']) ?></h2>
                <h5 class="mb-2 opacity-75"><?= h($currentPosition) ?> <?= h($currentLevel) ?></h5>
                <div class="d-flex flex-wrap gap-3 justify-content-center justify-content-md-start mt-3">
                    <span class="badge bg-white text-teal px-3 py-2 rounded-pill shadow-sm"><i class="fa-solid fa-building me-1"></i> <?= h($currentDept) ?></span>
                    <span class="badge bg-white text-teal px-3 py-2 rounded-pill shadow-sm"><i class="fa-solid fa-id-badge me-1"></i> รหัส: <?= h($empData['emp_code'] ?: '-') ?></span>
                    <span class="badge bg-white text-teal px-3 py-2 rounded-pill shadow-sm"><i class="fa-solid fa-id-card me-1"></i> ปชช: <?= h($empData['national_id'] ?: '-') ?></span>
                    <span class="badge <?= $empData['status'] == 'ปฏิบัติงาน' ? 'bg-success' : 'bg-secondary' ?> text-white px-3 py-2 rounded-pill shadow-sm">
                        สถานะ: <?= h($empData['status']) ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Detail Tabs -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <ul class="nav nav-tabs" id="kp7Tabs" role="tablist">
                <li class="nav-item" role="presentation"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-personal" type="button" role="tab"><i class="fa-solid fa-user me-1"></i> ข้อมูลส่วนตัว & ครอบครัว</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-work" type="button" role="tab"><i class="fa-solid fa-briefcase me-1"></i> ประวัติการทำงาน</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-edu" type="button" role="tab"><i class="fa-solid fa-graduation-cap me-1"></i> การศึกษา & อบรม</button></li>
                <li class="nav-item" role="presentation"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-other" type="button" role="tab"><i class="fa-solid fa-award me-1"></i> เครื่องราชฯ & วินัย & ลา</button></li>
            </ul>

            <div class="tab-content" id="kp7TabsContent">
                
                <!-- Tab 1: Personal & Family -->
                <div class="tab-pane fade show active" id="tab-personal" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-address-book"></i> ข้อมูลติดต่อ</h5>
                            <table class="table table-borderless table-sm">
                                <tr><td width="150" class="text-muted">วัน/เดือน/ปีเกิด:</td><td class="fw-semibold"><?= h($empData['dob']) ?></td></tr>
                                <tr><td class="text-muted">เพศ:</td><td class="fw-semibold"><?= h($empData['gender']) ?></td></tr>
                                <tr><td class="text-muted">เบอร์โทรศัพท์:</td><td class="fw-semibold"><?= h($empData['phone'] ?: '-') ?></td></tr>
                                <tr><td class="text-muted">อีเมล:</td><td class="fw-semibold"><?= h($empData['email'] ?: '-') ?></td></tr>
                                <tr><td class="text-muted">ประเภทบุคลากร:</td><td class="fw-semibold text-info"><?= h($empData['employee_type'] ?: '-') ?></td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-users"></i> ข้อมูลครอบครัว</h5>
                            <table class="table table-borderless table-sm">
                                <tr><td width="150" class="text-muted">ชื่อบิดา:</td><td class="fw-semibold"><?= h($fam['father_name'] ?? '-') ?></td></tr>
                                <tr><td class="text-muted">ชื่อมารดา:</td><td class="fw-semibold"><?= h($fam['mother_name'] ?? '-') ?></td></tr>
                                <tr><td class="text-muted">ชื่อคู่สมรส:</td><td class="fw-semibold"><?= h($fam['spouse_name'] ?? '-') ?></td></tr>
                                <tr><td class="text-muted">จำนวนบุตร:</td><td class="fw-semibold"><?= h($fam['children_count'] ?? '0') ?> คน</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Work History & Acting -->
                <div class="tab-pane fade" id="tab-work" role="tabpanel">
                    <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-clock-rotate-left"></i> ประวัติการรับราชการ / การทำงาน</h5>
                    <div class="table-responsive mb-4">
                        <table class="table custom-table table-hover border">
                            <thead><tr><th>วัน/เดือน/ปี</th><th>เลขที่คำสั่ง</th><th>ตำแหน่ง</th><th>ระดับ</th><th>ส่วนราชการ</th><th>เงินเดือน</th></tr></thead>
                            <tbody>
                                <?php if(empty($wk)): ?><tr><td colspan="6" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                <?php else: foreach($wk as $w): ?>
                                    <tr>
                                        <td><?= h($w['start_date']) ?></td>
                                        <td><?= h($w['order_number']) ?></td>
                                        <td class="fw-semibold text-dark"><?= h($w['position_name']) ?> <small class="text-muted">(เลขที่ <?= h($w['position_number']) ?>)</small></td>
                                        <td><?= h($w['level']) ?></td>
                                        <td><?= h($w['department']) ?></td>
                                        <td><?= number_format((float)(h($w['salary'] ?? 0)), 2) ?></td>
                                    </tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary mt-4"><i class="fa-solid fa-user-tie"></i> ประวัติการรักษาการในตำแหน่ง</h5>
                    <div class="table-responsive mb-4">
                        <table class="table custom-table table-hover border">
                            <thead><tr><th>ตำแหน่งที่รักษาการ</th><th>เลขที่คำสั่ง</th><th>วันที่เริ่ม</th></tr></thead>
                            <tbody>
                                <?php if(empty($act)): ?><tr><td colspan="3" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                <?php else: foreach($act as $a): ?>
                                    <tr><td><?= h($a['acting_position']) ?></td><td><?= h($a['order_number']) ?></td><td><?= h($a['start_date']) ?></td></tr>
                                <?php endforeach; endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: Education & Training -->
                <div class="tab-pane fade" id="tab-edu" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-12">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-graduation-cap"></i> ประวัติการศึกษา</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>วุฒิการศึกษา</th><th>สาขาวิชาเอก</th><th>สถานศึกษา</th><th>ปีที่จบ</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($edu)): ?><tr><td colspan="4" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                        <?php else: foreach($edu as $e): ?>
                                            <tr><td><?= h($e['degree_level']) ?></td><td><?= h($e['major']) ?></td><td><?= h($e['institution']) ?></td><td><?= h($e['graduation_year']) ?></td></tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-chalkboard-user"></i> ประวัติการฝึกอบรม/ดูงาน</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>หลักสูตร</th><th>หน่วยงานที่จัด</th><th>ปีที่อบรม</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($trn)): ?><tr><td colspan="3" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                        <?php else: foreach($trn as $t): ?>
                                            <tr><td><?= h($t['course_name']) ?></td><td><?= h($t['institution']) ?></td><td><?= h($t['training_year']) ?></td></tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-12 mt-2">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-id-card-clip"></i> ใบอนุญาตประกอบวิชาชีพ</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>ชื่อใบอนุญาต</th><th>เลขที่</th><th>วันออกบัตร</th><th>วันหมดอายุ</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($lic)): ?><tr><td colspan="4" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                        <?php else: foreach($lic as $l): ?>
                                            <tr><td><?= h($l['license_name']) ?></td><td><?= h($l['license_number']) ?></td><td><?= h($l['issue_date']) ?></td><td><?= h($l['expiry_date']) ?></td></tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 4: Other (Decorations, Leave, Disciplinary, Evaluation) -->
                <div class="tab-pane fade" id="tab-other" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-warning"><i class="fa-solid fa-medal"></i> ประวัติเครื่องราชอิสริยาภรณ์</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>ชื่อเครื่องราชฯ</th><th>ปีที่ได้รับ</th><th>ราชกิจจาฯ</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($dec)): ?><tr><td colspan="3" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                        <?php else: foreach($dec as $d): ?>
                                            <tr><td><?= h($d['decor_name']) ?></td><td><?= h($d['received_year']) ?></td><td><?= h($d['gazette_info']) ?></td></tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-solid fa-star"></i> ประวัติการประเมินผล</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>ปีงบประมาณ</th><th>รอบประเมิน</th><th>คะแนน (%)</th><th>ระดับผลการประเมิน</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($ev)): ?><tr><td colspan="4" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                        <?php else: foreach($ev as $e): ?>
                                            <tr><td><?= h($e['eval_year']) ?></td><td><?= h($e['eval_round']) ?></td><td><?= h($e['score_percent']) ?></td><td><span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25"><?= h($e['result_level']) ?></span></td></tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-primary"><i class="fa-regular fa-calendar-xmark"></i> ประวัติการลา</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border">
                                    <thead><tr><th>ปีงบประมาณ</th><th class="text-center">ลาป่วย (วัน)</th><th class="text-center">ลากิจ (วัน)</th><th class="text-center">ลาพักผ่อน (วัน)</th><th class="text-center">มาสาย (ครั้ง)</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($lv)): ?><tr><td colspan="5" class="text-center text-muted">ไม่มีประวัติข้อมูล</td></tr>
                                        <?php else: foreach($lv as $l): ?>
                                            <tr>
                                                <td><?= h($l['leave_year']) ?></td>
                                                <td class="text-center"><?= h($l['sick_leave']) ?></td>
                                                <td class="text-center"><?= h($l['personal_leave']) ?></td>
                                                <td class="text-center"><?= h($l['vacation_leave']) ?></td>
                                                <td class="text-center text-warning fw-bold"><?= h($l['late_count']) ?></td>
                                            </tr>
                                        <?php endforeach; endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <h5 class="fw-bold border-bottom pb-2 mb-3 text-danger"><i class="fa-solid fa-gavel"></i> ประวัติการกระทำผิดทางวินัย</h5>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover border border-danger border-opacity-25">
                                    <thead class="table-danger border-danger border-opacity-25"><tr><th>วันที่</th><th>ประเภทโทษ</th><th>รายละเอียด</th></tr></thead>
                                    <tbody>
                                        <?php if(empty($dis)): ?><tr><td colspan="3" class="text-center text-success"><i class="fa-solid fa-circle-check me-1"></i> ไม่มีประวัติการกระทำผิดวินัย</td></tr>
                                        <?php else: foreach($dis as $d): ?>
                                            <tr>
                                                <td><?= h($d['incident_date']) ?></td>
                                                <td class="text-danger fw-bold"><?= h($d['punishment_type']) ?></td>
                                                <td><?= h($d['description']) ?></td>
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