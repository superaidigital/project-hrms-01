<?php include 'views/layout/header.php'; ?>

<div class="mb-4">
    <a href="index.php?action=manpower" class="btn btn-light border shadow-sm fw-semibold text-secondary mb-3">
        <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าภาพรวม
    </a>
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark"><i class="fa-solid fa-building text-teal me-2" style="color: #0d9488;"></i> <?php echo htmlspecialchars($dept ?? 'ไม่ระบุ'); ?></h2>
            <p class="text-muted">ทะเบียนคุมอัตรากำลังบุคลากร</p>
        </div>
        <a href="index.php?action=manpower_create" class="btn text-white fw-bold shadow-sm" style="background-color: #10b981;">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มตำแหน่งใหม่
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="modern-card border-primary border-start border-5 shadow-sm py-3">
            <h6 class="text-muted fw-bold small">กรอบทั้งหมด</h6>
            <h3 class="text-primary fw-bold mb-0"><?php echo $deptTotal ?? 0; ?> <span class="fs-6 text-muted fw-normal">อัตรา</span></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="modern-card border-success border-start border-5 shadow-sm py-3">
            <h6 class="text-muted fw-bold small">คนครอง</h6>
            <h3 class="text-success fw-bold mb-0"><?php echo $deptOccupied ?? 0; ?> <span class="fs-6 text-muted fw-normal">คน</span></h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="modern-card border-danger border-start border-5 shadow-sm py-3">
            <h6 class="text-muted fw-bold small">อัตราว่าง</h6>
            <h3 class="text-danger fw-bold mb-0"><?php echo $deptVacant ?? 0; ?> <span class="fs-6 text-muted fw-normal">อัตรา</span></h3>
        </div>
    </div>
</div>

<div class="modern-card border-0 shadow-sm p-0 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light text-muted">
                <tr>
                    <th class="ps-4">เลขที่ตำแหน่ง</th>
                    <th>ประเภท</th>
                    <th>ฝ่าย/ส่วน</th>
                    <th>ชื่อสายงาน / ตำแหน่ง</th>
                    <th>ระดับ</th>
                    <th>สถานะ</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($deptManpowers)): ?>
                    <?php foreach($deptManpowers as $m): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-dark"><?php echo htmlspecialchars($m['position_number'] ?? '-'); ?></td>
                        <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($m['employee_type'] ?? '-'); ?></span></td>
                        <td><?php echo htmlspecialchars($m['division'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($m['position_name'] ?? '-'); ?></td>
                        <td><?php echo htmlspecialchars($m['level'] ?? '-'); ?></td>
                        <td>
                            <?php if(($m['status'] ?? '') == 'occupied'): ?>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success"><i class="fa-solid fa-user-check me-1"></i> มีคนครอง</span>
                            <?php else: ?>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger"><i class="fa-solid fa-user-xmark me-1"></i> ว่าง</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="index.php?action=manpower_edit&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen"></i></a>
                            <?php if(($m['status'] ?? '') == 'vacant'): ?>
                                <a href="index.php?action=manpower_delete&id=<?php echo $m['id']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('ยืนยันการลบตำแหน่งนี้?');"><i class="fa-solid fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center py-4 text-muted">ไม่พบข้อมูลตำแหน่งในกองนี้</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>