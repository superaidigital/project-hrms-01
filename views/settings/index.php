<?php include 'views/layout/header.php'; ?>

<style>
    .setting-card {
        transition: all 0.3s ease;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: white;
    }
    .setting-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(13, 148, 136, 0.2);
        border-color: #14b8a6;
    }
    .icon-box {
        width: 60px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 16px;
        font-size: 24px;
    }
</style>

<div class="mb-4">
    <h2 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-gear text-teal me-2" style="color: #0d9488;"></i> ตั้งค่าระบบ (System Settings)</h2>
    <p class="text-muted mt-1">จัดการข้อมูลพื้นฐาน สิทธิ์การใช้งาน และการบำรุงรักษาระบบ</p>
</div>

<!-- หมวดหมู่ที่ 1: การจัดการข้อมูลพื้นฐาน (Master Data) -->
<h5 class="fw-bold text-dark border-bottom pb-2 mb-4 mt-5">ข้อมูลพื้นฐาน (Master Data)</h5>
<div class="row g-4 mb-4">
    <!-- หน่วยงานในสังกัด -->
    <div class="col-md-4">
        <a href="index.php?action=departments" class="text-decoration-none">
            <div class="setting-card p-4 h-100 d-flex flex-column align-items-center text-center">
                <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                    <i class="fa-regular fa-building"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">หน่วยงาน / ส่วนราชการ</h5>
                <p class="text-muted small mb-0">เพิ่ม/ลด/แก้ไข รายชื่อกอง, สถานศึกษา, รพ.สต.</p>
            </div>
        </a>
    </div>

    <!-- ระดับตำแหน่ง -->
    <div class="col-md-4">
        <a href="index.php?action=position_levels" class="text-decoration-none">
            <div class="setting-card p-4 h-100 d-flex flex-column align-items-center text-center">
                <div class="icon-box bg-info bg-opacity-10 text-info mb-3">
                    <i class="fa-solid fa-layer-group"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">ระดับตำแหน่ง (อปท.)</h5>
                <p class="text-muted small mb-0">จัดการสายงานและระดับ (ปฏิบัติการ, ชำนาญการ ฯลฯ)</p>
            </div>
        </a>
    </div>
    
    <!-- จัดการเมนู -->
    <div class="col-md-4">
        <a href="index.php?action=menus" class="text-decoration-none">
            <div class="setting-card p-4 h-100 d-flex flex-column align-items-center text-center">
                <div class="icon-box bg-warning bg-opacity-10 text-warning mb-3">
                    <i class="fa-solid fa-bars-staggered"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">จัดการเมนูระบบ</h5>
                <p class="text-muted small mb-0">ตั้งค่าการแสดงผลเมนู Sidebar (เปิด/ปิดเมนู)</p>
            </div>
        </a>
    </div>
</div>

<!-- หมวดหมู่ที่ 2: ความปลอดภัยและผู้ใช้งาน -->
<h5 class="fw-bold text-dark border-bottom pb-2 mb-4 mt-5">ความปลอดภัยและผู้ใช้งาน (Security & Users)</h5>
<div class="row g-4 mb-4">
    <!-- จัดการผู้ใช้และสิทธิ์ -->
    <div class="col-md-4">
        <a href="index.php?action=users" class="text-decoration-none">
            <div class="setting-card p-4 h-100 d-flex flex-column align-items-center text-center">
                <div class="icon-box bg-success bg-opacity-10 text-success mb-3">
                    <i class="fa-solid fa-user-shield"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">ผู้ใช้งานและสิทธิ์ (Roles)</h5>
                <p class="text-muted small mb-0">จัดการรหัสผ่าน รีเซ็ต และกำหนดสิทธิ์ (Admin/HR/User)</p>
            </div>
        </a>
    </div>
</div>

<!-- หมวดหมู่ที่ 3: ระบบ (System & Maintenance) -->
<h5 class="fw-bold text-dark border-bottom pb-2 mb-4 mt-5">บำรุงรักษาระบบ (System Maintenance)</h5>
<div class="row g-4 mb-5">
    <!-- สำรองข้อมูล (Backup) - ใช้งานได้จริง -->
    <div class="col-md-4">
        <div class="setting-card p-4 h-100 d-flex flex-column align-items-center text-center">
            <div class="icon-box bg-teal bg-opacity-10 text-teal mb-3" style="color: #0d9488;">
                <i class="fa-solid fa-database"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">สำรองฐานข้อมูล (Backup)</h5>
            <p class="text-muted small mb-3">ดาวน์โหลดข้อมูลทั้งหมดเก็บไว้เพื่อป้องกันข้อมูลสูญหาย</p>
            <a href="index.php?action=settings_backup" class="btn btn-outline-teal w-100 fw-bold mt-auto" style="color: #0d9488; border-color: #0d9488;" onclick="return confirm('ระบบกำลังจะสร้างไฟล์ .sql เพื่อดาวน์โหลด ดำเนินการต่อหรือไม่?');">
                <i class="fa-solid fa-cloud-arrow-down me-1"></i> ดาวน์โหลด (.sql)
            </a>
        </div>
    </div>

    <!-- เปิด/ปิด ระบบ (Maintenance) - ใช้งานได้จริง -->
    <div class="col-md-4">
        <div class="setting-card p-4 h-100 d-flex flex-column align-items-center text-center border-<?php echo $maintenance_mode == 'on' ? 'danger' : 'light'; ?>">
            <div class="icon-box bg-<?php echo $maintenance_mode == 'on' ? 'danger' : 'secondary'; ?> bg-opacity-10 text-<?php echo $maintenance_mode == 'on' ? 'danger' : 'secondary'; ?> mb-3">
                <i class="fa-solid fa-power-off"></i>
            </div>
            <h5 class="fw-bold text-dark mb-1">สถานะระบบปัจจุบัน</h5>
            <p class="text-<?php echo $maintenance_mode == 'on' ? 'danger fw-bold' : 'success fw-bold'; ?> small mb-3">
                <i class="fa-solid fa-circle text-<?php echo $maintenance_mode == 'on' ? 'danger' : 'success'; ?> me-1" style="font-size: 8px;"></i> 
                <?php echo $maintenance_mode == 'on' ? 'ปิดปรับปรุง (Maintenance)' : 'เปิดใช้งานปกติ (Online)'; ?>
            </p>
            
            <form action="index.php?action=settings_toggle_maintenance" method="POST" class="w-100 mt-auto">
                <input type="hidden" name="maintenance_status" value="<?php echo $maintenance_mode == 'on' ? 'off' : 'on'; ?>">
                <?php if($maintenance_mode == 'on'): ?>
                    <button type="submit" class="btn btn-success w-100 fw-bold" onclick="return confirm('ยืนยันการเปิดระบบให้ผู้ใช้ทั่วไปเข้าใช้งาน?');">เปิดระบบ</button>
                <?php else: ?>
                    <button type="submit" class="btn btn-outline-danger w-100 fw-bold" onclick="return confirm('คำเตือน: หากปิดระบบ ผู้ใช้ที่ไม่ใช่ Admin จะถูกเตะออกจากระบบทันที ยืนยันหรือไม่?');">ปิดปรับปรุงระบบ</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>