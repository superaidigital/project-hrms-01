<!-- 
==========================================
ชื่อไฟล์: index.php
ที่อยู่ไฟล์: views/employee/index.php
==========================================
-->
<?php include 'views/layout/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold mb-0"><i class="fa-regular fa-address-card text-teal me-2"></i> ทะเบียนประวัติบุคลากร</h2>
        <p class="text-muted">ค้นหาและจัดการข้อมูลประวัติข้าราชการและพนักงาน</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <!-- ปุ่มส่งออก Excel -->
        <a href="index.php?action=export_excel" class="btn btn-light border shadow-sm fw-semibold text-secondary" title="ส่งออกข้อมูลทั้งหมดเป็นไฟล์ Excel">
            <i class="fa-solid fa-file-export text-success me-1"></i> ส่งออกข้อมูล
        </a>
        
        <!-- 🌟 ปุ่มนำเข้า Excel (เปิด Modal) 🌟 -->
        <button type="button" class="btn btn-light border shadow-sm fw-semibold text-secondary" data-bs-toggle="modal" data-bs-target="#importExcelModal" title="นำเข้าข้อมูลบุคลากรด้วยไฟล์ Excel">
            <i class="fa-solid fa-file-import text-primary me-1"></i> นำเข้าข้อมูล
        </button>

        <!-- ปุ่มเพิ่มบุคลากร -->
        <a href="index.php?action=create" class="btn text-white fw-semibold shadow-sm" style="background: linear-gradient(to right, #14b8a6, #10b981);">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มบุคลากร
        </a>
    </div>
</div>

<?php if(isset($_SESSION['message'])): ?>
    <div class="alert alert-<?= htmlspecialchars($_SESSION['message_type'], ENT_QUOTES, 'UTF-8') ?> alert-dismissible fade show shadow-sm" role="alert">
        <?= htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
<?php endif; ?>

