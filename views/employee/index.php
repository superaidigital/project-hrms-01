<?php include 'views/layout/header.php'; ?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold mb-0">ทะเบียนประวัติบุคลากร</h2>
        <p class="text-muted">ค้นหาและจัดการข้อมูลประวัติข้าราชการและพนักงาน</p>
    </div>
    <div class="d-flex gap-2">
        <a href="index.php?action=export_excel" class="btn btn-light border shadow-sm fw-semibold text-secondary" title="ส่งออกข้อมูลทั้งหมดเป็นไฟล์ Excel">
            <i class="fa-solid fa-file-excel text-success me-1"></i> ส่งออก Excel
        </a>
        <a href="index.php?action=create" class="btn text-white fw-semibold shadow-sm" style="background: linear-gradient(to right, #14b8a6, #10b981);">
            <i class="fa-solid fa-plus me-1"></i> เพิ่มบุคลากร
        </a>
    </div>
</div>

<div class="modern-card">
    
    <!-- ฟอร์มค้นหาและกรองข้อมูล -->
    <div class="row mb-4">
        <div class="col-md-8 col-lg-6">
            <form action="index.php" method="GET" class="d-flex gap-2 w-100">
                <input type="hidden" name="action" value="employees">
                <div class="input-group shadow-sm border-0 rounded-3">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" class="form-control border-start-0 border-end-0" name="search" placeholder="ค้นหา ชื่อ, นามสกุล..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    
                    <!-- Dropdown กรองประเภทส่วนราชการ -->
                    <select name="dept_type" class="form-select border-start-0 text-secondary" style="max-width: 180px; border-left: 1px solid #dee2e6;">
                        <option value="">ทุกส่วนราชการ</option>
                        <?php if(!empty($department_types)): ?>
                            <?php foreach($department_types as $type): ?>
                                <option value="<?php echo htmlspecialchars($type['type']); ?>" <?php echo (isset($_GET['dept_type']) && $_GET['dept_type'] == $type['type']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($type['type']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    
                    <button class="btn btn-outline-secondary px-3" type="submit">ค้นหา</button>
                </div>
                <?php if(!empty($_GET['search']) || !empty($_GET['dept_type'])): ?>
                    <a href="index.php?action=employees" class="btn btn-light border" title="ล้างการค้นหา"><i class="fa-solid fa-xmark text-danger"></i></a>
                <?php endif; ?>
            </form>
        </div>
        <div class="col-md-4 col-lg-6 text-md-end mt-2 mt-md-0 pt-2">
            <span class="text-muted small fw-bold">พบข้อมูลทั้งหมด: <span class="text-teal"><?php echo $total_rows ?? 0; ?></span> รายการ</span>
        </div>
    </div>

    <!-- ตารางรายชื่อ -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-white">
                <tr>
                    <th class="ps-3">ข้อมูลบุคลากร</th>
                    <th>สายงาน / ระดับ</th>
                    <th>สังกัด</th>
                    <th class="text-center" width="160px">สถานะ</th>
                    <th class="text-end pe-3">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if(!empty($employees)): ?>
                    <?php foreach($employees as $emp): ?>
                    <tr>
                        <td class="ps-3">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($emp['first_name'] ?? 'User'); ?>&background=14b8a6&color=fff" class="rounded-circle me-3" width="45" height="45">
                                <div>
                                    <div class="fw-bold text-dark"><?php echo ($emp['prefix'] ?? '') . ($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''); ?></div>
                                    <div class="text-muted" style="font-size: 0.8rem;"><?php echo $emp['emp_code'] ?? '-'; ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark"><?php echo $emp['position'] ?? '-'; ?></div>
                            <div class="text-muted" style="font-size: 0.8rem;"><span class="text-teal" style="color: #0d9488;"><?php echo !empty($emp['level']) ? $emp['level'] : '-'; ?></span> | <?php echo $emp['employee_type'] ?? '-'; ?></div>
                        </td>
                        <td>
                            <div class="text-secondary fw-medium"><?php echo $emp['department'] ?? '-'; ?></div>
                        </td>
                        <td class="text-center">
                            <?php 
                            // กำหนดคลาสสีเริ่มต้นสำหรับ Dropdown ตามสถานะปัจจุบัน
                            $current_status = !empty($emp['status']) ? $emp['status'] : 'ปฏิบัติงาน';
                            $select_class = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                            
                            if($current_status == 'ปฏิบัติงาน') $select_class = 'bg-success-subtle text-success border-success-subtle';
                            elseif($current_status == 'ช่วยราชการ') $select_class = 'bg-info-subtle text-info border-info-subtle';
                            elseif($current_status == 'ลาศึกษาต่อ') $select_class = 'bg-primary-subtle text-primary border-primary-subtle';
                            elseif(in_array($current_status, ['ลาออก', 'เกษียณอายุ', 'โอนย้าย'])) $select_class = 'bg-secondary-subtle text-secondary border-secondary-subtle';
                            elseif(in_array($current_status, ['ถูกพักราชการ', 'เสียชีวิต'])) $select_class = 'bg-danger-subtle text-danger border-danger-subtle';
                            ?>
                            
                            <!-- เปลี่ยน Badge เป็น Select Dropdown (Interactive) -->
                            <select class="form-select form-select-sm rounded-pill border fw-bold text-center status-ajax-dropdown <?php echo $select_class; ?>" data-id="<?php echo $emp['id']; ?>" style="cursor: pointer; font-size: 0.85rem; padding-top: 0.25rem; padding-bottom: 0.25rem;">
                                <option value="ปฏิบัติงาน" <?php echo $current_status == 'ปฏิบัติงาน' ? 'selected' : ''; ?> class="bg-white text-dark">ปฏิบัติงาน</option>
                                <option value="ช่วยราชการ" <?php echo $current_status == 'ช่วยราชการ' ? 'selected' : ''; ?> class="bg-white text-dark">ช่วยราชการ</option>
                                <option value="ลาศึกษาต่อ" <?php echo $current_status == 'ลาศึกษาต่อ' ? 'selected' : ''; ?> class="bg-white text-dark">ลาศึกษาต่อ</option>
                                <option value="ถูกพักราชการ" <?php echo $current_status == 'ถูกพักราชการ' ? 'selected' : ''; ?> class="bg-white text-dark">ถูกพักราชการ</option>
                                <option value="เกษียณอายุ" <?php echo $current_status == 'เกษียณอายุ' ? 'selected' : ''; ?> class="bg-white text-dark">เกษียณอายุ</option>
                                <option value="ลาออก" <?php echo $current_status == 'ลาออก' ? 'selected' : ''; ?> class="bg-white text-dark">ลาออก</option>
                                <option value="โอนย้าย" <?php echo $current_status == 'โอนย้าย' ? 'selected' : ''; ?> class="bg-white text-dark">โอนย้าย</option>
                                <option value="เสียชีวิต" <?php echo $current_status == 'เสียชีวิต' ? 'selected' : ''; ?> class="bg-white text-dark">เสียชีวิต</option>
                            </select>
                        </td>
                        <td class="text-end pe-3">
                            <a href="index.php?action=show&id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-light text-teal border border-teal-200 fw-bold me-1" style="color: #0d9488; background-color:#f0fdfa;" title="ดูประวัติ">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="index.php?action=edit&id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-light text-primary border me-1" title="แก้ไขข้อมูล">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <a href="index.php?action=delete&id=<?php echo $emp['id']; ?>" class="btn btn-sm btn-light text-danger border" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลของคุณ <?php echo $emp['first_name']; ?>?');" title="ลบข้อมูล">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-folder-open fs-1 text-light mb-3 d-block"></i>
                            ไม่พบข้อมูลบุคลากรที่คุณค้นหา
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ส่วนแบ่งหน้า (Pagination) -->
    <?php if(isset($total_pages) && $total_pages > 1): ?>
    <?php 
        $search_query = ""; 
        if(!empty($_GET['search'])) $search_query .= "&search=" . urlencode($_GET['search']);
        if(!empty($_GET['dept_type'])) $search_query .= "&dept_type=" . urlencode($_GET['dept_type']);
        $current_page = isset($page) ? $page : 1;
    ?>
    <nav aria-label="Page navigation" class="mt-4 pt-3 border-top">
        <ul class="pagination justify-content-center mb-0">
            <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link text-teal" href="index.php?action=employees&page=<?php echo $current_page - 1; ?><?php echo $search_query; ?>">ก่อนหน้า</a>
            </li>
            
            <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?php echo $current_page == $i ? 'active' : ''; ?>">
                    <a class="page-link <?php echo $current_page == $i ? 'text-white' : 'text-teal'; ?>" 
                        style="<?php echo $current_page == $i ? 'background-color: #0d9488; border-color: #0d9488;' : ''; ?>"
                        href="index.php?action=employees&page=<?php echo $i; ?><?php echo $search_query; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                <a class="page-link text-teal" href="index.php?action=employees&page=<?php echo $current_page + 1; ?><?php echo $search_query; ?>">ถัดไป</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php 
// เริ่มต้นการสร้างสตริงสำหรับสคริปต์ (จะถูกนำไปวางไว้ท้ายไฟล์ด้วย footer.php)
ob_start(); 
?>
<!-- สคริปต์ JavaScript สำหรับเปลี่ยนสถานะแบบ Real-time (ใช้ Fetch API ไม่ต้องง้อ jQuery) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // หา Dropdown สถานะทั้งหมดในหน้า
    const dropdowns = document.querySelectorAll('.status-ajax-dropdown');
    
    dropdowns.forEach(function(dropdown) {
        dropdown.addEventListener('change', function() {
            const selectEl = this;
            const empId = selectEl.getAttribute('data-id');
            const newStatus = selectEl.value;
            
            // 1. ลบคลาสสีเก่าออกทั้งหมด
            selectEl.classList.remove(
                'bg-success-subtle', 'text-success', 'border-success-subtle', 
                'bg-info-subtle', 'text-info', 'border-info-subtle', 
                'bg-primary-subtle', 'text-primary', 'border-primary-subtle', 
                'bg-secondary-subtle', 'text-secondary', 'border-secondary-subtle', 
                'bg-danger-subtle', 'text-danger', 'border-danger-subtle'
            );
            
            // 2. ใส่คลาสสีใหม่เข้าไปตามสถานะที่เลือก
            if(newStatus === 'ปฏิบัติงาน') selectEl.classList.add('bg-success-subtle', 'text-success', 'border-success-subtle');
            else if(newStatus === 'ช่วยราชการ') selectEl.classList.add('bg-info-subtle', 'text-info', 'border-info-subtle');
            else if(newStatus === 'ลาศึกษาต่อ') selectEl.classList.add('bg-primary-subtle', 'text-primary', 'border-primary-subtle');
            else if(['ลาออก', 'เกษียณอายุ', 'โอนย้าย'].includes(newStatus)) selectEl.classList.add('bg-secondary-subtle', 'text-secondary', 'border-secondary-subtle');
            else if(['ถูกพักราชการ', 'เสียชีวิต'].includes(newStatus)) selectEl.classList.add('bg-danger-subtle', 'text-danger', 'border-danger-subtle');

            // ปิดการใช้งาน dropdown ชั่วคราวป้องกันการกดรัว
            selectEl.disabled = true;

            // 3. เตรียมข้อมูลสำหรับส่งไป Backend
            const formData = new FormData();
            formData.append('id', empId);
            formData.append('status', newStatus);

            // 4. ส่งข้อมูลด้วย Fetch API
            fetch('index.php?action=employee_update_status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                selectEl.disabled = false; // เปิดให้ใช้งานต่อ
                
                if(data.status === 'success') {
                    // เรียกใช้ SweetAlert2 แจ้งเตือนสวยๆ
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: 'อัปเดตสถานะสำเร็จ',
                            text: 'เปลี่ยนสถานะเป็น: ' + newStatus,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } else {
                    alert('ผิดพลาด: ' + (data.message || 'ไม่สามารถอัปเดตข้อมูลได้'));
                }
            })
            .catch(error => {
                selectEl.disabled = false;
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อกับเซิร์ฟเวอร์');
                console.error('Error:', error);
            });
        });
    });
});
</script>
<?php 
$extra_scripts = ob_get_clean(); 
?>

<?php include 'views/layout/footer.php'; ?>