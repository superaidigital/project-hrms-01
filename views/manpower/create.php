<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i> เพิ่มตำแหน่งในกรอบอัตรากำลัง</h5>
                    <a href="index.php?action=manpower" class="btn btn-sm btn-light"><i class="fas fa-arrow-left"></i> กลับหน้ารวม</a>
                </div>
                <div class="card-body">
                    
                    <?php if(isset($_SESSION['message'])): ?>
                        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show" role="alert">
                            <?= $_SESSION['message'] ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                    <?php endif; ?>

                    <form action="index.php?action=manpower_store" method="POST">
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
                            <div class="col-md-6">
                                <label class="form-label">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                                <select name="level" class="form-select" required>
                                    <option value="">-- เลือกระดับ --</option>
                                    <?php foreach($position_levels as $lvl): ?>
                                        <option value="<?= htmlspecialchars($lvl['name']) ?>"><?= htmlspecialchars($lvl['name']) ?> (<?= htmlspecialchars($lvl['type']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">สถานะปัจจุบัน <span class="text-danger">*</span></label>
                                <select name="status" id="manpower_status" class="form-select" required>
                                    <option value="vacant" selected>ว่าง (Vacant)</option>
                                    <option value="occupied">มีคนครอง (Occupied)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 🌟 ส่วนค้นหาพนักงานที่จะแสดงเมื่อเลือก "มีคนครอง" 🌟 -->
                        <div class="row mb-3" id="employee_search_section" style="display: none; background-color: #f8f9fa; padding: 15px; border-radius: 8px; border: 1px solid #dee2e6;">
                            <div class="col-12 position-relative">
                                <label class="form-label fw-bold text-primary"><i class="fas fa-search"></i> ค้นหาบุคลากรเพื่อครองตำแหน่งนี้ <span class="text-danger">*</span></label>
                                <input type="text" id="search_emp_input" class="form-control" placeholder="พิมพ์ชื่อ, นามสกุล หรือเลขบัตรประชาชน..." autocomplete="off">
                                
                                <!-- ซ่อน ID พนักงานที่ถูกเลือกไว้ส่งไปบันทึก -->
                                <input type="hidden" name="assigned_employee_id" id="assigned_employee_id">
                                
                                <!-- กล่องแสดงผลการค้นหา -->
                                <div id="search_results" class="list-group position-absolute w-100 shadow-sm" style="z-index: 1000; display: none;"></div>
                                
                                <!-- ข้อความแสดงคนที่เลือกแล้ว -->
                                <div id="selected_employee_display" class="mt-2 fs-6 fw-bold"></div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">หมายเหตุ (ถ้ามี)</label>
                                <textarea name="remark" class="form-control" rows="2" placeholder="เช่น รอการสรรหา, ลาศึกษาต่อ"></textarea>
                            </div>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> บันทึกข้อมูลตำแหน่ง</button>
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

    // 1. ฟังก์ชันเปิด/ปิดกล่องค้นหา
    function toggleSearchBox() {
        if (statusSelect.value === 'occupied') {
            searchSection.style.display = 'block';
            searchInput.setAttribute('required', 'required'); // บังคับกรอกถ้าเลือกว่ามีคนครอง
        } else {
            searchSection.style.display = 'none';
            searchInput.removeAttribute('required');
            hiddenEmpId.value = '';
            selectedDisplay.innerHTML = '';
            searchInput.value = '';
        }
    }

    // ทำงานเมื่อโหลดหน้าเว็บ และเมื่อเปลี่ยน Dropdown สถานะ
    toggleSearchBox();
    statusSelect.addEventListener('change', toggleSearchBox);

    // 2. ฟังก์ชันค้นหาข้อมูล (AJAX)
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
                            
                            // เช็คว่าเดิมทีคนนี้มีเลขตำแหน่งอยู่แล้วหรือไม่
                            let posText = emp.emp_code ? `<span class="badge bg-warning text-dark">เลขตำแหน่งเดิม: ${emp.emp_code}</span>` : `<span class="badge bg-success">ไม่มีตำแหน่ง</span>`;
                            
                            a.innerHTML = `${emp.prefix}${emp.first_name} ${emp.last_name} ${posText}`;
                            
                            // เมื่อคลิกเลือกรายชื่อ
                            a.onclick = function() {
                                hiddenEmpId.value = emp.id;
                                selectedDisplay.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> เลือกแล้ว: ${emp.prefix}${emp.first_name} ${emp.last_name}</span>`;
                                searchInput.value = '';
                                searchInput.removeAttribute('required'); // เอา required ออกเพราะเลือกแล้ว
                                searchResults.style.display = 'none';
                            };
                            searchResults.appendChild(a);
                        });
                    }
                    searchResults.style.display = 'block';
                })
                .catch(error => console.error('Error fetching employees:', error));
        }, 300); // หน่วงเวลา 300ms รอให้พิมพ์เสร็จ
    });

    // ซ่อนผลลัพธ์การค้นหาเมื่อคลิกที่อื่น
    document.addEventListener('click', function(e) {
        if (e.target !== searchInput) {
            searchResults.style.display = 'none';
        }
    });
});
</script>

<?php require_once 'views/layout/footer.php'; ?>