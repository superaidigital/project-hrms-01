<?php include 'views/layout/header.php'; ?>

<?php
// =========================================================
// ลอจิกคัดแยกกลุ่มหน่วยงาน (จัดกลุ่มสำหรับแสดงผลในตาราง)
// =========================================================
$central = [];
$school = [];
$health_grouped = []; // สำหรับแยก รพ.สต. ตามอำเภอ

$sumC_total = 0; $sumC_occ = 0; $sumC_vac = 0;
$sumS_total = 0; $sumS_occ = 0; $sumS_vac = 0;
$sumH_total = 0; $sumH_occ = 0; $sumH_vac = 0;

if(!empty($categories)) {
    foreach($categories as $deptName => $data) {
        $isSchool = preg_match('/โรงเรียน|วิทยาลัย/u', $deptName);
        $isHealth = preg_match('/รพ\.สต\.|โรงพยาบาล|อนามัย|สาธารณสุข/u', $deptName);

        if($isSchool) {
            $school[$deptName] = $data;
            $sumS_total += $data['total'];
            $sumS_occ += $data['occupied'];
            $sumS_vac += $data['vacant'];
        } elseif($isHealth) {
            $amphoe = 'อำเภออื่นๆ';
            // ดึงชื่ออำเภอออกมา
            if (preg_match('/(อำเภอ|อ\.)\s*([ก-๙a-zA-Z0-9]+)/u', $deptName, $matches)) {
                $name = trim($matches[2]);
                if($name == 'เมืองศรีสะเกษ' || $name == 'เมือง') {
                    $name = 'เมืองศรีสะเกษ'; 
                }
                $amphoe = 'อำเภอ' . $name;
            }
            if(!isset($health_grouped[$amphoe])) {
                $health_grouped[$amphoe] = [];
            }
            $health_grouped[$amphoe][$deptName] = $data;
            $sumH_total += $data['total'];
            $sumH_occ += $data['occupied'];
            $sumH_vac += $data['vacant'];
        } else {
            $central[$deptName] = $data;
            $sumC_total += $data['total'];
            $sumC_occ += $data['occupied'];
            $sumC_vac += $data['vacant'];
        }
    }
    // เรียงลำดับชื่ออำเภอ
    ksort($health_grouped);
}
?>

