<!-- 
==========================================
ชื่อไฟล์: index.php
ที่อยู่ไฟล์: views/position_level/index.php
==========================================
-->
<?php include 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-layer-group text-info me-2"></i> จัดการระดับตำแหน่ง (อปท.)</h2>
        <p class="text-muted mt-1 mb-0">เพิ่ม แก้ไข หรือลบ ข้อมูลระดับตำแหน่งและสายงาน</p>
    </div>
    <div>
        <a href="index.php?action=settings" class="btn btn-light border shadow-sm me-2 text-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> กลับหน้าตั้งค่า
        </a>
        <button type="button" class="btn btn-info text-white shadow-sm" data-bs-toggle="modal" data-bs-target="#addLevelModal">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มระดับตำแหน่ง
        </button>
    </div>
</div>

<div class="modern-card p-0 overflow-hidden shadow-sm border-0 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="ps-4 py-3">ระดับตำแหน่ง</th>
                    <th class="py-3">ประเภทสายงาน</th>
                    <th class="text-end pe-4 py-3">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($position_levels)): ?>
                <tr><td colspan="3" class="text-center py-4 text-muted">ไม่พบข้อมูลระดับตำแหน่ง</td></tr>
                <?php endif; ?>

                <?php foreach ($position_levels as $level): ?>
                <tr>
                    <td class="ps-4 fw-bold text-dark">
                        <?php echo htmlspecialchars($level['name']); ?>
                    </td>
                    <td>
                        <?php 
                            $badgeClass = 'bg-secondary';
                            if($level['type'] == 'ประเภทวิชาการ') $badgeClass = 'bg-primary-subtle text-primary border-primary-subtle';
                            else if($level['type'] == 'ประเภททั่วไป') $badgeClass = 'bg-success-subtle text-success border-success-subtle';
                            else if($level['type'] == 'ประเภทอำนวยการท้องถิ่น') $badgeClass = 'bg-warning-subtle text-warning border-warning-subtle';
                            else if($level['type'] == 'ประเภทบริหารท้องถิ่น') $badgeClass = 'bg-danger-subtle text-danger border-danger-subtle';
                        ?>
                        <span class="badge <?php echo $badgeClass; ?> border px-3 py-2 rounded-pill">
                            <?php echo htmlspecialchars($level['type']); ?>
                        </span>
                    </td>
                    <td class="text-end pe-4">
                        <button type="button" class="btn btn-sm btn-light text-primary border me-1" data-bs-toggle="modal" data-bs-target="#editLevelModal<?php echo $level['id']; ?>">
                            <i class="fa-solid fa-pen-to-square"></i> แก้ไข
                        </button>
                        <a href="index.php?action=position_levels_delete&id=<?php echo $level['id']; ?>" class="btn btn-sm btn-light text-danger border" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลนี้?');">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal แก้ไขระดับตำแหน่ง -->
                <div class="modal fade" id="editLevelModal<?php echo $level['id']; ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content rounded-4 border-0 shadow">
                            <div class="modal-header bg-light border-bottom-0 pb-0 pt-4 px-4">
                                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen text-info me-2"></i> แก้ไขข้อมูลระดับตำแหน่ง</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <form action="index.php?action=position_levels_update" method="POST">
                                <input type="hidden" name="id" value="<?php echo $level['id']; ?>">
                                <div class="modal-body p-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted small">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control bg-light" name="name" required value="<?php echo htmlspecialchars($level['name']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted small">ประเภทสายงาน <span class="text-danger">*</span></label>
                                        <select class="form-select bg-light" name="type" required>
                                            <option value="ประเภททั่วไป" <?php echo $level['type'] == 'ประเภททั่วไป' ? 'selected' : ''; ?>>ประเภททั่วไป</option>
                                            <option value="ประเภทวิชาการ" <?php echo $level['type'] == 'ประเภทวิชาการ' ? 'selected' : ''; ?>>ประเภทวิชาการ</option>
                                            <option value="ประเภทอำนวยการท้องถิ่น" <?php echo $level['type'] == 'ประเภทอำนวยการท้องถิ่น' ? 'selected' : ''; ?>>ประเภทอำนวยการท้องถิ่น</option>
                                            <option value="ประเภทบริหารท้องถิ่น" <?php echo $level['type'] == 'ประเภทบริหารท้องถิ่น' ? 'selected' : ''; ?>>ประเภทบริหารท้องถิ่น</option>
                                            <option value="อื่นๆ" <?php echo $level['type'] == 'อื่นๆ' ? 'selected' : ''; ?>>อื่นๆ / ไม่มีระดับ</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">ยกเลิก</button>
                                    <button type="submit" class="btn btn-info text-white fw-bold shadow-sm">บันทึกการแก้ไข</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal เพิ่มระดับตำแหน่ง -->
<div class="modal fade" id="addLevelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-light border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-layer-group text-info me-2"></i> เพิ่มระดับตำแหน่งใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=position_levels_store" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                        <input type="text" class="form-control bg-light" name="name" required placeholder="เช่น ชำนาญการพิเศษ">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted small">ประเภทสายงาน <span class="text-danger">*</span></label>
                        <select class="form-select bg-light" name="type" required>
                            <option value="ประเภททั่วไป">ประเภททั่วไป</option>
                            <option value="ประเภทวิชาการ">ประเภทวิชาการ</option>
                            <option value="ประเภทอำนวยการท้องถิ่น">ประเภทอำนวยการท้องถิ่น</option>
                            <option value="ประเภทบริหารท้องถิ่น">ประเภทบริหารท้องถิ่น</option>
                            <option value="อื่นๆ">อื่นๆ / ไม่มีระดับ</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-top-0 pt-0 px-4 pb-4">
                    <button type="button" class="btn btn-light border fw-bold" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-info text-white fw-bold shadow-sm">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>