<?php include 'views/layout/header.php'; ?>

<div class="container-fluid mb-5">
    <style>
        .text-teal { color: #0d9488 !important; }
        .bg-teal { background-color: #0d9488 !important; }
        
        .summary-card {
            border-radius: 12px; border: 1px solid #e2e8f0; background: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05); transition: transform 0.2s;
        }
        .summary-card:hover { transform: translateY(-3px); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        
        .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-modern thead th {
            background-color: #f8fafc; color: #475569; font-weight: 700;
            border-bottom: 2px solid #e2e8f0; padding: 12px 15px; font-size: 0.9rem;
        }
        .table-modern tbody tr { background-color: #fff; transition: all 0.2s; }
        .table-modern tbody tr:hover { background-color: #f1f5f9; }
        .table-modern tbody td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid #e2e8f0; }
        
        .badge-type-central { background-color: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; }
        .badge-type-school { background-color: #dcfce7; color: #16a34a; border: 1px solid #bbf7d0; }
        .badge-type-health { background-color: #fef3c7; color: #d97706; border: 1px solid #fde68a; }
    </style>

    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">
                <i class="fas fa-building text-teal mr-2"></i> จัดการส่วนราชการ / หน่วยงาน
            </h2>
            <p class="text-muted mb-0">ตั้งค่าโครงสร้างหน่วยงานของ อบจ.</p>
        </div>
        <div>
            <a href="index.php?action=department_create" class="btn text-white font-weight-bold shadow-sm px-4 py-2" style="background-color: #0d9488; border-radius: 8px;">
                <i class="fas fa-plus mr-1"></i> เพิ่มหน่วยงานใหม่
            </a>
        </div>
    </div>

    <!-- Alert Messages -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= $_SESSION['message_type'] ?> alert-dismissible fade show shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fas <?= $_SESSION['message_type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?> mr-2"></i>
            <?= $_SESSION['message'] ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="summary-card h-100 py-3 border-left-secondary">
                <div class="card-body text-center">
                    <h6 class="font-weight-bold text-secondary mb-2">หน่วยงานทั้งหมด</h6>
                    <div class="h3 font-weight-bold text-dark mb-0"><?= number_format($total ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="summary-card h-100 py-3" style="border-left: 4px solid #0284c7;">
                <div class="card-body text-center">
                    <h6 class="font-weight-bold mb-2" style="color: #0284c7;">ส่วนราชการส่วนกลาง</h6>
                    <div class="h3 font-weight-bold text-dark mb-0"><?= number_format($count_central ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="summary-card h-100 py-3" style="border-left: 4px solid #16a34a;">
                <div class="card-body text-center">
                    <h6 class="font-weight-bold mb-2" style="color: #16a34a;">สังกัดโรงเรียน</h6>
                    <div class="h3 font-weight-bold text-dark mb-0"><?= number_format($count_school ?? 0) ?></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="summary-card h-100 py-3" style="border-left: 4px solid #d97706;">
                <div class="card-body text-center">
                    <h6 class="font-weight-bold mb-2" style="color: #d97706;">รพ.สต.</h6>
                    <div class="h3 font-weight-bold text-dark mb-0"><?= number_format($count_health ?? 0) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-modern" id="dataTableDept">
                    <thead>
                        <tr>
                            <th class="text-center" width="5%">#</th>
                            <th width="15%">ประเภทหน่วยงาน</th>
                            <th width="40%">ชื่อหน่วยงาน</th>
                            <th width="15%">สังกัดอำเภอ</th>
                            <th class="text-center" width="10%">สถานะ</th>
                            <th class="text-center" width="15%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($departments)): ?>
                            <?php foreach($departments as $index => $row): 
                                // จัดการแสดงผล Badge ประเภท
                                $typeBadge = '';
                                $typeText = '';
                                if($row['dept_type'] == 'central') { $typeBadge = 'badge-type-central'; $typeText = '<i class="fas fa-building mr-1"></i> ส่วนกลาง'; }
                                elseif($row['dept_type'] == 'school') { $typeBadge = 'badge-type-school'; $typeText = '<i class="fas fa-school mr-1"></i> โรงเรียน'; }
                                elseif($row['dept_type'] == 'health') { $typeBadge = 'badge-type-health'; $typeText = '<i class="fas fa-hospital mr-1"></i> รพ.สต.'; }
                            ?>
                            <tr>
                                <td class="text-center text-muted"><?= $index + 1 ?></td>
                                <td><span class="badge px-3 py-2 rounded-pill <?= $typeBadge ?>"><?= $typeText ?></span></td>
                                <td class="font-weight-bold text-dark" style="font-size: 1.05rem;"><?= htmlspecialchars($row['name']) ?></td>
                                <td>
                                    <?php if($row['dept_type'] == 'health' && !empty($row['amphoe'])): ?>
                                        <span class="text-secondary"><i class="fas fa-map-marker-alt text-danger mr-1"></i> อ.<?= htmlspecialchars($row['amphoe']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if($row['status'] == 'active'): ?>
                                        <span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> เปิดใช้งาน</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary px-2 py-1"><i class="fas fa-ban"></i> ปิดใช้งาน</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="index.php?action=department_edit&id=<?= $row['id'] ?>" class="btn btn-sm btn-light border text-primary shadow-sm" title="แก้ไข">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="index.php?action=department_delete&id=<?= $row['id'] ?>" class="btn btn-sm btn-light border text-danger shadow-sm" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบหน่วยงานนี้?');" title="ลบ">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center py-5 text-muted">ไม่พบข้อมูลหน่วยงาน</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var checkJq = setInterval(function() {
        if (window.jQuery && window.jQuery.fn.DataTable) {
            clearInterval(checkJq);
            window.jQuery('#dataTableDept').DataTable({
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json" },
                "pageLength": 25
            });
        }
    }, 100);
});
</script>

<?php include 'views/layout/footer.php'; ?>