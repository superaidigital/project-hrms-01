<?php
// ==========================================
// ชื่อไฟล์: index.php
// ที่อยู่ไฟล์: views/manpower/index.php
// ==========================================

// 🚀 1. ระบบป้องกันการเข้าไฟล์โดยตรง (Auto-Redirect)
if (strpos($_SERVER['REQUEST_URI'], 'views/manpower/index.php') !== false) {
    header("Location: ../../index.php?action=manpower");
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

// =========================================================
// 📊 4. ลอจิกคำนวณและคัดแยกกลุ่มหน่วยงาน
// =========================================================
$categories = []; $totalRequired = 0; $totalOccupied = 0; $totalVacant = 0;

if (isset($manpowers) && is_array($manpowers)) {
    foreach ($manpowers as $mp) {
        $deptName = trim($mp['department'] ?? '');
        if ($deptName === '') $deptName = 'ไม่ระบุสังกัด';
        if (!isset($categories[$deptName])) { $categories[$deptName] = ['total' => 0, 'occupied' => 0, 'vacant' => 0]; }
        
        $categories[$deptName]['total']++; $totalRequired++;
        if ($mp['status'] == 'occupied') { $categories[$deptName]['occupied']++; $totalOccupied++; } 
        else { $categories[$deptName]['vacant']++; $totalVacant++; }
    }
}

$central = []; $school = []; $health_grouped = []; $countH_units = 0;
if(!empty($categories)) {
    foreach($categories as $deptName => $data) {
        $isSchool = preg_match('/โรงเรียน|วิทยาลัย/u', $deptName);
        $isHealth = preg_match('/รพ\.สต\.|โรงพยาบาล|อนามัย|สาธารณสุข/u', $deptName);

        if($isSchool) { $school[$deptName] = $data; } 
        elseif($isHealth) {
            $amphoe = 'อำเภออื่นๆ';
            if (preg_match('/(อำเภอ|อ\.)\s*([ก-๙a-zA-Z0-9]+)/u', $deptName, $matches)) {
                $name = trim($matches[2]);
                if(in_array($name, ['เมือง', 'เมืองศรีสะเกษ'])) { $name = 'เมืองศรีสะเกษ'; }
                $amphoe = 'อำเภอ' . $name;
            }
            if(!isset($health_grouped[$amphoe])) { $health_grouped[$amphoe] = []; }
            $health_grouped[$amphoe][$deptName] = $data;
            $countH_units++;
        } 
        else { $central[$deptName] = $data; }
    }
    ksort($health_grouped);
}

$sumC_total = array_sum(array_column($central, 'total')); $sumC_occ = array_sum(array_column($central, 'occupied')); $sumC_vac = array_sum(array_column($central, 'vacant'));
$sumS_total = array_sum(array_column($school, 'total')); $sumS_occ = array_sum(array_column($school, 'occupied')); $sumS_vac = array_sum(array_column($school, 'vacant'));
$sumH_total = 0; $sumH_occ = 0; $sumH_vac = 0;
foreach($health_grouped as $h_group) {
    $sumH_total += array_sum(array_column($h_group, 'total')); $sumH_occ += array_sum(array_column($h_group, 'occupied')); $sumH_vac += array_sum(array_column($h_group, 'vacant'));
}
?>

<div class="container-fluid mb-5 mt-3">
    <style>
        .text-teal { color: #0d9488 !important; } .bg-teal { background-color: #0d9488 !important; }
        .summary-card { border-radius: 16px; border: 2px solid transparent; background: #fff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s; }
        .summary-card:hover { transform: translateY(-3px); }
        .card-border-blue { border-color: #3b82f6; } .card-border-green { border-color: #10b981; } .card-border-red { border-color: #ef4444; }
        .search-wrapper { position: relative; width: 100%; max-width: 350px; }
        .search-wrapper .form-control { border-radius: 30px; padding-left: 20px; padding-right: 45px; border: 1px solid #e3e6f0; background-color: #f8f9fc; height: 40px; font-size: 0.9rem; }
        .search-wrapper .form-control:focus { background-color: #fff; box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15); border-color: #0d9488; outline: none; }
        .search-wrapper .btn-search { position: absolute; right: 4px; top: 3px; height: 34px; width: 34px; border-radius: 50%; background: #0d9488; color: white; border: none; display: flex; align-items: center; justify-content: center; pointer-events: none; }
        .table-modern { border-collapse: separate; border-spacing: 0; width: 100%; }
        .table-modern thead th { background-color: #fff; border-bottom: 2px solid #edf2f7; padding: 15px 10px; font-weight: 700; color: #475569; }
        .table-modern tbody td { padding: 12px 10px; border-bottom: 1px solid #edf2f7; vertical-align: middle; }
        .table-modern tfoot td { padding: 15px 10px; background-color: #f8fafc; border-top: 2px solid #cbd5e1; }
        .group-header td { background-color: #f1f5f9; font-weight: bold; } .subgroup-header td { background-color: #f8fafc; }
        .btn-detail { background-color: #fff; color: #0d9488; border: 1px solid #e2e8f0; border-radius: 6px; font-weight: 600; padding: 5px 16px; transition: all 0.2s; font-size: 0.85rem; box-shadow: 0 1px 2px rgba(0,0,0,0.05); }
        .btn-detail:hover { background-color: #f0fdfa; border-color: #ccfbf1; color: #0f766e; text-decoration: none; }
        .view-toggle .btn.active { background-color: #0d9488; color: #fff; border-color: #0d9488; }
        .toggle-icon { transition: transform 0.3s; }
    </style>

    <!-- Header & Summary Cards -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1"><i class="fas fa-sitemap text-teal mr-2"></i> ข้อมูลกรอบอัตรากำลัง</h2>
            <p class="text-muted mb-0">ภาพรวมการจัดสรรอัตรากำลังและตำแหน่งแยกตามหน่วยงาน</p>
        </div>
        <button type="button" class="btn text-white font-weight-bold shadow-sm px-4 py-2" style="background-color: #0d9488; border-radius: 8px;" data-bs-toggle="modal" data-bs-target="#addManpowerModal">
            <i class="fas fa-plus-circle mr-1"></i> เพิ่มตำแหน่งใหม่
        </button>
    </div>

    <!-- Alert System -->
    <?php if(isset($_SESSION['message'])): ?>
        <div class="alert alert-<?= h($_SESSION['message_type']) ?> alert-dismissible fade show shadow-sm border-0" role="alert">
            <i class="fas <?= $_SESSION['message_type'] == 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> mr-1"></i>
            <?= h($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
    <?php endif; ?>

    <div class="row mb-4">
        <div class="col-md-4 mb-3"><div class="summary-card card-border-blue p-3 d-flex justify-content-between align-items-center h-100"><div><h6 class="text-muted font-weight-bold mb-1">อัตราตำแหน่ง (ทั้งหมด)</h6><h2 class="font-weight-bold text-primary mb-0"><?= number_format($totalRequired) ?> <small class="h6 text-muted">อัตรา</small></h2></div><div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center" style="width:50px;height:50px;"><i class="fas fa-users fa-lg"></i></div></div></div>
        <div class="col-md-4 mb-3"><div class="summary-card card-border-red p-3 d-flex justify-content-between align-items-center h-100"><div><h6 class="text-muted font-weight-bold mb-1">ว่าง</h6><h2 class="font-weight-bold text-danger mb-0"><?= number_format($totalVacant) ?> <small class="h6 text-muted">อัตรา</small></h2></div><div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex justify-content-center align-items-center" style="width:50px;height:50px;"><i class="fas fa-user-xmark fa-lg"></i></div></div></div>
        <div class="col-md-4 mb-3"><div class="summary-card card-border-green p-3 d-flex justify-content-between align-items-center h-100"><div><h6 class="text-muted font-weight-bold mb-1">คนครอง</h6><h2 class="font-weight-bold text-success mb-0"><?= number_format($totalOccupied) ?> <small class="h6 text-muted">อัตรา</small></h2></div><div class="bg-success bg-opacity-10 text-success rounded-circle d-flex justify-content-center align-items-center" style="width:50px;height:50px;"><i class="fas fa-user-check fa-lg"></i></div></div></div>
    </div>

    <!-- Controls -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-3 bg-white p-3 rounded shadow-sm border">
        <h5 class="font-weight-bold text-dark mb-3 mb-md-0"><i class="fas fa-list-ul text-teal mr-2"></i> สรุปข้อมูลแยกตามส่วนราชการ</h5>
        <div class="d-flex flex-column flex-md-row gap-2 align-items-center w-100" style="max-width: 500px; justify-content: flex-end;">
            <div class="search-wrapper flex-grow-1 mb-2 mb-md-0">
                <input type="text" class="form-control" id="searchDeptInput" placeholder="ค้นหาหน่วยงาน หรือ อำเภอ..." aria-label="Search">
                <button class="btn-search" type="button"><i class="fas fa-search fa-sm"></i></button>
            </div>
            <div class="btn-group view-toggle" role="group">
                <button type="button" class="btn btn-sm active" id="btnTableView"><i class="fas fa-list"></i></button>
                <button type="button" class="btn btn-sm" id="btnCardView"><i class="fas fa-th-large"></i></button>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- Table View -->
    <!-- ============================================== -->
    <div class="card shadow-sm border-0 mb-5" id="tableViewContainer" style="border-radius: 12px; overflow: hidden;">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table-modern" id="mainDeptTable">
                    <thead>
                        <tr class="bg-light">
                            <th class="text-center border-0" width="8%">ลำดับ</th>
                            <th class="pl-3 border-0">หน่วยงาน</th>
                            <th class="text-center border-0" width="18%">อัตราตำแหน่ง (ทั้งหมด)</th>
                            <th class="text-center border-0" width="12%">ว่าง</th>
                            <th class="text-center border-0" width="12%">คนครอง</th>
                            <th class="text-center border-0" width="15%">จัดการข้อมูล</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $seq = 1; ?>
                        
                        <!-- กลุ่ม: ส่วนกลาง -->
                        <tr class="group-header">
                            <td class="text-center text-muted">-</td>
                            <td class="pl-3 text-primary"><i class="fas fa-building mr-2"></i> ส่วนกลาง <span class="badge bg-primary ml-2"><?= count($central) ?> หน่วยงาน</span></td>
                            <td class="text-center font-weight-bold text-primary"><?= number_format($sumC_total) ?></td>
                            <td class="text-center font-weight-bold <?= $sumC_vac > 0 ? 'text-danger' : 'text-primary' ?>"><?= number_format($sumC_vac) ?></td>
                            <td class="text-center font-weight-bold text-success"><?= number_format($sumC_occ) ?></td>
                            <td></td>
                        </tr>
                        <?php foreach($central as $name => $data): $detail_url = "index.php?action=manpower_detail&dept=" . urlencode($name); ?>
                        <tr class="data-item-row">
                            <td class="text-center text-muted"><?= $seq++ ?></td>
                            <td class="pl-4"><div class="d-flex align-items-center"><i class="fas fa-caret-right text-gray-300 mr-2"></i> <span class="text-dark font-weight-bold search-target"><?= h($name) ?></span></div></td>
                            <td class="text-center font-weight-bold text-secondary"><?= number_format($data['total']) ?></td>
                            <td class="text-center font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger bg-danger bg-opacity-10 rounded' : 'text-muted' ?>"><?= number_format($data['vacant']) ?></td>
                            <td class="text-center font-weight-bold text-teal"><?= number_format($data['occupied']) ?></td>
                            <td class="text-center"><a href="<?= h($detail_url) ?>" class="btn-detail d-inline-block">จัดการข้อมูล <i class="fas fa-cog ml-1 fa-sm"></i></a></td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- กลุ่ม: โรงเรียน -->
                        <tr class="group-header">
                            <td class="text-center text-muted">-</td>
                            <td class="pl-3 text-success"><i class="fas fa-school mr-2"></i> สังกัดโรงเรียน <span class="badge bg-success ml-2"><?= count($school) ?> หน่วยงาน</span></td>
                            <td class="text-center font-weight-bold text-success"><?= number_format($sumS_total) ?></td>
                            <td class="text-center font-weight-bold <?= $sumS_vac > 0 ? 'text-danger' : 'text-success' ?>"><?= number_format($sumS_vac) ?></td>
                            <td class="text-center font-weight-bold text-success"><?= number_format($sumS_occ) ?></td>
                            <td></td>
                        </tr>
                        <?php foreach($school as $name => $data): $detail_url = "index.php?action=manpower_detail&dept=" . urlencode($name); ?>
                        <tr class="data-item-row">
                            <td class="text-center text-muted"><?= $seq++ ?></td>
                            <td class="pl-4"><div class="d-flex align-items-center"><i class="fas fa-caret-right text-gray-300 mr-2"></i> <span class="text-dark font-weight-bold search-target"><?= h($name) ?></span></div></td>
                            <td class="text-center font-weight-bold text-secondary"><?= number_format($data['total']) ?></td>
                            <td class="text-center font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger bg-danger bg-opacity-10 rounded' : 'text-muted' ?>"><?= number_format($data['vacant']) ?></td>
                            <td class="text-center font-weight-bold text-teal"><?= number_format($data['occupied']) ?></td>
                            <td class="text-center"><a href="<?= h($detail_url) ?>" class="btn-detail d-inline-block">จัดการข้อมูล <i class="fas fa-cog ml-1 fa-sm"></i></a></td>
                        </tr>
                        <?php endforeach; ?>

                        <!-- กลุ่ม: รพ.สต. -->
                        <tr class="group-header">
                            <td class="text-center text-muted">-</td>
                            <td class="pl-3 text-info"><i class="fas fa-hospital mr-2"></i> รพ.สต. (แยกตามอำเภอ) <span class="badge bg-info text-white ml-2"><?= $countH_units ?> หน่วยงาน</span></td>
                            <td class="text-center font-weight-bold text-info"><?= number_format($sumH_total) ?></td>
                            <td class="text-center font-weight-bold <?= $sumH_vac > 0 ? 'text-danger' : 'text-info' ?>"><?= number_format($sumH_vac) ?></td>
                            <td class="text-center font-weight-bold text-success"><?= number_format($sumH_occ) ?></td>
                            <td></td>
                        </tr>
                        <?php foreach($health_grouped as $amphoe => $h_data): 
                            $amphoeTotal = array_sum(array_column($h_data, 'total'));
                            $amphoeVacant = array_sum(array_column($h_data, 'vacant'));
                            $amphoeOccupied = array_sum(array_column($h_data, 'occupied'));
                        ?>
                            <tr class="subgroup-header subgroup-toggle-btn" data-subgroup="<?= h($amphoe) ?>" style="cursor: pointer; transition: background 0.2s;" onmouseover="this.style.backgroundColor='#e2e8f0'" onmouseout="this.style.backgroundColor='#f8fafc'">
                                <td class="text-center text-muted"></td>
                                <td class="pl-4 font-weight-bold text-dark"><div class="d-flex justify-content-between align-items-center"><span><i class="fas fa-map-marker-alt text-danger mr-2"></i> <span class="search-target"><?= h($amphoe) ?></span></span><i class="fas fa-chevron-right text-muted toggle-icon"></i></div></td>
                                <td class="text-center text-muted"><?= number_format($amphoeTotal) ?></td>
                                <td class="text-center text-muted"><?= number_format($amphoeVacant) ?></td>
                                <td class="text-center text-muted"><?= number_format($amphoeOccupied) ?></td>
                                <td></td>
                            </tr>
                            <?php foreach($h_data as $name => $data): $detail_url = "index.php?action=manpower_detail&dept=" . urlencode($name); ?>
                            <tr class="data-item-row" data-subgroup="<?= h($amphoe) ?>" style="display: none;">
                                <td class="text-center text-muted"><?= $seq++ ?></td>
                                <td style="padding-left: 5.5rem;"><div class="d-flex align-items-center"><i class="fas fa-minus text-gray-300 mr-2 fa-xs"></i> <span class="text-dark font-weight-bold search-target"><?= h($name) ?></span></div></td>
                                <td class="text-center font-weight-bold text-secondary"><?= number_format($data['total']) ?></td>
                                <td class="text-center font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger bg-danger bg-opacity-10 rounded' : 'text-muted' ?>"><?= number_format($data['vacant']) ?></td>
                                <td class="text-center font-weight-bold text-teal"><?= number_format($data['occupied']) ?></td>
                                <td class="text-center"><a href="<?= h($detail_url) ?>" class="btn-detail d-inline-block">จัดการข้อมูล <i class="fas fa-cog ml-1 fa-sm"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                    <!-- 🌟 แถวรวมยอดทั้งหมด (Footer) 🌟 -->
                    <tfoot>
                        <tr>
                            <td colspan="2" class="text-right pr-4 font-weight-bold text-dark" style="font-size: 1.05rem;">
                                รวมส่วนราชการ / จำนวนหน่วยงาน (<?= number_format(count($categories)) ?> หน่วยงาน)
                            </td>
                            <td class="text-center font-weight-bold text-primary" style="font-size: 1.1rem;"><?= number_format($totalRequired) ?></td>
                            <td class="text-center font-weight-bold text-danger" style="font-size: 1.1rem;"><?= number_format($totalVacant) ?></td>
                            <td class="text-center font-weight-bold text-success" style="font-size: 1.1rem;"><?= number_format($totalOccupied) ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ============================================== -->
    <!-- Card View (ซ่อนเป็นค่าเริ่มต้น) -->
    <!-- ============================================== -->
    <div class="row d-none" id="cardViewContainer">
        <?php 
        $all_groups = [
            ['title' => 'ส่วนกลาง', 'icon' => 'fa-building', 'color' => 'primary', 'data' => $central, 'type' => 'normal'],
            ['title' => 'สังกัดโรงเรียน', 'icon' => 'fa-school', 'color' => 'success', 'data' => $school, 'type' => 'normal'],
            ['title' => 'รพ.สต. (แยกตามอำเภอ)', 'icon' => 'fa-hospital', 'color' => 'info', 'data' => $health_grouped, 'type' => 'grouped']
        ];
        foreach($all_groups as $grp): if(empty($grp['data'])) continue;
        ?>
        <div class="col-12 mb-3 mt-2 card-group-header" data-group="<?= $grp['color'] ?>">
            <h5 class="font-weight-bold text-<?= $grp['color'] ?> border-bottom pb-2"><i class="fas <?= $grp['icon'] ?> mr-2"></i> <?= $grp['title'] ?></h5>
        </div>
        
        <?php if($grp['type'] == 'normal'): foreach($grp['data'] as $name => $data): $url = 'index.php?action=manpower_detail&dept='.urlencode($name); ?>
            <div class="col-lg-4 col-md-6 mb-4 data-item-card" data-group="<?= $grp['color'] ?>">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 12px; transition: transform 0.2s;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="font-weight-bold text-dark mb-3 search-target" style="line-height: 1.4; height: 40px; overflow: hidden;"><i class="fas fa-caret-right text-teal mr-1"></i> <?= h($name) ?></h6>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom"><span class="text-muted small">อัตราตำแหน่ง (ทั้งหมด)</span><span class="font-weight-bold text-dark"><?= number_format($data['total']) ?></span></div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom"><span class="text-muted small">ว่าง</span><span class="font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($data['vacant']) ?></span></div>
                            <div class="d-flex justify-content-between mb-4"><span class="text-muted small">คนครอง</span><span class="font-weight-bold text-teal"><?= number_format($data['occupied']) ?></span></div>
                            <a href="<?= h($url) ?>" class="btn btn-block font-weight-bold btn-light border text-teal" style="border-radius: 8px;">จัดการข้อมูล <i class="fas fa-cog ml-1 fa-sm"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; else: foreach($grp['data'] as $amphoe => $h_data): ?>
            <div class="col-12 mb-2 card-group-header subgroup-toggle-btn" data-group="<?= $grp['color'] ?>" data-subgroup="<?= h($amphoe) ?>" style="cursor:pointer;">
                <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded border">
                    <h6 class="font-weight-bold text-dark mb-0 search-target"><i class="fas fa-map-marker-alt text-danger mr-2"></i> <?= h($amphoe) ?></h6>
                    <i class="fas fa-chevron-right text-muted toggle-icon"></i>
                </div>
            </div>
            <?php foreach($h_data as $name => $data): $url = 'index.php?action=manpower_detail&dept='.urlencode($name); ?>
            <div class="col-lg-4 col-md-6 mb-4 data-item-card" data-group="<?= $grp['color'] ?>" data-subgroup="<?= h($amphoe) ?>" style="display: none;">
                <div class="card shadow-sm h-100 border-0" style="border-radius: 12px; transition: transform 0.2s;">
                    <div class="card-body d-flex flex-column">
                        <h6 class="font-weight-bold text-dark mb-3 search-target" style="line-height: 1.4; height: 40px; overflow: hidden;"><i class="fas fa-hospital-alt text-teal mr-1"></i> <?= h($name) ?></h6>
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom"><span class="text-muted small">อัตราตำแหน่ง (ทั้งหมด)</span><span class="font-weight-bold text-dark"><?= number_format($data['total']) ?></span></div>
                            <div class="d-flex justify-content-between mb-2 pb-2 border-bottom"><span class="text-muted small">ว่าง</span><span class="font-weight-bold <?= $data['vacant'] > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($data['vacant']) ?></span></div>
                            <div class="d-flex justify-content-between mb-4"><span class="text-muted small">คนครอง</span><span class="font-weight-bold text-teal"><?= number_format($data['occupied']) ?></span></div>
                            <a href="<?= h($url) ?>" class="btn btn-block font-weight-bold btn-light border text-teal" style="border-radius: 8px;">จัดการข้อมูล <i class="fas fa-cog ml-1 fa-sm"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; endforeach; endif; endforeach; ?>
    </div>
</div>

<!-- ============================================== -->
<!-- 🌟 Modal เพิ่มตำแหน่งใหม่ (Popup) 🌟 -->
<!-- ============================================== -->
<div class="modal fade" id="addManpowerModal" tabindex="-1" aria-labelledby="addManpowerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
            <div class="modal-header bg-light border-bottom-0" style="border-radius: 12px 12px 0 0;">
                <h5 class="modal-title font-weight-bold text-dark" id="addManpowerModalLabel">
                    <div class="d-flex align-items-center">
                        <div class="bg-teal bg-opacity-10 text-teal rounded-circle d-flex justify-content-center align-items-center mr-3" style="width: 40px; height: 40px;"><i class="fas fa-plus"></i></div>
                        <div>เพิ่มกรอบอัตรากำลังใหม่<br><small class="text-muted font-weight-normal" style="font-size:0.85rem;">กรอกข้อมูลรายละเอียดของตำแหน่งที่ต้องการเพิ่มลงในระบบ</small></div>
                    </div>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="index.php?action=manpower_store" method="POST">
                <div class="modal-body p-4 bg-white">
                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-dark"><i class="fas fa-id-badge text-teal mr-2"></i> ข้อมูลสายงานและระดับ</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">เลขที่ตำแหน่ง <span class="text-danger">*</span></label>
                            <input type="text" name="position_number" class="form-control font-weight-bold" required placeholder="เช่น 01-1-001">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ชื่อสายงาน/ตำแหน่ง <span class="text-danger">*</span></label>
                            <input type="text" name="position_name" class="form-control" required placeholder="เช่น นักทรัพยากรบุคคล">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ประเภทบุคลากร <span class="text-danger">*</span></label>
                            <select name="employee_type" class="form-select" required>
                                <option value="">-- เลือกประเภท --</option>
                                <option value="ข้าราชการ อบจ.">ข้าราชการ อบจ.</option>
                                <option value="ข้าราชการครู อบจ.">ข้าราชการครู อบจ.</option>
                                <option value="ลูกจ้างประจำ">ลูกจ้างประจำ</option>
                                <option value="พนักงานจ้างตามภารกิจ">พนักงานจ้างตามภารกิจ</option>
                                <option value="พนักงานจ้างทั่วไป">พนักงานจ้างทั่วไป</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">ระดับตำแหน่ง <span class="text-danger">*</span></label>
                            <select name="level" class="form-select" required>
                                <option value="">-- เลือกระดับ --</option>
                                <?php if(!empty($position_levels)): foreach($position_levels as $lvl): ?>
                                    <option value="<?= h($lvl['name']) ?>"><?= h($lvl['name']) ?> <?= !empty($lvl['type']) ? "(".h($lvl['type']).")" : "" ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-3 pb-2 border-bottom text-dark"><i class="fas fa-building text-teal mr-2"></i> ข้อมูลสังกัดและสถานะ</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">สังกัดหลัก (สำนัก/กอง/รพ.สต.) <span class="text-danger">*</span></label>
                            <select name="department" class="form-select" required>
                                <option value="">-- เลือกส่วนราชการ --</option>
                                <?php if(!empty($departments)): foreach($departments as $dept): ?>
                                    <option value="<?= h($dept['name']) ?>"><?= h($dept['name']) ?></option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary small">สังกัดย่อย (ฝ่าย/ส่วน/งาน)</label>
                            <input type="text" name="division" class="form-control" placeholder="เช่น ฝ่ายบริหารงานทั่วไป">
                        </div>
                        <!-- ลบช่องเงินเดือนออกแล้วตามที่ร้องขอ -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-secondary small">สถานะ <span class="text-danger">*</span></label>
                            <select name="status" class="form-select fw-bold border-success text-success bg-success bg-opacity-10" required onchange="this.className = this.value === 'vacant' ? 'form-select fw-bold border-success text-success bg-success bg-opacity-10' : 'form-select fw-bold border-secondary text-secondary bg-light'">
                                <option value="vacant" selected>ว่าง (รอสรรหา)</option>
                                <option value="occupied">มีคนครอง</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold text-secondary small">หมายเหตุ</label>
                            <textarea name="remark" class="form-control" rows="2" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light border-top-0" style="border-radius: 0 0 12px 12px;">
                    <button type="button" class="btn btn-outline-secondary font-weight-bold px-4" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn text-white font-weight-bold px-4 shadow-sm" style="background-color: #0d9488;"><i class="fas fa-save mr-1"></i> บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. สลับตาราง/การ์ด
    const btnTableView = document.getElementById('btnTableView'); const btnCardView = document.getElementById('btnCardView');
    const tableViewContainer = document.getElementById('tableViewContainer'); const cardViewContainer = document.getElementById('cardViewContainer');
    btnTableView.addEventListener('click', function() { btnTableView.classList.add('active'); btnCardView.classList.remove('active'); tableViewContainer.classList.remove('d-none'); cardViewContainer.classList.add('d-none'); });
    btnCardView.addEventListener('click', function() { btnCardView.classList.add('active'); btnTableView.classList.remove('active'); cardViewContainer.classList.remove('d-none'); tableViewContainer.classList.add('d-none'); });

    // 2. ระบบย่อขยายแถว (Accordion)
    document.querySelectorAll('.subgroup-toggle-btn').forEach(btn => {
        btn.setAttribute('data-expanded', 'false');
        btn.addEventListener('click', function() {
            const subgroup = this.getAttribute('data-subgroup');
            const isExpanded = this.getAttribute('data-expanded') === 'true';
            
            document.querySelectorAll(`.subgroup-toggle-btn[data-subgroup="${subgroup}"]`).forEach(b => {
                b.setAttribute('data-expanded', !isExpanded ? 'true' : 'false');
                const icon = b.querySelector('.toggle-icon');
                if (icon) { icon.classList.toggle('fa-chevron-right', isExpanded); icon.classList.toggle('fa-chevron-down', !isExpanded); }
            });

            const term = document.getElementById('searchDeptInput').value.toLowerCase().trim();
            ['#mainDeptTable .data-item-row', '.data-item-card'].forEach(selector => {
                document.querySelectorAll(`${selector}[data-subgroup="${subgroup}"]`).forEach(el => {
                    if (term !== '') {
                        const target = el.querySelector('.search-target');
                        el.style.display = (target && target.textContent.toLowerCase().includes(term) && !isExpanded) ? '' : 'none';
                    } else { el.style.display = !isExpanded ? '' : 'none'; }
                });
            });
        });
    });

    // 3. ระบบค้นหา Real-time
    const searchDeptInput = document.getElementById('searchDeptInput');
    searchDeptInput.addEventListener('input', function() {
        const term = this.value.toLowerCase().trim();
        
        if (term === '') {
            document.querySelectorAll('.data-item-row, .data-item-card').forEach(el => {
                const subgroup = el.getAttribute('data-subgroup');
                if (subgroup) {
                    const btn = document.querySelector(`.subgroup-toggle-btn[data-subgroup="${subgroup}"]`);
                    el.style.display = (btn && btn.getAttribute('data-expanded') === 'true') ? '' : 'none';
                } else { el.style.display = ''; }
            });
            document.querySelectorAll('.group-header, .subgroup-header, .card-group-header').forEach(h => h.style.display = '');
            return;
        }

        let groups = {}; let subgroups = {};
        
        ['#mainDeptTable .data-item-row', '.data-item-card'].forEach(selector => {
            document.querySelectorAll(selector).forEach(el => {
                const target = el.querySelector('.search-target');
                const isMatch = target && target.textContent.toLowerCase().includes(term);
                el.style.display = isMatch ? '' : 'none';
                
                if (isMatch) {
                    const g = el.getAttribute('data-group'); if (g) groups[g] = (groups[g] || 0) + 1;
                    const sg = el.getAttribute('data-subgroup'); if (sg) subgroups[sg] = true;
                }
            });
        });

        // เปิดหัวข้อย่อยและกลุ่มอัตโนมัติ
        document.querySelectorAll('.subgroup-toggle-btn').forEach(btn => {
            const sg = btn.getAttribute('data-subgroup');
            const target = btn.querySelector('.search-target');
            if ((target && target.textContent.toLowerCase().includes(term)) || subgroups[sg]) {
                btn.style.display = '';
                btn.setAttribute('data-expanded', 'true');
                const icon = btn.querySelector('.toggle-icon'); if (icon) { icon.classList.replace('fa-chevron-right', 'fa-chevron-down'); }
                if(target && target.textContent.toLowerCase().includes(term)) {
                     document.querySelectorAll(`[data-subgroup="${sg}"]`).forEach(e => e.style.display='');
                }
            } else { btn.style.display = 'none'; }
        });

        document.querySelectorAll('.group-header, .card-group-header').forEach(header => {
            const g = header.getAttribute('data-group'); header.style.display = groups[g] ? '' : 'none';
        });
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>