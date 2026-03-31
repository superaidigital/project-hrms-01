<?php include 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark">
            <i class="fa-solid fa-chart-pie text-primary me-2"></i>ภาพรวมระบบ (Dashboard)
        </h2>
        <p class="text-muted mb-0">ข้อมูลสรุปสถิติและอัตรากำลังขององค์การบริหารส่วนจังหวัดศรีสะเกษ</p>
    </div>
    <div class="text-end d-none d-md-block">
        <div class="text-muted small">ข้อมูลอัปเดตล่าสุด</div>
        <div class="fw-bold text-teal"><?= date('d / m / Y'); ?></div>
    </div>
</div>

<!-- ================= 1. ส่วนของการ์ดสรุปข้อมูล (Summary Cards) ================= -->
<div class="row g-4 mb-4">
    <!-- การ์ดที่ 1: บุคลากรทั้งหมด -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold" style="font-size: 0.9rem;">บุคลากรทั้งหมด</p>
                        <h2 class="fw-bold mb-0 display-5"><?= number_format($stats['total_employees']); ?></h2>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-users fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 text-white-50 small">
                    <i class="fa-solid fa-arrow-up me-1"></i> ปฏิบัติงานจริง ณ ปัจจุบัน
                </div>
            </div>
        </div>
    </div>

    <!-- การ์ดที่ 2: กรอบอัตรากำลัง -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #10b981, #059669); color: white;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold" style="font-size: 0.9rem;">กรอบอัตรากำลัง</p>
                        <h2 class="fw-bold mb-0 display-5"><?= number_format($stats['total_manpower']); ?></h2>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-sitemap fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 text-white-50 small">
                    ตามแผนอัตรากำลัง 3 ปี
                </div>
            </div>
        </div>
    </div>

    <!-- การ์ดที่ 3: หน่วยงาน/แผนก -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold" style="font-size: 0.9rem;">หน่วยงาน/กอง</p>
                        <h2 class="fw-bold mb-0 display-5"><?= number_format($stats['total_departments']); ?></h2>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-building fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 text-white-50 small">
                    ส่วนราชการในสังกัด
                </div>
            </div>
        </div>
    </div>

    <!-- การ์ดที่ 4: ผู้ใช้งานระบบ -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100" style="background: linear-gradient(135deg, #8b5cf6, #6d28d9); color: white;">
            <div class="card-body p-4 position-relative">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="mb-1 text-white-50 fw-semibold" style="font-size: 0.9rem;">ผู้ใช้งานระบบ</p>
                        <h2 class="fw-bold mb-0 display-5"><?= number_format($stats['total_users']); ?></h2>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-user-shield fs-4"></i>
                    </div>
                </div>
                <div class="mt-3 text-white-50 small">
                    เจ้าหน้าที่ที่เปิดสิทธิ์เข้าถึง
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ================= 2. ส่วนของกราฟ (Charts) ================= -->
<div class="row g-4 mb-4">
    <!-- กราฟแท่ง (Bar Chart) -->
    <div class="col-12 col-lg-8">
        <div class="modern-card h-100">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-chart-column text-teal me-2"></i>สัดส่วนบุคลากรแยกตามหน่วยงาน (ตัวอย่าง)</h5>
            <div style="height: 300px;">
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- กราฟวงกลม (Doughnut Chart) -->
    <div class="col-12 col-lg-4">
        <div class="modern-card h-100">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-solid fa-chart-pie text-teal me-2"></i>ประเภทบุคลากร (ตัวอย่าง)</h5>
            <div style="height: 250px; position: relative; margin: auto;">
                <canvas id="pieChart"></canvas>
            </div>
            <div class="mt-4 text-center">
                <span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1 me-1">ข้าราชการ</span>
                <span class="badge bg-success bg-opacity-10 text-success px-2 py-1 me-1">ลูกจ้างประจำ</span>
                <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">พนักงานจ้าง</span>
            </div>
        </div>
    </div>
</div>

<?php 
// ================= 3. ส่วนของการแทรก JavaScript (Chart.js) =================
// เราใช้ ob_start() เพื่อเก็บโค้ด JS ไปใส่ในตัวแปร $extra_scripts ที่ footer.php ดึงไปใช้
ob_start(); 
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        
        // --- กราฟแท่ง (Bar Chart) ---
        const ctxBar = document.getElementById('barChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: ['สำนักปลัด', 'กองคลัง', 'กองช่าง', 'กองสาธารณสุข', 'กองการศึกษา'],
                datasets: [{
                    label: 'จำนวนบุคลากร (คน)',
                    data: [45, 25, 60, 30, 15], // (ข้อมูลจำลอง)
                    backgroundColor: 'rgba(45, 212, 191, 0.7)',
                    borderColor: 'rgba(20, 184, 166, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // --- กราฟวงกลม (Doughnut Chart) ---
        const ctxPie = document.getElementById('pieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: ['ข้าราชการ', 'ลูกจ้างประจำ', 'พนักงานจ้าง'],
                datasets: [{
                    data: [120, 35, 200], // (ข้อมูลจำลอง)
                    backgroundColor: [
                        '#3b82f6', // สีน้ำเงิน
                        '#10b981', // สีเขียว
                        '#f59e0b'  // สีส้ม
                    ],
                    borderWidth: 2,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { display: false }
                }
            }
        });

    });
</script>
<?php 
$extra_scripts = ob_get_clean(); 
// จบการทำงานของสคริปต์
?>

<?php include 'views/layout/footer.php'; ?>