<div class="container-fluid mb-5">
    <style>
        /* ==========================================
           สไตล์ UI สำหรับหน้าจอ Index (Modern Design)
           ========================================== */
        .text-teal { color: #0d9488 !important; }
        .bg-teal { background-color: #0d9488 !important; }
        
        /* สไตล์การ์ดสรุปผลด้านบน */
        .summary-card {
            border-radius: 16px; border: 2px solid transparent; background: #fff;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            transition: transform 0.2s;
        }
        .summary-card:hover { transform: translateY(-3px); }
        .card-border-blue { border-color: #3b82f6; }
        .card-border-green { border-color: #10b981; }
        .card-border-red { border-color: #ef4444; }
        
        /* สไตล์ช่องค้นหา */
        .search-wrapper { position: relative; width: 100%; max-width: 350px; }
        .search-wrapper .form-control {
            border-radius: 30px; padding-left: 20px; padding-right: 45px;
            border: 1px solid #e3e6f0; background-color: #f8f9fc; height: 40px; font-size: 0.9rem;
        }
        .search-wrapper .form-control:focus {
            background-color: #fff; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15); border-color: #0d9488; outline: none;
        }
        .search-wrapper .btn-search {
            position: absolute; right: 4px; top: 3px; height: 34px; width: 34px;
            border-radius: 50%; background: #0d9488; color: white; border: none;
            display: flex; align-items: center; justify-content: center; pointer-events: none;
        }
        
        /* สไตล์ตารางจัดกลุ่มแบบใหม่ */
        .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-modern thead th {
            background-color: #fff; color: #333; font-weight: 700;
            border-bottom: 2px solid #edf2f7; padding: 15px 10px; font-size: 0.9rem;
        }
        .table-modern tbody tr { background-color: #fff; transition: background-color 0.2s; border-bottom: 1px solid #edf2f7; }
        .table-modern tbody tr.data-item-row:hover { background-color: #f8fafc; }
        .table-modern tbody td { padding: 12px 10px; vertical-align: middle; border-bottom: 1px solid #edf2f7; }
        
        /* ไฮไลต์แถวจัดกลุ่ม */
        .group-header td { background-color: #f1f5f9; border-top: 2px solid #e2e8f0; }
        .subgroup-header td { background-color: #f8fafc; border-top: 1px solid #e2e8f0; }
        .cell-vacant-highlight { background-color: #fef2f2 !important; }
        
        /* ปุ่มรายละเอียด */
        .btn-detail {
            background-color: #fff; color: #0d9488; border: 1px solid #e2e8f0;
            border-radius: 6px; font-weight: 600; padding: 4px 12px; font-size: 0.85rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05); transition: all 0.2s;
        }
        .btn-detail:hover { background-color: #f0fdfa; border-color: #ccfbf1; color: #0f766e; text-decoration: none; }
        
        /* ปุ่มสลับมุมมอง (Toggle View) */
        .view-toggle .btn { border: 1px solid #e2e8f0; color: #64748b; font-weight: 600; background: #fff; }
        .view-toggle .btn.active { background-color: #0d9488; color: #fff; border-color: #0d9488; }
        .view-toggle .btn:hover:not(.active) { background-color: #f8fafc; color: #0d9488; }
    </style>

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1">
                <i class="fas fa-sitemap text-teal mr-2"></i> กรอบอัตรากำลัง อบจ.
            </h2>
            <p class="text-muted mb-0">ภาพรวมการจัดสรรอัตรากำลังและตำแหน่งที่ว่าง (แยกตามหน่วยงาน)</p>
        </div>
        <div>
            <a href="index.php?action=manpower_create" class="btn text-white font-weight-bold shadow-sm px-4 py-2" style="background-color: #0d9488; border-radius: 8px;">
                <i class="fas fa-plus mr-1"></i> เพิ่มตำแหน่งใหม่
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-4 mb-3">
            <div class="summary-card card-border-blue h-100 py-3">
                <div class="card-body text-center">
                    <h6 class="font-weight-bold text-dark mb-3">กรอบทั้งหมด</h6>
                    <div class="display-4 font-weight-bold" style="color: #3b82f6;">
                        <?= number_format($totalRequired ?? 0) ?> <span class="h6 text-dark font-weight-normal">อัตรา</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-3">
            <div class="summary-card card-border-green h-100 py-3">
                <div class="card-body text-center">
                    <h6 class="font-weight-bold text-dark mb-3">คนครอง (มีตัวบุคคล)</h6>
                    <div class="display-4 font-weight-bold" style="color: #10b981;">
                        <?= number_format($totalOccupied ?? 0) ?> <span class="h6 text-dark font-weight-normal">คน</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4 col-md-4 mb-3">
            <div class="summary-card card-border-red h-100 py-3">
                <div class="card-body text-center">
                    <h6 class="font-weight-bold text-dark mb-3">อัตราว่าง (รอสรรหา)</h6>
                    <div class="display-4 font-weight-bold" style="color: #ef4444;">
                        <?= number_format($totalVacant ?? 0) ?> <span class="h6 text-dark font-weight-normal">อัตรา</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Title & Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3">
        <h5 class="font-weight-bold text-dark mb-3 mb-md-0">ข้อมูลแยกตามส่วนราชการ / หน่วยงาน</h5>
        
        <div class="d-flex flex-column flex-md-row gap-2 align-items-center w-100" style="max-width: 500px; justify-content: flex-end;">
            <!-- ช่องค้นหา -->
            <div class="search-wrapper flex-grow-1 mr-md-2 mb-2 mb-md-0">
                <input type="text" class="form-control" id="searchDeptInput" placeholder="ค้นหาหน่วยงาน หรือ อำเภอ..." aria-label="Search">
                <button class="btn-search" type="button"><i class="fas fa-search fa-sm"></i></button>
            </div>
            
            <!-- ปุ่มสลับมุมมอง -->
            <div class="btn-group view-toggle shadow-sm" role="group">
                <button type="button" class="btn btn-sm active" id="btnTableView">
                    <i class="fas fa-list mr-1"></i> แบบตาราง
                </button>
                <button type="button" class="btn btn-sm" id="btnCardView">
                    <i class="fas fa-th-large mr-1"></i> แบบการ์ด
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- มุมมองแบบตารางจัดกลุ่ม (Hierarchical Table View) -->
    <!-- ============================================== -->
    <div class="card shadow-sm border-0 mb-5" id="tableViewContainer" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-modern" id="mainDeptTable">
                    <thead>
                        <tr>
                            <th class="pl-4">ส่วนราชการ / หน่วยงาน</th>
                            <th class="text-center" width="15%">กรอบทั้งหมด (อัตรา)</th>
                            <th class="text-center" width="15%">มีคนครอง (อัตรา)</th>
                            <th class="text-center" width="15%">อัตราว่าง (อัตรา)</th>
                            <th class="text-center pr-4" width="15%">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <!-- ================= กลุ่ม: ส่วนกลาง ================= -->
                        <tr class="group-header" data-group="central">
                            <td class="font-weight-bold text-primary pl-4" style="font-size: 1.05rem;">
                                <i class="fas fa-building mr-2"></i> ส่วนกลาง
                            </td>
                            <td class="text-center font-weight-bold text-primary"><?= number_format($sumC_total) ?></td>
                            <td class="text-center font-weight-bold text-primary"><?= number_format($sumC_occ) ?></td>
                            <td class="text-center font-weight-bold <?= $sumC_vac > 0 ? 'text-danger' : 'text-primary' ?>"><?= number_format($sumC_vac) ?></td>
                            <td></td>
                        </tr>
                        <?php if(empty($central)): ?>
                            <tr class="data-item-row" data-group="central"><td colspan="5" class="text-center text-muted py-3">ไม่มีข้อมูลส่วนกลาง</td></tr>
                        <?php else: ?>
                            <?php foreach($central as $deptName => $data): 
                                $vacantBgClass = $data['vacant'] > 0 ? 'cell-vacant-highlight text-danger font-weight-bold' : 'text-muted';
                                $actualClass = $data['occupied'] > 0 ? 'text-teal font-weight-bold' : 'text-muted';
                                $detail_url = 'index.php?action=manpower_detail&dept=' . urlencode($deptName);
                            ?>
                            <tr class="data-item-row" data-group="central">
                                <td class="pl-5">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-angle-right text-gray-400 mr-3"></i>
                                        <a href="<?= $detail_url ?>" class="text-dark font-weight-bold text-decoration-none search-target">
                                            <?= htmlspecialchars($deptName) ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center font-weight-bold text-dark"><?= number_format($data['total']) ?></td>
                                <td class="text-center <?= $actualClass ?>"><?= number_format($data['occupied']) ?></td>
                                <td class="text-center <?= $vacantBgClass ?>"><?= number_format($data['vacant']) ?></td>
                                <td class="text-center pr-4">
                                    <a href="<?= $detail_url ?>" class="btn-detail d-inline-block">รายละเอียด</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>


                        <!-- ================= กลุ่ม: สังกัดโรงเรียน ================= -->
                        <tr class="group-header" data-group="school">
                            <td class="font-weight-bold text-success pl-4" style="font-size: 1.05rem;">
                                <i class="fas fa-school mr-2"></i> สังกัดโรงเรียน
                            </td>
                            <td class="text-center font-weight-bold text-success"><?= number_format($sumS_total) ?></td>
                            <td class="text-center font-weight-bold text-success"><?= number_format($sumS_occ) ?></td>
                            <td class="text-center font-weight-bold <?= $sumS_vac > 0 ? 'text-danger' : 'text-success' ?>"><?= number_format($sumS_vac) ?></td>
                            <td></td>
                        </tr>
                        <?php if(empty($school)): ?>
                            <tr class="data-item-row" data-group="school"><td colspan="5" class="text-center text-muted py-3">ไม่มีข้อมูลสังกัดโรงเรียน</td></tr>
                        <?php else: ?>
                            <?php foreach($school as $deptName => $data): 
                                $vacantBgClass = $data['vacant'] > 0 ? 'cell-vacant-highlight text-danger font-weight-bold' : 'text-muted';
                                $actualClass = $data['occupied'] > 0 ? 'text-teal font-weight-bold' : 'text-muted';
                                $detail_url = 'index.php?action=manpower_detail&dept=' . urlencode($deptName);
                            ?>
                            <tr class="data-item-row" data-group="school">
                                <td class="pl-5">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-angle-right text-gray-400 mr-3"></i>
                                        <a href="<?= $detail_url ?>" class="text-dark font-weight-bold text-decoration-none search-target">
                                            <?= htmlspecialchars($deptName) ?>
                                        </a>
                                    </div>
                                </td>
                                <td class="text-center font-weight-bold text-dark"><?= number_format($data['total']) ?></td>
                                <td class="text-center <?= $actualClass ?>"><?= number_format($data['occupied']) ?></td>
                                <td class="text-center <?= $vacantBgClass ?>"><?= number_format($data['vacant']) ?></td>
                                <td class="text-center pr-4">
                                    <a href="<?= $detail_url ?>" class="btn-detail d-inline-block">รายละเอียด</a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>


                        <!-- ================= กลุ่ม: รพ.สต. (แยกรายอำเภอ) ================= -->
                        <tr class="group-header" data-group="health">
                            <td class="font-weight-bold text-info pl-4" style="font-size: 1.05rem;">
                                <i class="fas fa-hospital mr-2"></i> รพ.สต. (แยกเป็นอำเภอ)
                            </td>
                            <td class="text-center font-weight-bold text-info"><?= number_format($sumH_total) ?></td>
                            <td class="text-center font-weight-bold text-info"><?= number_format($sumH_occ) ?></td>
                            <td class="text-center font-weight-bold <?= $sumH_vac > 0 ? 'text-danger' : 'text-info' ?>"><?= number_format($sumH_vac) ?></td>
                            <td></td>
                        </tr>
                        <?php if(empty($health_grouped)): ?>
                            <tr class="data-item-row" data-group="health"><td colspan="5" class="text-center text-muted py-3">ไม่มีข้อมูล รพ.สต.</td></tr>
                        <?php else: ?>
                            <?php foreach($health_grouped as $amphoe => $h_data): 
                                $sumA_total = 0; $sumA_occ = 0; $sumA_vac = 0;
                                foreach($h_data as $h) { $sumA_total += $h['total']; $sumA_occ += $h['occupied']; $sumA_vac += $h['vacant']; }
                            ?>
                                <!-- Header อำเภอ -->
                                <tr class="subgroup-header" data-group="health" data-subgroup="<?= htmlspecialchars($amphoe) ?>">
                                    <td class="font-weight-bold text-dark pl-5 search-target">
                                        <i class="fas fa-map-marker-alt text-danger mr-2"></i> <?= htmlspecialchars($amphoe) ?>
                                    </td>
                                    <td class="text-center font-weight-bold text-secondary"><?= number_format($sumA_total) ?></td>
                                    <td class="text-center font-weight-bold text-secondary"><?= number_format($sumA_occ) ?></td>
                                    <td class="text-center font-weight-bold text-secondary"><?= number_format($sumA_vac) ?></td>
                                    <td></td>
                                </tr>
                                
                                <!-- ข้อมูล รพ.สต. ใต้อำเภอ -->
                                <?php foreach($h_data as $deptName => $data): 
                                    $vacantBgClass = $data['vacant'] > 0 ? 'cell-vacant-highlight text-danger font-weight-bold' : 'text-muted';
                                    $actualClass = $data['occupied'] > 0 ? 'text-teal font-weight-bold' : 'text-muted';
                                    $detail_url = 'index.php?action=manpower_detail&dept=' . urlencode($deptName);
                                ?>
                                <tr class="data-item-row" data-group="health" data-subgroup="<?= htmlspecialchars($amphoe) ?>">
                                    <td style="padding-left: 4.5rem;">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-angle-right text-gray-300 mr-2" style="font-size: 0.8rem;"></i>
                                            <a href="<?= $detail_url ?>" class="text-dark font-weight-bold text-decoration-none search-target">
                                                <?= htmlspecialchars($deptName) ?>
                                            </a>
                                        </div>
                                    </td>
                                    <td class="text-center font-weight-bold text-dark"><?= number_format($data['total']) ?></td>
                                    <td class="text-center <?= $actualClass ?>"><?= number_format($data['occupied']) ?></td>
                                    <td class="text-center <?= $vacantBgClass ?>"><?= number_format($data['vacant']) ?></td>
                                    <td class="text-center pr-4">
                                        <a href="<?= $detail_url ?>" class="btn-detail d-inline-block">รายละเอียด</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                
                            <?php endforeach; ?>
                        <?php endif; ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- มุมมองแบบการ์ด (Card View) พร้อมหัวข้อแบ่งกลุ่ม -->
    <!-- ============================================== -->
    <div class="row d-none" id="cardViewContainer">
        
        <!-- กลุ่มที่ 1: ส่วนกลาง -->
        <div class="col-12 mb-3 mt-2 card-group-header" data-group="central">
            <h5 class="font-weight-bold text-primary border-bottom pb-2"><i class="fas fa-building mr-2"></i> ส่วนกลาง</h5>
        </div>
        <?php foreach($central as $deptName => $data): 
            $detail_url = 'index.php?action=manpower_detail&dept=' . urlencode($deptName);
        ?>
        <div class="col-lg-4 col-md-6 mb-4 data-item-card" data-group="central">
            <div class="card shadow-sm h-100 border-0" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body">
                    <h6 class="font-weight-bold text-dark mb-3 search-target" style="line-height: 1.4;">
                        <i class="far fa-building text-teal mr-2"></i> <?= htmlspecialchars($deptName) ?>
                    </h6>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted small">กรอบทั้งหมด</span>
                        <span class="font-weight-bold text-dark"><?= number_format($data['total']) ?> อัตรา</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted small">มีคนครอง</span>
                        <span class="font-weight-bold text-teal"><?= number_format($data['occupied']) ?> คน</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted small">อัตราว่าง</span>
                        <span class="font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($data['vacant']) ?> อัตรา</span>
                    </div>
                    <a href="<?= $detail_url ?>" class="btn btn-block font-weight-bold" style="border-radius: 8px; border: 1px solid #ccfbf1; background-color: #f0fdfa; color: #0f766e;">
                        รายละเอียดเพิ่มเติม <i class="fas fa-arrow-right ml-1 fa-sm"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- กลุ่มที่ 2: สังกัดโรงเรียน -->
        <div class="col-12 mb-3 mt-3 card-group-header" data-group="school">
            <h5 class="font-weight-bold text-success border-bottom pb-2"><i class="fas fa-school mr-2"></i> สังกัดโรงเรียน</h5>
        </div>
        <?php foreach($school as $deptName => $data): 
            $detail_url = 'index.php?action=manpower_detail&dept=' . urlencode($deptName);
        ?>
        <div class="col-lg-4 col-md-6 mb-4 data-item-card" data-group="school">
            <div class="card shadow-sm h-100 border-0" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body">
                    <h6 class="font-weight-bold text-dark mb-3 search-target" style="line-height: 1.4;">
                        <i class="fas fa-school text-teal mr-2"></i> <?= htmlspecialchars($deptName) ?>
                    </h6>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted small">กรอบทั้งหมด</span>
                        <span class="font-weight-bold text-dark"><?= number_format($data['total']) ?> อัตรา</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                        <span class="text-muted small">มีคนครอง</span>
                        <span class="font-weight-bold text-teal"><?= number_format($data['occupied']) ?> คน</span>
                    </div>
                    <div class="d-flex justify-content-between mb-4">
                        <span class="text-muted small">อัตราว่าง</span>
                        <span class="font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($data['vacant']) ?> อัตรา</span>
                    </div>
                    <a href="<?= $detail_url ?>" class="btn btn-block font-weight-bold" style="border-radius: 8px; border: 1px solid #ccfbf1; background-color: #f0fdfa; color: #0f766e;">
                        รายละเอียดเพิ่มเติม <i class="fas fa-arrow-right ml-1 fa-sm"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- กลุ่มที่ 3: รพ.สต. (แยกอำเภอ) -->
        <div class="col-12 mb-3 mt-3 card-group-header" data-group="health">
            <h5 class="font-weight-bold text-info border-bottom pb-2"><i class="fas fa-hospital mr-2"></i> รพ.สต. (แยกเป็นอำเภอ)</h5>
        </div>
        <?php foreach($health_grouped as $amphoe => $h_data): ?>
            <div class="col-12 mb-2 card-group-header" data-group="health" data-subgroup="<?= htmlspecialchars($amphoe) ?>">
                <h6 class="font-weight-bold text-dark"><i class="fas fa-map-marker-alt text-danger mr-2"></i> <?= htmlspecialchars($amphoe) ?></h6>
            </div>
            <?php foreach($h_data as $deptName => $data): 
                $detail_url = 'index.php?action=manpower_detail&dept=' . urlencode($deptName);
            ?>
            <div class="col-lg-4 col-md-6 mb-4 data-item-card" data-group="health" data-subgroup="<?= htmlspecialchars($amphoe) ?>">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 12px; transition: transform 0.2s;">
                    <div class="card-body">
                        <h6 class="font-weight-bold text-dark mb-3 search-target" style="line-height: 1.4;">
                            <i class="fas fa-hospital-alt text-teal mr-2"></i> <?= htmlspecialchars($deptName) ?>
                        </h6>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted small">กรอบทั้งหมด</span>
                            <span class="font-weight-bold text-dark"><?= number_format($data['total']) ?> อัตรา</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                            <span class="text-muted small">มีคนครอง</span>
                            <span class="font-weight-bold text-teal"><?= number_format($data['occupied']) ?> คน</span>
                        </div>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="text-muted small">อัตราว่าง</span>
                            <span class="font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($data['vacant']) ?> อัตรา</span>
                        </div>
                        <a href="<?= $detail_url ?>" class="btn btn-block font-weight-bold" style="border-radius: 8px; border: 1px solid #ccfbf1; background-color: #f0fdfa; color: #0f766e;">
                            รายละเอียดเพิ่มเติม <i class="fas fa-arrow-right ml-1 fa-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        
    </div>

</div>

<!-- Scripts สำหรับจัดการ UI และค้นหาในระบบจัดกลุ่ม -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // 1. ระบบสลับมุมมอง (Toggle View)
    const btnTableView = document.getElementById('btnTableView');
    const btnCardView = document.getElementById('btnCardView');
    const tableViewContainer = document.getElementById('tableViewContainer');
    const cardViewContainer = document.getElementById('cardViewContainer');

    btnTableView.addEventListener('click', function() {
        btnTableView.classList.add('active');
        btnCardView.classList.remove('active');
        tableViewContainer.classList.remove('d-none');
        cardViewContainer.classList.add('d-none');
    });

    btnCardView.addEventListener('click', function() {
        btnCardView.classList.add('active');
        btnTableView.classList.remove('active');
        cardViewContainer.classList.remove('d-none');
        tableViewContainer.classList.add('d-none');
    });

    // 2. ระบบค้นหาอัจฉริยะ (จัดการการซ่อน/แสดงกลุ่มหลักและกลุ่มย่อยอัตโนมัติ)
    const searchDeptInput = document.getElementById('searchDeptInput');

    searchDeptInput.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();

        // ตัวแปรนับจำนวนการค้นพบในแต่ละกลุ่ม
        let groupVisible = { central: 0, school: 0, health: 0 };
        let subgroupVisible = {};

        // -------------------------
        // 2.1 ค้นหาในส่วนของ 'มุมมองตาราง'
        // -------------------------
        document.querySelectorAll('#mainDeptTable .data-item-row').forEach(row => {
            const target = row.querySelector('.search-target');
            const group = row.getAttribute('data-group');
            const subgroup = row.getAttribute('data-subgroup');

            if (target && target.textContent.toLowerCase().includes(term)) {
                row.style.display = '';
                if(group) groupVisible[group]++;
                if(subgroup) subgroupVisible[subgroup] = (subgroupVisible[subgroup] || 0) + 1;
            } else {
                row.style.display = 'none';
            }
        });

        // จัดการซ่อน/แสดงแถวหัวข้ออำเภอ (Subgroup)
        document.querySelectorAll('#mainDeptTable .subgroup-header').forEach(header => {
            const subgroup = header.getAttribute('data-subgroup');
            const target = header.querySelector('.search-target');
            const group = header.getAttribute('data-group');

            // ถ้าค้นหาด้วยชื่ออำเภอตรงๆ
            if (target && target.textContent.toLowerCase().includes(term)) {
                header.style.display = '';
                groupVisible[group]++;
                
                // ให้แสดง รพ.สต. ข้างในทั้งหมดด้วย
                document.querySelectorAll(`#mainDeptTable .data-item-row[data-subgroup="${subgroup}"]`).forEach(row => {
                    row.style.display = '';
                });
            } 
            // หรือถ้ามี รพ.สต. ข้างในที่ค้นเจอ
            else if (subgroupVisible[subgroup] > 0) {
                header.style.display = '';
            } else {
                header.style.display = 'none';
            }
        });

        // จัดการซ่อน/แสดงแถวหัวข้อหลัก (Group)
        document.querySelectorAll('#mainDeptTable .group-header').forEach(header => {
            const group = header.getAttribute('data-group');
            if (term === '' || groupVisible[group] > 0) {
                header.style.display = '';
            } else {
                header.style.display = 'none';
            }
        });

        // -------------------------
        // 2.2 ค้นหาในส่วนของ 'มุมมองการ์ด'
        // -------------------------
        document.querySelectorAll('.data-item-card').forEach(card => {
            const target = card.querySelector('.search-target');
            if (target && target.textContent.toLowerCase().includes(term)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
        
        // ซ่อน/แสดงหัวข้อกลุ่มในการ์ด
        document.querySelectorAll('.card-group-header').forEach(header => {
            const group = header.getAttribute('data-group');
            const subgroup = header.getAttribute('data-subgroup');
            let visibleCards = 0;
            
            if(subgroup) {
                visibleCards = document.querySelectorAll(`.data-item-card[data-subgroup="${subgroup}"]:not([style*="display: none"])`).length;
            } else {
                visibleCards = document.querySelectorAll(`.data-item-card[data-group="${group}"]:not([style*="display: none"])`).length;
            }

            if(visibleCards > 0 || term === '') {
                header.style.display = '';
            } else {
                header.style.display = 'none';
            }
        });

    });

});
</script>

<?php include 'views/layout/footer.php'; ?>