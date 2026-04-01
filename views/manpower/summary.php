<?php
// ==========================================
// ชื่อไฟล์: summary.php
// ที่อยู่ไฟล์: views/manpower/summary.php
// ==========================================

if (strpos($_SERVER['REQUEST_URI'], 'views/manpower/summary.php') !== false) {
    header("Location: ../../index.php?action=summary_report");
    exit();
}

// นำตัวแปรที่ส่งมาจาก Controller มาเช็คค่าว่างป้องกัน Error
$overall = $overall ?? ['total' => 0, 'occupied' => 0, 'vacant' => 0];
$dept_stats = $dept_stats ?? [];
$type_stats = $type_stats ?? [];

// เตรียมข้อมูลสำหรับส่งไปให้ JavaScript (Chart.js)
$typeLabels = []; $typeData = [];
foreach($type_stats as $t) {
    $typeLabels[] = $t['emp_type'];
    $typeData[] = $t['total'];
}

// ตัวแปรสำหรับแทรก Script กราฟไว้ด้านล่างสุด (ส่งให้ footer.php)
ob_start();
?>
<!-- 🌟 แทรก Chart.js ผ่าน CDN 🌟 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    // กราฟโดนัท: อัตราการครองตำแหน่ง (Occupied vs Vacant)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['มีคนครอง', 'ตำแหน่งว่าง'],
            datasets: [{
                data: [<?= $overall['occupied'] ?>, <?= $overall['vacant'] ?>],
                backgroundColor: ['#10b981', '#ef4444'], // เขียว, แดง
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: { position: 'bottom', labels: { font: { family: "'Prompt', sans-serif" } } }
            }
        }
    });

    // กราฟแท่ง: แยกตามประเภทบุคลากร
    const ctxType = document.getElementById('typeChart').getContext('2d');
    new Chart(ctxType, {
        type: 'bar',
        data: {
            labels: <?= json_encode($typeLabels) ?>,
            datasets: [{
                label: 'จำนวน (อัตรา)',
                data: <?= json_encode($typeData) ?>,
                backgroundColor: '#3b82f6', // สีน้ำเงิน
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: "'Prompt', sans-serif" } } },
                x: { ticks: { font: { family: "'Prompt', sans-serif" } } }
            },
            plugins: {
                legend: { display: false }
            }
        }
    });
});
</script>
<?php
$extra_scripts = ob_get_clean();

// โหลด Header (บรรทัดนี้ต้องอยู่หลังการเตรียม $extra_scripts)
include 'views/layout/header.php'; 
?>