<div class="modern-card card border-0 shadow-sm p-4 rounded-4 bg-white">
    
    <!-- ฟอร์มค้นหาและกรองข้อมูล -->
    <div class="row mb-4">
        <div class="col-md-10 col-lg-8">
            <form action="index.php" method="GET" class="d-flex flex-wrap gap-2 w-100">
                <input type="hidden" name="action" value="employees">
                
                <!-- 🛡️ [ป้องกัน XSS]: htmlspecialchars ค่าการค้นหาเดิม -->
                <input type="text" name="search" class="form-control form-control-sm" style="max-width: 250px;" 
                       placeholder="ค้นหา ชื่อ, สกุล, รหัส..." 
                       value="<?= htmlspecialchars($search_term ?? '', ENT_QUOTES, 'UTF-8') ?>">
                
                <select name="dept_type" class="form-select form-select-sm" style="max-width: 200px;">
                    <option value="">-- ทุกส่วนราชการ --</option>
                    <?php if(!empty($department_types)): foreach($department_types as $dt): ?>
                        <option value="<?= htmlspecialchars($dt['type'], ENT_QUOTES, 'UTF-8') ?>" 
                            <?= (isset($dept_type) && $dept_type === $dt['type']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dt['type'], ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; endif; ?>
                </select>
                
                <button type="submit" class="btn btn-sm btn-primary px-3"><i class="fa-solid fa-magnifying-glass"></i> ค้นหา</button>
                <a href="index.php?action=employees" class="btn btn-sm btn-light border"><i class="fa-solid fa-rotate-right"></i> ล้างค่า</a>
            </form>
        </div>
    </div>

    <!-- ตารางแสดงข้อมูลบุคลากร -->
    <div class="table-responsive">
        <table class="table table-hover align-middle custom-table">
            <thead class="table-light">
                <tr>
                    <th class="text-center" width="60">รูป</th>
                    <th>รหัสพนักงาน</th>
                    <th>ชื่อ-นามสกุล</th>
                    <th>ประเภท/ตำแหน่ง/ระดับ</th>
                    <th>ส่วนราชการ</th>
                    <th width="150">สถานะ</th>
                    <th class="text-center" width="140">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($employees)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="fa-solid fa-inbox fa-3x mb-3 opacity-25"></i><br>
                            ไม่พบข้อมูลบุคลากรในระบบ
                        </td>
                    </tr>
                <?php else: foreach($employees as $emp): ?>
                    <tr>
                        <td class="text-center">
                            <?php if(!empty($emp['avatar']) && file_exists("uploads/" . $emp['avatar'])): ?>
                                <img src="uploads/<?= htmlspecialchars($emp['avatar'], ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="Avatar" class="rounded-circle object-fit-cover shadow-sm" width="45" height="45">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary bg-opacity-10 text-secondary d-flex align-items-center justify-content-center mx-auto shadow-sm" style="width:45px;height:45px; font-weight:bold;">
                                    <?= htmlspecialchars(mb_substr($emp['first_name'], 0, 1, 'UTF-8'), ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="fw-semibold text-secondary"><?= htmlspecialchars($emp['emp_code'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <div class="fw-bold text-dark">
                                <?= htmlspecialchars(($emp['prefix'] ?? '') . ' ' . ($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
                            </div>
                            <small class="text-muted"><i class="fa-solid fa-id-card me-1"></i> <?= htmlspecialchars($emp['national_id'] ?? '-', ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 mb-1">
                                <?= htmlspecialchars($emp['employee_type'] ?? 'ไม่ระบุประเภท', ENT_QUOTES, 'UTF-8') ?>
                            </span><br>
                            <span class="text-dark fw-medium"><?= htmlspecialchars($emp['position'] ?? '-', ENT_QUOTES, 'UTF-8') ?></span> 
                            <small class="text-muted"><?= htmlspecialchars($emp['level'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($emp['department'] ?? '-', ENT_QUOTES, 'UTF-8') ?></div>
                            <small class="text-muted"><?= htmlspecialchars($emp['department_type'] ?? '', ENT_QUOTES, 'UTF-8') ?></small>
                        </td>
                        <td>
                            <!-- Dropdown เปลี่ยนสถานะการทำงาน (เรียกใช้ AJAX) -->
                            <select class="form-select form-select-sm status-select <?= ($emp['status'] == 'ปฏิบัติงาน') ? 'border-success text-success fw-bold' : 'border-secondary text-secondary' ?>" data-id="<?= htmlspecialchars($emp['id'], ENT_QUOTES, 'UTF-8') ?>">
                                <option value="ปฏิบัติงาน" <?= ($emp['status'] == 'ปฏิบัติงาน') ? 'selected' : '' ?>>ปฏิบัติงาน</option>
                                <option value="ลาออก" <?= ($emp['status'] == 'ลาออก') ? 'selected' : '' ?>>ลาออก</option>
                                <option value="เกษียณอายุ" <?= ($emp['status'] == 'เกษียณอายุ') ? 'selected' : '' ?>>เกษียณอายุ</option>
                                <option value="โอนย้าย" <?= ($emp['status'] == 'โอนย้าย') ? 'selected' : '' ?>>โอนย้าย</option>
                                <option value="เสียชีวิต" <?= ($emp['status'] == 'เสียชีวิต') ? 'selected' : '' ?>>เสียชีวิต</option>
                            </select>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <a href="index.php?action=show&id=<?= htmlspecialchars($emp['id'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-light border text-primary" title="ดูประวัติ ก.พ.7"><i class="fa-solid fa-file-lines"></i></a>
                                <a href="index.php?action=edit&id=<?= htmlspecialchars($emp['id'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-light border text-warning" title="แก้ไขข้อมูล"><i class="fa-solid fa-pen-to-square"></i></a>
                                <a href="index.php?action=delete&id=<?= htmlspecialchars($emp['id'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-sm btn-light border text-danger" title="ลบข้อมูล" onclick="return confirm('⚠️ ยืนยันการลบข้อมูลบุคลากรท่านนี้? ข้อมูลประวัติทั้งหมดจะถูกลบและไม่สามารถกู้คืนได้')"><i class="fa-solid fa-trash-can"></i></a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ระบบแบ่งหน้า (Pagination) -->
    <?php if(isset($total_pages) && $total_pages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?action=employees&page=<?= $page - 1 ?>&search=<?= urlencode($search_term) ?>&dept_type=<?= urlencode($dept_type) ?>">ก่อนหน้า</a>
                </li>
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?= ($page == $i) ? 'active' : '' ?>">
                        <a class="page-link" href="index.php?action=employees&page=<?= $i ?>&search=<?= urlencode($search_term) ?>&dept_type=<?= urlencode($dept_type) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                    <a class="page-link" href="index.php?action=employees&page=<?= $page + 1 ?>&search=<?= urlencode($search_term) ?>&dept_type=<?= urlencode($dept_type) ?>">ถัดไป</a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<!-- ========================================== -->
<!-- 🌟 Modal สำหรับอัปโหลด Excel นำเข้าข้อมูล 🌟 -->
<!-- ========================================== -->
<div class="modal fade" id="importExcelModal" tabindex="-1" aria-labelledby="importExcelModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            
            <div class="modal-header border-bottom-0 pb-0 pt-4 px-4">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center" id="importExcelModalLabel">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                        <i class="fa-solid fa-file-import"></i>
                    </div>
                    นำเข้าข้อมูลบุคลากรด้วยไฟล์ Excel
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="index.php?action=employee_import" method="POST" enctype="multipart/form-data">
                <div class="modal-body px-4 py-4">
                    
                    <div class="alert alert-info border-0 shadow-sm rounded-3 mb-4">
                        <h6 class="fw-bold text-info-emphasis mb-2"><i class="fa-solid fa-circle-info me-2"></i> คำแนะนำการนำเข้า</h6>
                        <p class="small mb-2 text-info-emphasis">
                            กรุณาดาวน์โหลดไฟล์ Excel ต้นแบบ และกรอกข้อมูลบุคลากรให้ตรงตามคอลัมน์ที่กำหนด (ห้ามแก้ไขหัวคอลัมน์แถวแรก)
                        </p>
                        <!-- 🌟 เปลี่ยนให้เรียกจากระบบหลังบ้านแทนการระบุไฟล์ตรงๆ 🌟 -->
                        <a href="index.php?action=download_template" class="btn btn-sm btn-info text-white fw-bold shadow-sm">
                            <i class="fa-solid fa-download me-1"></i> ดาวน์โหลดไฟล์ต้นแบบ (Template)
                        </a>
                    </div>

                    <div class="mb-3">
                        <label for="excel_file" class="form-label fw-bold text-dark">อัปโหลดไฟล์ที่กรอกข้อมูลแล้ว <span class="text-danger">*</span></label>
                        <input class="form-control form-control-lg border-secondary border-opacity-25 bg-light" type="file" id="excel_file" name="excel_file" accept=".xls,.xlsx,.csv" required>
                        <div class="form-text mt-2"><i class="fa-solid fa-paperclip me-1"></i> รองรับไฟล์นามสกุล .xls, .xlsx หรือ .csv เท่านั้น</div>
                    </div>

                </div>
                
                <div class="modal-footer border-top-0 pt-0 pb-4 px-4 justify-content-between">
                    <button type="button" class="btn btn-light border shadow-sm px-4 rounded-3 fw-medium" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3 shadow-sm fw-bold" onclick="this.innerHTML='<i class=\'fa-solid fa-spinner fa-spin me-2\'></i> กำลังนำเข้า...'; this.classList.add('disabled'); this.form.submit();">
                        <i class="fa-solid fa-cloud-arrow-up me-2"></i> ยืนยันการนำเข้า
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- ส่วนของ JavaScript สำหรับการแก้ไขสถานะแบบ AJAX -->
<?php ob_start(); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // เลือก dropdown สถานะทั้งหมด
    const statusSelects = document.querySelectorAll('.status-select');
    
    statusSelects.forEach(selectEl => {
        selectEl.addEventListener('change', function() {
            const empId = this.getAttribute('data-id');
            const newStatus = this.value;
            
            // เปลี่ยนสไตล์ของช่อง dropdown ตามสถานะทันที (UI Update)
            if(newStatus === 'ปฏิบัติงาน') {
                this.className = 'form-select form-select-sm status-select border-success text-success fw-bold';
            } else {
                this.className = 'form-select form-select-sm status-select border-secondary text-secondary';
            }

            this.disabled = true;

            const formData = new FormData();
            formData.append('id', empId);
            formData.append('status', newStatus);

            fetch('index.php?action=employee_update_status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                this.disabled = false; 
                
                if(data.status === 'success') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัปเดตสถานะสำเร็จ',
                            text: 'เปลี่ยนสถานะเป็น: ' + newStatus,
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }
                } else {
                    Swal.fire({ icon: 'error', title: 'ผิดพลาด', text: data.message || 'ไม่สามารถอัปเดตข้อมูลได้' });
                }
            })
            .catch(error => {
                this.disabled = false;
                Swal.fire({ icon: 'error', title: 'การเชื่อมต่อขัดข้อง', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์' });
                console.error('Error:', error);
            });
        });
    });
});
</script>
<?php $extra_scripts = ob_get_clean(); ?>

<?php include 'views/layout/footer.php'; ?>