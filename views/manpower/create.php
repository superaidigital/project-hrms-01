<?php
// ==========================================
// ชื่อไฟล์: create.php
// ที่อยู่ไฟล์: views/manpower/create.php
// ==========================================

// 🚀 ระบบป้องกันการเข้าไฟล์โดยตรง (Auto-Redirect)
if (strpos($_SERVER['REQUEST_URI'], 'views/manpower/create.php') !== false) {
    header("Location: ../../index.php?action=manpower_create");
    exit();
}

include 'views/layout/header.php'; 

// 🛡️ ฟังก์ชันช่วยเหลือสำหรับป้องกัน XSS
if (!function_exists('h')) {
    function h($string) { return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8'); }
}
?>

<div class="container-fluid py-4 mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0 fw-bold text-dark">
                        <i class="fas fa-plus-circle text-primary me-2"></i> เพิ่มตำแหน่งในกรอบอัตรากำลัง
                    </h4>
                    <a href="index.php?action=manpower" class="btn btn-light border shadow-sm fw-semibold text-secondary">
                        <i class="fas fa-arrow-left me-1"></i> กลับหน้ารวม
                    </a>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= h($_SESSION['message_type']) ?> alert-dismissible fade show shadow-sm" role="alert">
                            <?= h($_SESSION['message']) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <form action="index.php?action=manpower_store" method="POST">
                        
                        <h6 class="text-teal fw-bold mb-3 pb-2 border-bottom"><i class="fas fa-sitemap mr-2"></i> ข้อมูลตำแหน่ง</h6>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">ประเภทบุคลากร <span class="text-danger">*</span></label>
                                <select name="employee_type" class="form-select bg-light" required>
                                    <option value="">-- เลือกประเภท --</option>
                                    <option value="ข้าราชการ อบจ.">ข้าราชการ อบจ.</option>
                                    <option value="ข้าราชการครู อบจ.">ข้าราชการครู อบจ.</option>
                                    <option value="ลูกจ้างประจำ">ลูกจ้างประจำ</option>
                                    <option value="พนักงานจ้างตามภารกิจ">พนักงานจ้างตามภารกิจ</option>
                                    <option value="พนักงานจ้างทั่วไป">พนักงานจ้างทั่วไป</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">เลขที่ตำแหน่ง <span class="text-danger">*</span></label>
                                <input type="text" name="position_number" class="form-control bg-light" required placeholder="เช่น 01-1-001">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">ชื่อสายงาน/ตำแหน่ง <span class="text-danger">*</span></label>
                                <input type="text" name="position_name" class="form-control bg-light" required placeholder="เช่น นักทรัพยากรบุคคล">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                                <select name="level" class="form-select bg-light" required>
                                    <option value="">-- เลือกระดับ --</option>
                                    <?php if(!empty($position_levels)): foreach($position_levels as $lvl): ?>
                                        <option value="<?= h($lvl['name']) ?>"><?= h($lvl['name']) ?> <?= !empty($lvl['type']) ? "(".h($lvl['type']).")" : "" ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>

                        <h6 class="text-teal fw-bold mb-3 pb-2 border-bottom mt-5"><i class="fas fa-building mr-2"></i> สังกัดและสถานะ</h6>

                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">สังกัดสำนัก/กอง/รพ.สต. <span class="text-danger">*</span></label>
                                <select name="department" class="form-select bg-light" required>
                                    <option value="">-- เลือกส่วนราชการ --</option>
                                    <?php if(!empty($departments)): foreach($departments as $dept): ?>
                                        <option value="<?= h($dept['name']) ?>"><?= h($dept['name']) ?></option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">ฝ่าย/ส่วน/งาน (ถ้ามี)</label>
                                <input type="text" name="division" class="form-control bg-light" placeholder="เช่น ฝ่ายบริหารงานทั่วไป">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">เงินเดือนที่ตั้งไว้ (บาท)</label>
                                <input type="number" step="0.01" name="salary" class="form-control bg-light" placeholder="0.00" value="0.00">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-muted small">สถานะเริ่มต้น <span class="text-danger">*</span></label>
                                <select name="status" class="form-select border-success text-success fw-bold" style="background-color: #f0fdf4;">
                                    <option value="vacant" selected>ว่าง (รอสรรหา)</option>
                                    <option value="occupied">มีคนครอง</option>
                                </select>
                                <small class="text-muted mt-1 d-block">ระบบแนะนำให้ตั้งเป็น "ว่าง" เพื่อให้ไปผูกประวัติคนทีหลังได้</small>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-bold text-muted small">หมายเหตุ</label>
                                <textarea name="remark" class="form-control bg-light" rows="3" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                            </div>
                        </div>

                        <!-- ปุ่มบันทึกข้อมูล -->
                        <div class="mt-5 text-end">
                            <a href="index.php?action=manpower" class="btn btn-outline-secondary px-4 fw-bold me-2">ยกเลิก</a>
                            <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-1"></i> บันทึกข้อมูลตำแหน่ง
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>