<style>
/* ================= CSS สำหรับการพิมพ์ (Print Mode) ================= */
@media print {
    /* ซ่อนเมนูและปุ่มที่ไม่จำเป็นเวลาพิมพ์ */
    #sidebar, .topbar, .btn-print, .sidebar-overlay, .alert { display: none !important; }
    /* ขยายหน้ากระดาษให้เต็ม */
    #main-wrapper { margin-left: 0 !important; width: 100% !important; }
    .content-area { padding: 0 !important; }
    /* ซ่อนเงาและขอบการ์ดให้ดูสะอาด */
    .card { border: none !important; box-shadow: none !important; }
    .card-header { background-color: transparent !important; border-bottom: 2px solid #000 !important; }
    /* สีตัวอักษรเป็นสีดำทั้งหมดเพื่อประหยัดหมึก */
    body { background-color: #fff; color: #000; }
    .text-teal, .text-primary, .text-success, .text-danger { color: #000 !important; }
    /* ให้ตารางพิมพ์แล้วดูชัดเจน */
    table { border-collapse: collapse !important; width: 100%; }
    th, td { border: 1px solid #ddd !important; padding: 8px !important; }
    /* บังคับกราฟให้พิมพ์ออกสีได้ */
    canvas { -webkit-print-color-adjust: exact; color-adjust: exact; }
    .print-header { display: block !important; text-align: center; margin-bottom: 20px; }
}

/* CSS สำหรับหน้าจอปกติ */
.print-header { display: none; }
.stat-card-icon { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 50%; }
</style>

<div class="container-fluid py-3">
    
    <!-- ส่วนหัวสำหรับพิมพ์เท่านั้น -->
    <div class="print-header">
        <h3 class="fw-bold">รายงานสรุปข้อมูลกรอบอัตรากำลัง</h3>
        <p>องค์การบริหารส่วนจังหวัดศรีสะเกษ</p>
        <p>ข้อมูล ณ วันที่: <?= date('d/m/Y') ?></p>
    </div>

    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-1"><i class="fas fa-chart-line text-teal me-2"></i> รายงานและสถิติ</h2>
            <p class="text-muted mb-0">สรุปข้อมูลกรอบอัตรากำลัง แยกตามส่วนราชการและประเภทบุคลากร</p>
        </div>
        <button onclick="window.print()" class="btn btn-primary btn-print px-4 py-2 fw-bold shadow-sm" style="border-radius: 8px;">
            <i class="fas fa-print me-2"></i> พิมพ์รายงาน
        </button>
    </div>

    <!-- 1. การ์ดสรุปยอดรวม 3 ช่อง -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4" style="border-bottom: 4px solid #3b82f6;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1 text-uppercase small">กรอบตำแหน่งทั้งหมด</p>
                        <h2 class="fw-bold text-dark mb-0"><?= number_format($overall['total']) ?> <span class="fs-6 text-muted fw-normal">อัตรา</span></h2>
                    </div>
                    <div class="stat-card-icon bg-primary bg-opacity-10 text-primary"><i class="fas fa-users fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4" style="border-bottom: 4px solid #10b981;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1 text-uppercase small">มีคนครองตำแหน่ง</p>
                        <h2 class="fw-bold text-success mb-0"><?= number_format($overall['occupied']) ?> <span class="fs-6 text-muted fw-normal">อัตรา</span></h2>
                    </div>
                    <div class="stat-card-icon bg-success bg-opacity-10 text-success"><i class="fas fa-user-check fs-4"></i></div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 rounded-4" style="border-bottom: 4px solid #ef4444;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted fw-bold mb-1 text-uppercase small">ตำแหน่งว่าง</p>
                        <h2 class="fw-bold text-danger mb-0"><?= number_format($overall['vacant']) ?> <span class="fs-6 text-muted fw-normal">อัตรา</span></h2>
                    </div>
                    <div class="stat-card-icon bg-danger bg-opacity-10 text-danger"><i class="fas fa-user-slash fs-4"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. ส่วนของกราฟ (Charts) -->
    <div class="row g-4 mb-4">
        <!-- กราฟวงกลม -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-teal me-2"></i> สัดส่วนการครองตำแหน่ง</h6>
                </div>
                <div class="card-body" style="position: relative; height: 250px;">
                    <canvas id="statusChart"></canvas>
                    <!-- ตัวเลขเปอร์เซ็นต์ตรงกลางโดนัท -->
                    <div class="position-absolute top-50 start-50 translate-middle text-center" style="margin-top: 10px;">
                        <?php 
                            $percent = ($overall['total'] > 0) ? round(($overall['occupied'] / $overall['total']) * 100) : 0;
                        ?>
                        <h3 class="fw-bold text-dark mb-0"><?= $percent ?>%</h3>
                        <small class="text-muted" style="font-size: 0.75rem;">มีคนครอง</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- กราฟแท่ง -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="fas fa-chart-bar text-teal me-2"></i> จำนวนกรอบอัตราแยกตามประเภทบุคลากร</h6>
                </div>
                <div class="card-body" style="height: 250px;">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. ตารางข้อมูลรายละเอียด (Data Table) -->
    <div class="card border-0 shadow-sm rounded-4 mb-5 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <h6 class="fw-bold text-dark mb-0"><i class="fas fa-table text-teal me-2"></i> สรุปข้อมูลแยกตามส่วนราชการ</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background-color: #f8fafc;">
                        <tr>
                            <th class="py-3 px-4 text-secondary">ส่วนราชการ / หน่วยงาน</th>
                            <th class="py-3 text-center text-secondary" width="15%">กรอบทั้งหมด (อัตรา)</th>
                            <th class="py-3 text-center text-success" width="15%">มีคนครอง (อัตรา)</th>
                            <th class="py-3 text-center text-danger" width="15%">ตำแหน่งว่าง (อัตรา)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($dept_stats)): ?>
                            <tr><td colspan="4" class="text-center py-4 text-muted">ไม่มีข้อมูลส่วนราชการในระบบ</td></tr>
                        <?php else: foreach($dept_stats as $dept): ?>
                            <tr>
                                <td class="py-3 px-4 fw-bold text-dark"><?= h($dept['department'] ?: 'ไม่ระบุสังกัด') ?></td>
                                <td class="py-3 text-center fw-bold text-secondary"><?= number_format($dept['total']) ?></td>
                                <td class="py-3 text-center fw-bold text-success"><?= number_format($dept['occupied']) ?></td>
                                <td class="py-3 text-center fw-bold <?= $dept['vacant'] > 0 ? 'text-danger' : 'text-muted' ?>"><?= number_format($dept['vacant']) ?></td>
                            </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                    <tfoot style="background-color: #f8fafc; border-top: 2px solid #e2e8f0;">
                        <tr>
                            <td class="py-3 px-4 text-end fw-bold text-dark">รวมทั้งสิ้น</td>
                            <td class="py-3 text-center fw-bold text-primary fs-5"><?= number_format($overall['total']) ?></td>
                            <td class="py-3 text-center fw-bold text-success fs-5"><?= number_format($overall['occupied']) ?></td>
                            <td class="py-3 text-center fw-bold text-danger fs-5"><?= number_format($overall['vacant']) ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

</div>

<?php include 'views/layout/footer.php'; ?>