<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-edit me-2"></i> แก้ไขตำแหน่งในกรอบอัตรากำลัง</h5>
                    <a href="index.php?action=manpower" class="btn btn-sm btn-dark"><i class="fas fa-arrow-left"></i> กลับหน้ารวม</a>
                </div>
                <div class="card-body">
                    
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <form action="index.php?action=manpower_update" method="POST">
                        <input type="hidden" name="id" value="<?= $manpower['id'] ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">สังกัดสำนัก/กอง <span class="text-danger">*</span></label>
                                <select name="department" class="form-select" required>
                                    <option value="">-- เลือกสำนัก/กอง --</option>
                                    <?php foreach($departments as $dept): ?>
                                        <option value="<?= htmlspecialchars($dept['name']) ?>" <?= ($manpower['department'] == $dept['name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ฝ่าย/ส่วน/งาน</label>
                                <input type="text" name="division" class="form-control" value="<?= htmlspecialchars($manpower['division']) ?>" placeholder="เช่น ฝ่ายบริหารงานทั่วไป">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">ประเภทบุคลากร <span class="text-danger">*</span></label>
                                <select name="employee_type" class="form-select" required>
                                    <option value="ข้าราชการ อบจ." <?= ($manpower['employee_type'] == 'ข้าราชการ อบจ.') ? 'selected' : '' ?>>ข้าราชการ อบจ.</option>
                                    <option value="ข้าราชการครู อบจ." <?= ($manpower['employee_type'] == 'ข้าราชการครู อบจ.') ? 'selected' : '' ?>>ข้าราชการครู อบจ.</option>
                                    <option value="ลูกจ้างประจำ" <?= ($manpower['employee_type'] == 'ลูกจ้างประจำ') ? 'selected' : '' ?>>ลูกจ้างประจำ</option>
                                    <option value="พนักงานจ้างตามภารกิจ" <?= ($manpower['employee_type'] == 'พนักงานจ้างตามภารกิจ') ? 'selected' : '' ?>>พนักงานจ้างตามภารกิจ</option>
                                    <option value="พนักงานจ้างทั่วไป" <?= ($manpower['employee_type'] == 'พนักงานจ้างทั่วไป') ? 'selected' : '' ?>>พนักงานจ้างทั่วไป</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">เลขที่ตำแหน่ง <span class="text-danger">*</span></label>
                                <input type="text" name="position_number" class="form-control" required value="<?= htmlspecialchars($manpower['position_number']) ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ชื่อสายงาน/ตำแหน่ง <span class="text-danger">*</span></label>
                                <input type="text" name="position_name" class="form-control" required value="<?= htmlspecialchars($manpower['position_name']) ?>">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                                <select name="level" class="form-select" required>
                                    <option value="">-- เลือกระดับ --</option>
                                    <?php foreach($position_levels as $lvl): ?>
                                        <option value="<?= htmlspecialchars($lvl['name']) ?>" <?= ($manpower['level'] == $lvl['name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($lvl['name']) ?> (<?= htmlspecialchars($lvl['type']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">สถานะปัจจุบัน <span class="text-danger">*</span></label>
                                <select name="status" id="manpower_status" class="form-select" required>
                                    <option value="vacant" <?= ($manpower['status'] == 'vacant') ? 'selected' : '' ?>>ว่าง (Vacant)</option>
                                    <option value="occupied" <?= ($manpower['status'] == 'occupied') ? 'selected' : '' ?>>มีคนครอง (Occupied)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 🌟 ส่วนจัดการคนครองตำแหน่ง 🌟 -->
                        <div class="row mb-3" id="employee_search_section" style="display: none; background-color: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                            
                            <?php if($manpower['status'] == 'occupied' && isset($occupant) && $occupant): ?>
                                <div class="col-12 mb-3 p-3 bg-white rounded border border-success">
                                    <h6 class="text-success mb-2"><i class="fas fa-user-check"></i> ผู้ครองตำแหน่งปัจจุบัน:</h6>
                                    <div class="d-flex align-items-center">
                                        <?php $img = !empty($occupant['avatar']) ? 'uploads/'.$occupant['avatar'] : 'assets/images/default-avatar.png'; ?>
                                        <img src="<?= $img ?>" class="rounded-circle me-3" width="50" height="50" style="object-fit:cover;">
                                        <div>
                                            <p class="mb-0 fw-bold"><?= htmlspecialchars($occupant['prefix'].$occupant['first_name'].' '.$occupant['last_name']) ?></p>
                                            <small class="text-muted">เลข ปชช: <?= htmlspecialchars($occupant['national_id']) ?></small>
                                        </div>
                                    </div>
                                    <p class="mt-2 text-muted small"><i class="fas fa-info-circle"></i> หากต้องการเปลี่ยนคน ให้ค้นหาใหม่ด้านล่าง หรือถ้าต้องการปลดตำแหน่งให้เปลี่ยนสถานะเป็น "ว่าง"</p>
                                </div>
                            <?php endif; ?>

                            <div class="col-12 position-relative">
                                <label class="form-label fw-bold text-primary"><i class="fas fa-search"></i> เปลี่ยน/ระบุบุคลากรที่จะครองตำแหน่งนี้</label>
                                <input type="text" id="search_emp_input" class="form-control" placeholder="พิมพ์ชื่อ, นามสกุล หรือเลขบัตรประชาชน (ข้ามได้ถ้าใช้คนเดิม)..." autocomplete="off">
                                
                                <input type="hidden" name="assigned_employee_id" id="assigned_employee_id">
                                
                                <div id="search_results" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; display: none;"></div>
                                <div id="selected_employee_display" class="mt-2 fs-6 fw-bold"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">หมายเหตุ (ถ้ามี)</label>
                                <textarea name="remark" class="form-control" rows="2"><?= htmlspecialchars($manpower['remark']) ?></textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> อัปเดตข้อมูล</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('manpower_status');
    const searchSection = document.getElementById('employee_search_section');
    const searchInput = document.getElementById('search_emp_input');
    const searchResults = document.getElementById('search_results');
    const hiddenEmpId = document.getElementById('assigned_employee_id');
    const selectedDisplay = document.getElementById('selected_employee_display');

    // 1. ตรวจสอบสถานะว่าเปิดหน้าค้นหาหรือไม่
    function toggleSearchBox() {
        if (statusSelect.value === 'occupied') {
            searchSection.style.display = 'block';
        } else {
            searchSection.style.display = 'none';
            hiddenEmpId.value = '';
            selectedDisplay.innerHTML = '';
            searchInput.value = '';
        }
    }

    toggleSearchBox();
    statusSelect.addEventListener('change', toggleSearchBox);

    // 2. ระบบค้นหา (AJAX)
    let typingTimer;
    searchInput.addEventListener('keyup', function() {
        clearTimeout(typingTimer);
        const query = this.value.trim();
        
        if (query.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        typingTimer = setTimeout(() => {
            fetch(`index.php?action=search_employee&q=${encodeURIComponent(query)}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length === 0) {
                        searchResults.innerHTML = '<div class="list-group-item text-danger">ไม่พบข้อมูลบุคลากร</div>';
                    } else {
                        data.forEach(emp => {
                            const a = document.createElement('a');
                            a.href = 'javascript:void(0)';
                            a.className = 'list-group-item list-group-item-action';
                            
                            let posText = emp.emp_code ? `<span class="badge bg-warning text-dark">เลขตำแหน่งเดิม: ${emp.emp_code}</span>` : `<span class="badge bg-success">ไม่มีตำแหน่ง</span>`;
                            a.innerHTML = `${emp.prefix}${emp.first_name} ${emp.last_name} ${posText}`;
                            
                            a.onclick = function() {
                                hiddenEmpId.value = emp.id;
                                selectedDisplay.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> เลือกให้ดำรงตำแหน่งใหม่: ${emp.prefix}${emp.first_name} ${emp.last_name}</span>`;
                                searchInput.value = '';
                                searchResults.style.display = 'none';
                            };
                            searchResults.appendChild(a);
                        });
                    }
                    searchResults.style.display = 'block';
                })
                .catch(error => console.error('Error:', error));
        }, 300);
    });

    // ซ่อนผลลัพธ์
    document.addEventListener('click', function(e) {
        if (e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>