<?php
// ==========================================
// ชื่อไฟล์: print_kp7.php
// ที่อยู่ไฟล์: views/report/print_kp7.php
// ==========================================

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

$currentWork = !empty($wk) ? end($wk) : [];
$currentPosition = $currentWork['position_name'] ?? 'ยังไม่ระบุตำแหน่ง';
$currentLevel = $currentWork['level'] ?? '-';
$currentDept = $currentWork['department'] ?? 'ยังไม่ระบุสังกัด';

$firstWork = !empty($wk) ? reset($wk) : [];
$employmentDate = $firstWork['start_date'] ?? '-';

// ใช้ ?? เพื่อป้องกัน Error Undefined array key "employee_type"
$employeeType = $empData['employee_type'] ?? '- ไม่ระบุ -';

$age = '-';
if(!empty($empData['dob'])) {
    $parts = explode('/', $empData['dob']);
    if(count($parts) == 3) {
        $yearBE = (int)$parts[2];
        $currentYearBE = (int)date('Y') + 543;
        $age = ($currentYearBE - $yearBE) . " ปี";
    }
}
?>
<style>
    body { font-family: 'garuda', sans-serif; font-size: 11pt; line-height: 1.4; color: #000; }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .fw-bold { font-weight: bold; }
    .header-title { font-size: 18pt; font-weight: bold; margin-bottom: 5px; }
    .sub-header { font-size: 14pt; margin-bottom: 15px; }
    .section-title { font-size: 12pt; font-weight: bold; margin-top: 20px; margin-bottom: 8px; background-color: #e2e8f0; padding: 5px 10px; border-left: 5px solid #0f766e; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th, td { border: 1px solid #334155; padding: 6px 8px; text-align: left; vertical-align: top; }
    th { background-color: #f1f5f9; text-align: center; font-weight: bold; }
    .no-border, .no-border th, .no-border td { border: none !important; padding: 4px; }
</style>

<table class="no-border" style="margin-bottom: 10px;">
    <tr>
        <td width="80%" style="vertical-align: top;">
            <div class="header-title">ก.พ. ๗</div>
            <div class="sub-header">สมุดประวัติข้าราชการ/พนักงานส่วนท้องถิ่น</div>
            <table class="no-border">
                <tr><td width="30%" class="fw-bold">เลขประจำตัวประชาชน:</td><td><?php echo $empData['national_id'] ?? '-'; ?></td></tr>
                <tr><td class="fw-bold">เลขประจำตำแหน่ง:</td><td><?php echo $empData['emp_code'] ?? '-'; ?></td></tr>
                <tr><td class="fw-bold">ชื่อ - นามสกุล:</td><td><?php echo ($empData['prefix'] ?? '') . ($empData['first_name'] ?? '') . ' ' . ($empData['last_name'] ?? ''); ?></td></tr>
            </table>
        </td>
        <td width="20%" align="right" style="vertical-align: top;">
            <?php if(!empty($empData['avatar']) && file_exists("uploads/" . $empData['avatar'])): ?>
                <img src="uploads/<?php echo $empData['avatar']; ?>" style="width: 3.5cm; height: 4.5cm; border: 1px solid #000; object-fit: cover;">
            <?php else: ?>
                <div style="width: 3.5cm; height: 4.5cm; border: 1px solid #000; text-align: center; line-height: 4.5cm; font-size: 10pt; color: #666;">ติดรูปถ่าย</div>
            <?php endif; ?>
        </td>
    </tr>
</table>

<div class="section-title">๑. ข้อมูลส่วนบุคคลและครอบครัว</div>
<table class="no-border">
    <tr>
        <td width="15%" class="fw-bold">วันเกิด (พ.ศ.):</td><td width="35%"><?php echo !empty($empData['dob']) ? $empData['dob'] : '-'; ?> (อายุ <?php echo $age; ?>)</td>
        <td width="15%" class="fw-bold">เพศ:</td><td width="35%"><?php echo $empData['gender'] ?? '-'; ?></td>
    </tr>
    <tr>
        <td class="fw-bold">เบอร์ติดต่อ:</td><td><?php echo !empty($empData['phone']) ? $empData['phone'] : '-'; ?></td>
        <td class="fw-bold">อีเมล:</td><td><?php echo !empty($empData['email']) ? $empData['email'] : '-'; ?></td>
    </tr>
    <tr>
        <td class="fw-bold">ชื่อบิดา:</td><td><?php echo $fam['father_name'] ?? '-'; ?></td>
        <td class="fw-bold">ชื่อมารดา:</td><td><?php echo $fam['mother_name'] ?? '-'; ?></td>
    </tr>
    <tr>
        <td class="fw-bold">คู่สมรส:</td><td><?php echo $fam['spouse_name'] ?? '-'; ?></td>
        <td class="fw-bold">จำนวนบุตร:</td><td><?php echo isset($fam['children_count']) ? $fam['children_count'] . ' คน' : '-'; ?></td>
    </tr>
</table>

<div class="section-title">๒. ข้อมูลการปฏิบัติราชการปัจจุบัน</div>
<table class="no-border">
    <tr>
        <td width="20%" class="fw-bold">ประเภทบุคลากร:</td>
        <td width="80%"><?php echo htmlspecialchars($employeeType); ?></td>
    </tr>
    <tr>
        <td class="fw-bold">สังกัด / หน่วยงาน:</td>
        <td><?php echo htmlspecialchars($currentDept); ?></td>
    </tr>
    <tr>
        <td class="fw-bold">ตำแหน่ง / ระดับ:</td>
        <td><?php echo htmlspecialchars($currentPosition); ?> <?php echo $currentLevel !== '-' ? "ระดับ " . htmlspecialchars($currentLevel) : ''; ?></td>
    </tr>
    <tr>
        <td class="fw-bold">วันที่บรรจุ:</td>
        <td><?php echo htmlspecialchars($employmentDate); ?></td>
    </tr>
</table>

<div class="section-title">๓. ประวัติการดำรงตำแหน่งและรับเงินเดือน</div>
<table>
    <thead>
        <tr><th width="15%">ว/ด/ป</th><th width="15%">เลขคำสั่ง</th><th width="35%">ตำแหน่ง / สังกัด / หน่วยงาน</th><th width="15%">ระดับ</th><th width="20%">เงินเดือน (บาท)</th></tr>
    </thead>
    <tbody>
        <?php if(empty($wk)): ?><tr><td colspan="5" class="text-center">- ไม่มีข้อมูล -</td></tr>
        <?php else: foreach($wk as $w): ?>
            <tr>
                <td class="text-center"><?php echo $w['start_date'] ?? '-'; ?></td>
                <td class="text-center"><?php echo $w['order_number'] ?? '-'; ?></td>
                <td>
                    <b><?php echo $w['position_name'] ?? '-'; ?></b><br>
                    <span style="font-size: 10pt; color: #475569;">สังกัด: <?php echo $w['department'] ?? '-'; ?><br>หน่วยงาน: <?php echo $w['agency'] ?? 'อบจ.ศรีสะเกษ'; ?></span>
                </td>
                <td class="text-center"><?php echo $w['level'] ?? '-'; ?></td>
                <td class="text-right"><?php echo !empty($w['salary']) ? number_format((float)$w['salary'], 2) : '-'; ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>

<pagebreak />
<div class="section-title">๔. ประวัติการศึกษาและการฝึกอบรม</div>
<table style="margin-bottom: 5px;">
    <thead><tr><th width="25%">ระดับการศึกษา</th><th width="35%">สาขาวิชา / วิชาเอก</th><th width="30%">สถานศึกษา</th><th width="10%">ปีที่สำเร็จ</th></tr></thead>
    <tbody>
        <?php if(empty($edu)): ?><tr><td colspan="4" class="text-center">- ไม่มีข้อมูลประวัติการศึกษา -</td></tr>
        <?php else: foreach($edu as $e): ?><tr><td><?php echo $e['degree_level'] ?? '-'; ?></td><td><?php echo $e['major'] ?? '-'; ?></td><td><?php echo $e['institution'] ?? '-'; ?></td><td class="text-center"><?php echo $e['graduation_year'] ?? '-'; ?></td></tr><?php endforeach; endif; ?>
    </tbody>
</table>

<table>
    <thead>
        <tr>
            <th width="45%">หลักสูตรการฝึกอบรม / ดูงาน</th>
            <th width="40%">หน่วยงานที่จัด</th>
            <th width="15%">ปี พ.ศ.</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($trn)): ?><tr><td colspan="3" class="text-center">- ไม่มีข้อมูลประวัติการฝึกอบรม -</td></tr>
        <?php else: foreach($trn as $t): ?>
            <tr>
                <td><?php echo isset($t['course_name']) ? $t['course_name'] : '-'; ?></td>
                <td><?php echo isset($t['institution']) ? $t['institution'] : '-'; ?></td>
                <td class="text-center"><?php echo isset($t['training_year']) ? $t['training_year'] : '-'; ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>

<!-- 5. เครื่องราชอิสริยาภรณ์ และ ประวัติทางวินัย -->
<table class="no-border" width="100%">
    <tr>
        <td width="50%" style="padding-right: 10px;">
            <div class="section-title">๕. เครื่องราชอิสริยาภรณ์</div>
            <table>
                <thead><tr><th>ชั้นเครื่องราชฯ</th><th>ปี พ.ศ.</th></tr></thead>
                <tbody>
                    <?php if(empty($dec)): ?><tr><td colspan="2" class="text-center">- ไม่มีข้อมูล -</td></tr>
                    <?php else: foreach($dec as $d): ?>
                        <tr>
                            <td><?php echo isset($d['decor_name']) ? $d['decor_name'] : '-'; ?></td>
                            <td class="text-center"><?php echo isset($d['received_year']) ? $d['received_year'] : '-'; ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </td>
        <td width="50%" style="padding-left: 10px;">
            <div class="section-title" style="border-left-color: #dc2626;">๖. ประวัติทางวินัย</div>
            <table>
                <thead><tr><th>วันที่</th><th>ประเภทโทษ</th></tr></thead>
                <tbody>
                    <?php if(empty($dis)): ?><tr><td colspan="2" class="text-center">- ไม่มีประวัติทางวินัย -</td></tr>
                    <?php else: foreach($dis as $d): ?>
                        <tr>
                            <td><?php echo isset($d['incident_date']) ? $d['incident_date'] : '-'; ?></td>
                            <td><?php echo isset($d['punishment_type']) ? $d['punishment_type'] : '-'; ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </td>
    </tr>
</table>

<!-- 7. ประวัติการลา -->
<div class="section-title">๗. ประวัติการลา (รายปีงบประมาณ)</div>
<table>
    <thead>
        <tr>
            <th>ปี พ.ศ.</th>
            <th>ลาป่วย (วัน)</th>
            <th>ลากิจ (วัน)</th>
            <th>ลาพักผ่อน (วัน)</th>
            <th>มาสาย (ครั้ง)</th>
        </tr>
    </thead>
    <tbody>
        <?php if(empty($lv)): ?><tr><td colspan="5" class="text-center">- ไม่มีข้อมูล -</td></tr>
        <?php else: foreach($lv as $l): ?>
            <tr class="text-center">
                <td class="fw-bold"><?php echo isset($l['leave_year']) ? $l['leave_year'] : '-'; ?></td>
                <td><?php echo isset($l['sick_leave']) ? $l['sick_leave'] : '-'; ?></td>
                <td><?php echo isset($l['personal_leave']) ? $l['personal_leave'] : '-'; ?></td>
                <td><?php echo isset($l['vacation_leave']) ? $l['vacation_leave'] : '-'; ?></td>
                <td><?php echo isset($l['late_count']) ? $l['late_count'] : '-'; ?></td>
            </tr>
        <?php endforeach; endif; ?>
    </tbody>
</table>

<br><br><br>
<table class="no-border">
    <tr>
        <td class="text-center" width="50%">
            (.......................................................)<br>
            เจ้าของประวัติ<br>
            วันที่ ........ / .................... / ............
        </td>
        <td class="text-center" width="50%">
            (.......................................................)<br>
            เจ้าหน้าที่ผู้บันทึก / นายกองค์การบริหารส่วนจังหวัด<br>
            วันที่ ........ / .................... / ............
        </td>
    </tr>
</table>