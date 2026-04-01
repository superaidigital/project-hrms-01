<?php
// ==========================================
// ชื่อไฟล์: detail.php
// ที่อยู่ไฟล์: views/manpower/detail.php
// ==========================================

// 🚀 1. ระบบป้องกันการเข้าไฟล์โดยตรง (Auto-Redirect)
if (strpos($_SERVER['REQUEST_URI'], 'views/manpower/detail.php') !== false) {
    $queryString = $_SERVER['QUERY_STRING'] ? '&' . $_SERVER['QUERY_STRING'] : '';
    header("Location: ../../index.php?action=manpower_detail" . $queryString);
    exit();
}

include 'views/layout/header.php'; 

// 🛡️ 2. ฟังก์ชันช่วยเหลือสำหรับป้องกัน XSS
if (!function_exists('h')) {
    function h($string) { return htmlspecialchars((string)($string ?? ''), ENT_QUOTES, 'UTF-8'); }
}

// 💡 3. [Fallback] สำหรับ Dropdown ใน Modal
global $db;
if (empty($position_levels) && isset($db)) {
    try { $stmt = $db->query("SELECT name, type FROM position_levels ORDER BY id ASC"); $position_levels = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch (Exception $e) {}
}
if (empty($departments) && isset($db)) {
    try { $stmt = $db->query("SELECT name FROM departments ORDER BY name ASC"); $departments = $stmt->fetchAll(PDO::FETCH_ASSOC); } catch (Exception $e) {}
}

// 4. จัดเตรียมข้อมูลแสดงผล
$is_dept_view = isset($_GET['dept']) && !empty($_GET['dept']);
$dept_name = $is_dept_view ? trim($_GET['dept']) : '';

if ($is_dept_view) {
    $page_title = "รายละเอียดอัตรากำลัง: " . h($dept_name);
    $positionsList = $dept_manpowers ?? []; 
} else {
    $page_title = "รายละเอียดเลขที่ตำแหน่ง: " . h($manpower['position_number'] ?? '-');
    $positionsList = isset($manpower) && !empty($manpower) ? [$manpower] : [];
}

// คำนวณสรุปยอดอัตรากำลังในหน้านี้
$sum_total = count($positionsList); $sum_occ = 0; $sum_vac = 0;
foreach($positionsList as $p) { if(($p['status'] ?? '') == 'occupied') $sum_occ++; else $sum_vac++; }

// พารามิเตอร์สำหรับ Redirect กลับหลังจัดการข้อมูล
$return_url_param = $is_dept_view ? 'dept='.urlencode($dept_name) : 'id='.h($manpower['id'] ?? '');
?>

<!-- 🌟 นำเข้า SortableJS สำหรับฟีเจอร์ลากวาง 🌟 -->
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

<style>
/* CSS สำหรับตอนที่กำลังลาก (Drag) */
.sortable-ghost { opacity: 0.5; background-color: #f1f5f9; border: 2px dashed #0d9488 !important; }
.drag-handle { cursor: grab; }
.drag-handle:active { cursor: grabbing; }
.hover-bg-light:hover { background-color: #f8fafc !important; }
</style>

<div class="container-fluid mb-5 mt-4">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">
                <i class="fas fa-file-invoice text-teal mr-2" style="color: #0d9488;"></i> <?= $page_title ?>
            </h2>
            <p class="text-muted mb-0"><i class="fas fa-info-circle mr-1"></i> ตรวจสอบรายละเอียดพนักงานและสถานะตำแหน่งในกรอบอัตรากำลัง</p>
        </div>
        <a href="index.php?action=manpower" class="btn btn-white border shadow-sm px-4 py-2 font-weight-bold text-secondary bg-white hover-bg-light" style="border-radius: 8px;">
            <i class="fas fa-arrow-left mr-2"></i> ย้อนกลับไปหน้ารวม
        </a>
    </div>

    <!-- Alert System -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= h($_SESSION['message_type']) ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas <?= $_SESSION['message_type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-2"></i>
            <?= h($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 5px solid #3b82f6; border-radius: 12px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bold mb-1">กรอบตำแหน่งทั้งหมด</h6>
                        <h3 class="font-weight-bold text-dark mb-0"><?= number_format($sum_total) ?> <small class="h6 text-muted">อัตรา</small></h3>
                    </div>
                    <div class="text-primary opacity-25"><i class="fas fa-users fa-2x"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 5px solid #ef4444; border-radius: 12px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bold mb-1">ตำแหน่งว่าง</h6>
                        <h3 class="font-weight-bold text-danger mb-0"><?= number_format($sum_vac) ?> <small class="h6 text-muted">อัตรา</small></h3>
                    </div>
                    <div class="text-danger opacity-25"><i class="fas fa-user-slash fa-2x"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm border-0 h-100" style="border-left: 5px solid #10b981; border-radius: 12px;">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted font-weight-bold mb-1">มีคนครองตำแหน่ง</h6>
                        <h3 class="font-weight-bold text-success mb-0"><?= number_format($sum_occ) ?> <small class="h6 text-muted">อัตรา</small></h3>
                    </div>
                    <div class="text-success opacity-25"><i class="fas fa-user-check fa-2x"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Data Table -->
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center">
                <h5 class="mb-0 font-weight-bold text-dark"><i class="fas fa-list-ul mr-2 text-teal"></i> รายชื่อผู้ครองตำแหน่ง</h5>
                <?php if($is_dept_view && count($positionsList) > 1): ?>
                    <span class="badge bg-light text-secondary border ms-3 fw-normal" style="font-size: 0.85rem;">
                        <i class="fas fa-arrows-alt-v me-1"></i> ลากที่ไอคอนเพื่อสลับลำดับ
                    </span>
                <?php endif; ?>
            </div>
            <div class="input-group" style="max-width: 350px;">
                <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="quickSearch" class="form-control border-start-0 bg-light" placeholder="ค้นหาเลขที่, ชื่อ-สกุล, ตำแหน่ง..." style="box-shadow: none;">
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="positionsTable" style="border-collapse: separate; border-spacing: 0;">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th width="3%" class="border-bottom"></th> <!-- คอลัมน์สำหรับไอคอนลาก -->
                            <th class="py-3 border-bottom text-secondary font-weight-bold" width="13%">เลขที่ตำแหน่ง</th>
                            <th class="py-3 border-bottom text-secondary font-weight-bold" width="22%">ชื่อ - สกุล</th>
                            <th class="py-3 border-bottom text-secondary font-weight-bold" width="18%">ตำแหน่ง</th>
                            <th class="py-3 border-bottom text-secondary font-weight-bold" width="12%">ระดับ</th>
                            <th class="py-3 border-bottom text-secondary font-weight-bold" width="13%">กลุ่มงาน</th>
                            <th class="py-3 border-bottom text-secondary font-weight-bold" width="10%">หมายเหตุ</th>
                            <th class="py-3 pr-4 border-bottom text-center text-secondary font-weight-bold" width="9%">จัดการข้อมูล</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-tbody">
                        <?php if(empty($positionsList)): ?>
                            <tr id="emptyRow"><td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i><br>ไม่พบข้อมูลตำแหน่งในระบบ</td></tr>
                        <?php else: foreach($positionsList as $mp): ?>
                            <!-- 🌟 ใส่ data-id เพื่อส่งค่า ID ตอนสลับลำดับ 🌟 -->
                            <tr class="searchable-row" data-id="<?= h($mp['id']) ?>" style="transition: background-color 0.2s;">
                                
                                <!-- 0. ไอคอนสำหรับลาก (Drag Handle) -->
                                <td class="text-center text-muted drag-handle pl-2">
                                    <i class="fas fa-grip-vertical opacity-50 hover-opacity-100"></i>
                                </td>

                                <!-- 1. เลขที่ตำแหน่ง -->
                                <td class="py-3 border-bottom">
                                    <div class="font-weight-bold text-dark" style="font-size: 1.05rem;"><?= h($mp['position_number']) ?></div>
                                    <small class="text-muted"><?= h($mp['employee_type'] ?? '') ?></small>
                                </td>

                                <!-- 2. ชื่อ - สกุล -->
                                <td class="py-3 border-bottom">
                                    <?php 
                                        $has_person = false; $emp_full_name = ""; $emp_id_link = ""; $emp_avatar = "";
                                        
                                        if (!$is_dept_view && isset($employee) && !empty($employee)) {
                                            $has_person = true; $emp_full_name = h($employee['prefix'] ?? '') . h($employee['first_name'] ?? '') . ' ' . h($employee['last_name'] ?? '');
                                            $emp_id_link = h($employee['id'] ?? ''); $emp_avatar = $employee['avatar'] ?? '';
                                        } elseif ($is_dept_view && !empty($mp['emp_first_name'])) {
                                            $has_person = true; $emp_full_name = h($mp['emp_prefix'] ?? '') . h($mp['emp_first_name'] ?? '') . ' ' . h($mp['emp_last_name'] ?? '');
                                            $emp_id_link = h($mp['emp_id'] ?? ''); $emp_avatar = $mp['emp_avatar'] ?? '';
                                        }

                                        if (!$has_person && $mp['status'] == 'occupied' && !empty($mp['position_number'])) {
                                            if (isset($db)) {
                                                try {
                                                    $stmtEmp = $db->prepare("SELECT id, prefix, first_name, last_name, avatar FROM employees WHERE emp_code = :pos LIMIT 1");
                                                    $stmtEmp->execute([':pos' => $mp['position_number']]); $fetchEmp = $stmtEmp->fetch(PDO::FETCH_ASSOC);
                                                    if ($fetchEmp) {
                                                        $has_person = true; $emp_full_name = h($fetchEmp['prefix'] ?? '') . h($fetchEmp['first_name'] ?? '') . ' ' . h($fetchEmp['last_name'] ?? '');
                                                        $emp_id_link = h($fetchEmp['id'] ?? ''); $emp_avatar = $fetchEmp['avatar'] ?? '';
                                                    }
                                                } catch (Exception $e) {}
                                            }
                                        }
                                    ?>
                                    <?php if($has_person): ?>
                                        <div class="d-flex align-items-center">
                                            <div class="me-2">
                                                <?php if(!empty($emp_avatar) && file_exists("uploads/" . $emp_avatar)): ?>
                                                    <img src="uploads/<?= h($emp_avatar) ?>" alt="Avatar" class="rounded-circle object-fit-cover border shadow-sm" width="38" height="38">
                                                <?php else: ?>
                                                    <div class="bg-teal bg-opacity-10 text-teal rounded-circle d-flex justify-content-center align-items-center border" style="width: 38px; height: 38px; font-weight:bold; font-size: 0.9rem; border-color: rgba(13,148,136,0.2) !important;">
                                                        <?= mb_substr(str_replace(['นาย','นางสาว','นาง','ว่าที่ ร.ต.'], '', $emp_full_name), 0, 1, 'UTF-8') ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="line-height: 1.2;"><?= $emp_full_name ?></div>
                                                <a href="index.php?action=show&id=<?= $emp_id_link ?>" class="text-teal small text-decoration-none hover-underline"><i class="fas fa-address-card me-1"></i>ก.พ.7</a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="d-flex align-items-center text-muted opacity-75">
                                            <div class="bg-light rounded-circle d-flex justify-content-center align-items-center border me-2" style="width: 38px; height: 38px;"><i class="fas fa-user-slash fa-xs"></i></div>
                                            <span class="small font-italic"><?= ($mp['status'] == 'vacant') ? '(ว่าง)' : 'ข้อมูลไม่สมบูรณ์' ?></span>
                                        </div>
                                    <?php endif; ?>
                                </td>

                                <!-- 3. ตำแหน่ง -->
                                <td class="py-3 border-bottom">
                                    <div class="font-weight-bold text-dark" style="color: #0d9488 !important;"><?= h($mp['position_name']) ?></div>
                                </td>

                                <!-- 4. ระดับ -->
                                <td class="py-3 border-bottom">
                                    <span class="badge border font-weight-normal text-secondary bg-white px-2 py-1"><?= h($mp['level'] ?? '-') ?></span>
                                </td>

                                <!-- 5. กลุ่มงาน -->
                                <td class="py-3 border-bottom">
                                    <div class="text-muted small"><?= h($mp['division'] ?: '-') ?></div>
                                </td>

                                <!-- 6. หมายเหตุ -->
                                <td class="py-3 border-bottom">
                                    <div class="text-truncate small text-muted" style="max-width: 120px;" title="<?= h($mp['remark']) ?>">
                                        <?= h($mp['remark'] ?: '-') ?>
                                    </div>
                                </td>

                                <!-- 7. จัดการข้อมูล -->
                                <td class="py-3 pr-4 border-bottom text-center">
                                    <div class="btn-group shadow-sm border rounded overflow-hidden">
                                        <!-- ปุ่มแก้ไข (นำ salary ออกแล้ว) -->
                                        <button type="button" class="btn btn-sm btn-white text-primary border-0" title="แก้ไข"
                                            data-bs-toggle="modal" data-bs-target="#editManpowerModal"
                                            data-id="<?= h($mp['id']) ?>" 
                                            data-type="<?= h($mp['employee_type']) ?>" 
                                            data-number="<?= h($mp['position_number']) ?>"
                                            data-name="<?= h($mp['position_name']) ?>" 
                                            data-level="<?= h($mp['level']) ?>" 
                                            data-dept="<?= h($mp['department']) ?>"
                                            data-div="<?= h($mp['division']) ?>" 
                                            data-status="<?= h($mp['status']) ?>" 
                                            data-remark="<?= h($mp['remark']) ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <!-- ปุ่มลบ -->
                                        <?php if($mp['status'] == 'vacant'): ?>
                                            <button type="button" class="btn btn-sm btn-white text-danger border-0 btn-delete-mp" title="ลบ" data-id="<?= h($mp['id']) ?>" data-num="<?= h($mp['position_number']) ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="btn btn-sm btn-white text-muted border-0" disabled title="ไม่อนุญาตให้ลบเมื่อมีผู้ครองตำแหน่ง">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Popup แจ้งเตือนเวลาลากบันทึกเสร็จ (Toast) -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
  <div id="sortToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body fw-bold">
        <i class="fas fa-check-circle me-2"></i> บันทึกลำดับใหม่เรียบร้อยแล้ว
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<!-- ============================================== -->
<!-- 🌟 Modal แก้ไขตำแหน่ง (Popup) 🌟 -->
<!-- ============================================== -->
<div class="modal fade" id="editManpowerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold text-dark">
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-25 text-warning rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 42px; height: 42px;"><i class="fas fa-edit"></i></div>
                        <div>แก้ไขข้อมูลกรอบอัตรากำลัง<br><small class="text-muted font-weight-normal" style="font-size:0.85rem;">ปรับปรุงรายละเอียดตำแหน่งและสังกัด</small></div>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=manpower_update" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="return_url" value="manpower_detail&<?= $return_url_param ?>">
                
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">เลขที่ตำแหน่ง</label>
                            <input type="text" name="position_number" id="edit_position_number" class="form-control font-weight-bold bg-light" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ชื่อตำแหน่ง <span class="text-danger">*</span></label>
                            <input type="text" name="position_name" id="edit_position_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ประเภทบุคลากร <span class="text-danger">*</span></label>
                            <select name="employee_type" id="edit_employee_type" class="form-select" required>
                                <option value="ข้าราชการ อบจ.">ข้าราชการ อบจ.</option>
                                <option value="ข้าราชการครู อบจ.">ข้าราชการครู อบจ.</option>
                                <option value="ลูกจ้างประจำ">ลูกจ้างประจำ</option>
                                <option value="พนักงานจ้างตามภารกิจ">พนักงานจ้างตามภารกิจ</option>
                                <option value="พนักงานจ้างทั่วไป">พนักงานจ้างทั่วไป</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                            <select name="level" id="edit_level" class="form-select" required>
                                <?php if(!empty($position_levels)): foreach($position_levels as $lvl): ?>
                                    <option value="<?= h($lvl['name']) ?>"><?= h($lvl['name']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ส่วนราชการ <span class="text-danger">*</span></label>
                            <select name="department" id="edit_department" class="form-select" required>
                                <?php if(!empty($departments)): foreach($departments as $dept): ?>
                                    <option value="<?= h($dept['name']) ?>"><?= h($dept['name']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">กลุ่มงาน (ฝ่าย/ส่วน/งาน)</label>
                            <input type="text" name="division" id="edit_division" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary small">สถานะการครองตำแหน่ง <span class="text-danger">*</span></label>
                            <select name="status" id="edit_status" class="form-select fw-bold" required onchange="updateStatusColor(this)">
                                <option value="vacant">ว่าง (รอสรรหา)</option>
                                <option value="occupied">มีคนครอง</option>
                            </select>
                        </div>
                        <!-- ลบช่องเงินเดือนออกเรียบร้อยแล้ว -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-secondary small">หมายเหตุ</label>
                            <textarea name="remark" id="edit_remark" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary font-weight-bold px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-warning font-weight-bold text-dark px-4 shadow-sm"><i class="fas fa-save me-1"></i> บันทึกการแก้ไข</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form ซ่อนสำหรับระบบลบ -->
<form id="deleteManpowerForm" action="index.php?action=manpower_delete" method="POST" style="display: none;">
    <input type="hidden" name="id" id="delete_id">
    <input type="hidden" name="return_url" value="manpower_detail&<?= $return_url_param ?>">
</form>

<script>
/**
 * อัปเดตสีของ Select Status ตามค่าที่เลือก
 */
function updateStatusColor(el) {
    if (el.value === 'vacant') {
        el.className = 'form-select fw-bold border-success text-success bg-success bg-opacity-10';
    } else {
        el.className = 'form-select fw-bold border-secondary text-secondary bg-light';
    }
}

document.addEventListener('DOMContentLoaded', function() {
    
    // 🌟 1. ระบบลากวาง (Drag & Drop Reordering) ด้วย SortableJS 🌟
    const tbody = document.getElementById('sortable-tbody');
    if (tbody && !document.getElementById('emptyRow')) {
        new Sortable(tbody, {
            handle: '.drag-handle', // จับได้เฉพาะตรงไอคอน
            animation: 150,         // ความสมูทตอนสลับที่ (ms)
            ghostClass: 'sortable-ghost',
            onEnd: function (evt) {
                // ดึง ID เรียงตามลำดับใหม่
                let orderedIds = [];
                document.querySelectorAll('#sortable-tbody tr.searchable-row').forEach((row) => {
                    orderedIds.push(row.getAttribute('data-id'));
                });

                // ส่งไปบันทึกด้วย AJAX
                fetch('index.php?action=manpower_reorder', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ order: orderedIds })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        var toastEl = document.getElementById('sortToast');
                        var toast = new bootstrap.Toast(toastEl, { delay: 2000 });
                        toast.show();
                    } else {
                        console.error('Error saving order:', data.error);
                    }
                })
                .catch(error => console.error('Fetch error:', error));
            }
        });
    }

    // 2. ระบบค้นหา Real-time ในตาราง
    const searchInput = document.getElementById('quickSearch');
    if(searchInput) {
        searchInput.addEventListener('keyup', function() {
            const term = this.value.toLowerCase().trim();
            let visibleCount = 0;
            const rows = document.querySelectorAll('.searchable-row');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isMatch = text.includes(term);
                row.style.display = isMatch ? '' : 'none';
                if(isMatch) visibleCount++;
            });

            let emptyMsg = document.getElementById('searchEmptyRow');
            if(visibleCount === 0 && rows.length > 0) {
                if(!emptyMsg) {
                    const tb = document.querySelector('#positionsTable tbody');
                    const tr = document.createElement('tr');
                    tr.id = 'searchEmptyRow';
                    tr.innerHTML = `<td colspan="8" class="text-center py-5 text-muted"><i class="fas fa-search fa-2x mb-3 opacity-25"></i><br>ไม่พบข้อมูลที่ตรงกับการค้นหา: "${this.value}"</td>`;
                    tb.appendChild(tr);
                } else {
                    emptyMsg.style.display = '';
                    emptyMsg.querySelector('td').innerHTML = `<i class="fas fa-search fa-2x mb-3 opacity-25"></i><br>ไม่พบข้อมูลที่ตรงกับการค้นหา: "${this.value}"`;
                }
            } else if(emptyMsg) {
                emptyMsg.style.display = 'none';
            }
        });
    }

    // 3. ระบบจัดการข้อมูลใส่ Modal แก้ไข
    const editModal = document.getElementById('editManpowerModal');
    if (editModal) {
        editModal.addEventListener('show.bs.modal', function (event) {
            const btn = event.relatedTarget;
            document.getElementById('edit_id').value = btn.getAttribute('data-id');
            document.getElementById('edit_employee_type').value = btn.getAttribute('data-type');
            document.getElementById('edit_position_number').value = btn.getAttribute('data-number');
            document.getElementById('edit_position_name').value = btn.getAttribute('data-name');
            document.getElementById('edit_level').value = btn.getAttribute('data-level');
            document.getElementById('edit_department').value = btn.getAttribute('data-dept');
            document.getElementById('edit_division').value = btn.getAttribute('data-div');
            // ไม่มีการอ้างอิง salary แล้ว
            document.getElementById('edit_remark').value = btn.getAttribute('data-remark');
            
            const statusField = document.getElementById('edit_status');
            statusField.value = btn.getAttribute('data-status');
            updateStatusColor(statusField);
        });
    }

    // 4. ระบบยืนยันการลบตำแหน่ง
    document.querySelectorAll('.btn-delete-mp').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const num = this.getAttribute('data-num');
            if(confirm('⚠️ ยืนยันการลบตำแหน่งเลขที่ ' + num + ' ?\n\nเมื่อลบแล้วข้อมูลจะหายจากระบบถาวรและไม่สามารถกู้คืนได้')) {
                document.getElementById('delete_id').value = id;
                document.getElementById('deleteManpowerForm').submit();
            }
        });
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>