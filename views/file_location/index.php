<?php require_once 'views/layout/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-primary"><i class="fas fa-archive"></i> ศูนย์จัดการแฟ้มประวัติตัวจริง</h4>
            <a href="index.php?action=employees" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> กลับหน้าระบบบุคลากร</a>
        </div>
    </div>

    <div class="row">
        <?php if(empty($cabinets)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i><br>
                    ยังไม่มีข้อมูลตู้เก็บแฟ้มในระบบ กรุณาเข้าไปเพิ่มข้อมูล "สถานที่จัดเก็บแฟ้มประวัติ" ในหน้าแก้ไขประวัติบุคลากร
                </div>
            </div>
        <?php else: ?>
            <?php 
                // จัดกลุ่มข้อมูลตามตู้
                $groupedCabinets = [];
                foreach($cabinets as $cab) {
                    $groupedCabinets[$cab['cabinet_no']][] = $cab;
                }
            ?>
            
            <?php foreach($groupedCabinets as $cabinet_no => $shelves): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100 border-top-primary">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-dark"><i class="fas fa-cabinet-filing text-secondary"></i> ตู้: <?= htmlspecialchars($cabinet_no) ?></h5>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>ชั้นที่</th>
                                    <th class="text-center">จำนวนแฟ้ม</th>
                                    <th class="text-center">จัดการการพิมพ์</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($shelves as $shelf): ?>
                                <tr>
                                    <td class="align-middle fw-bold"><?= htmlspecialchars($shelf['shelf_no']) ?></td>
                                    <td class="align-middle text-center"><span class="badge bg-info fs-6"><?= $shelf['total_files'] ?> เล่ม</span></td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-print"></i> พิมพ์
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                                <li><h6 class="dropdown-header">พิมพ์สำหรับ ตู้ <?= htmlspecialchars($cabinet_no) ?> ชั้น <?= htmlspecialchars($shelf['shelf_no']) ?></h6></li>
                                                <li><a class="dropdown-item text-primary" href="index.php?action=file_print&type=label&cabinet=<?= urlencode($cabinet_no) ?>&shelf=<?= urlencode($shelf['shelf_no']) ?>" target="_blank"><i class="fas fa-tag"></i> ปริ้นเลขติดหน้าตู้/ชั้น</a></li>
                                                <li><a class="dropdown-item text-success" href="index.php?action=file_print&type=list&cabinet=<?= urlencode($cabinet_no) ?>&shelf=<?= urlencode($shelf['shelf_no']) ?>" target="_blank"><i class="fas fa-list-ol"></i> ปริ้นสารบัญรายชื่อ (A4)</a></li>
                                                <li><a class="dropdown-item text-warning text-dark" href="index.php?action=file_print&type=spine&cabinet=<?= urlencode($cabinet_no) ?>&shelf=<?= urlencode($shelf['shelf_no']) ?>" target="_blank"><i class="fas fa-sticky-note"></i> ปริ้นแถบชื่อติดสันแฟ้ม</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>