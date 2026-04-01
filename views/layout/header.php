<?php
// ==========================================
// ชื่อไฟล์: header.php
// ที่อยู่ไฟล์: views/layout/header.php
// ==========================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$current_action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

if (!function_exists('is_active')) {
    function is_active($actions_array, $current) {
        return in_array($current, $actions_array) ? 'active' : '';
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - ระบบบริหารจัดการบุคลากร อบจ.ศรีสะเกษ</title>
    
    <!-- 🌟 ฝัง Script ไว้บนสุดเพื่อป้องกันเมนูกระตุกตอนเปลี่ยนหน้า 🌟 -->
    <script>
        if (localStorage.getItem('sidebarCollapsed') === 'true') {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>

    <!-- Google Fonts: Prompt -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS & FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- SweetAlert2 -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        /* ================= ธีมหลักและสัดส่วน (Theme & Proportions) ================= */
        :root {
            --primary: #0d9488;       /* Teal */
            --primary-hover: #0f766e;
            --sidebar-bg: #1e293b;    /* Slate 800 - ดูสว่างขึ้นนิดหน่อย สบายตา */
            --sidebar-text: #cbd5e1;  /* Slate 300 */
            --sidebar-width: 260px;   /* ความกว้างเมนูที่สมส่วน */
            --sidebar-collapsed-width: 76px; /* ขนาดตอนย่อให้พอดีกับไอคอน */
            --topbar-height: 70px;    /* 🌟 ปรับความสูงมาตรฐานให้บาลานซ์ 🌟 */
            --bg-light: #f8fafc;
            --transition-speed: 0.3s;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: var(--bg-light);
            color: #334155;
            overflow-x: hidden;
        }

        .preload * { transition: none !important; }

        /* ================= โครงสร้างหลัก ================= */
        #wrapper { display: flex; width: 100%; min-height: 100vh; }

        /* ================= Sidebar ================= */
        #sidebar {
            width: var(--sidebar-width);
            background-color: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0; height: 100vh;
            z-index: 1040;
            transition: width var(--transition-speed) ease;
            box-shadow: 2px 0 10px rgba(0,0,0,0.05);
            display: flex; flex-direction: column;
            overflow-y: auto; overflow-x: hidden;
        }
        
        #sidebar::-webkit-scrollbar { width: 4px; }
        #sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }

        /* 🌟 ปรับปรุงส่วนหัวเมนู (โลโก้) ให้สมส่วน 🌟 */
        .sidebar-brand {
            min-height: 85px; /* ปรับจาก fix height เป็น min-height ให้ขยายได้ */
            display: flex; align-items: center; 
            padding: 1.25rem 1.5rem; /* เพิ่ม Padding ด้านบนและล่าง ไม่ให้ชิดขอบ */
            background-color: #0f172a; /* พื้นหลังเข้มขึ้นเพื่อเน้นโลโก้ */
            color: #fff; text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            white-space: nowrap; overflow: hidden;
            transition: all 0.3s;
        }
        .sidebar-brand:hover { color: #fff; background-color: rgba(0,0,0,0.2); }

        /* 🌟 ปรับขนาดไอคอนโลโก้ให้บาลานซ์กับปุ่มเมนู 🌟 */
        .brand-icon {
            width: 42px; height: 42px; 
            background: linear-gradient(135deg, #0d9488, #059669);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.25rem; color: white;
            margin-right: 12px; flex-shrink: 0;
            box-shadow: 0 4px 8px rgba(13, 148, 136, 0.3);
        }

        /* 🌟 จัดระเบียบข้อความโลโก้ 🌟 */
        .brand-text { display: flex; flex-direction: column; justify-content: center; }
        .brand-text h5 { margin: 0 0 3px 0; font-weight: 700; font-size: 1.15rem; letter-spacing: 0.5px; line-height: 1; }
        .brand-text h5 span { color: #2dd4bf; font-weight: 400; }
        .brand-text p { margin: 0; font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.2px; opacity: 0.9; line-height: 1; }

        .sidebar-nav { list-style: none; padding: 1.5rem 0.75rem 1rem; margin: 0; }
        
        .nav-title {
            padding: 0.75rem 1rem 0.5rem;
            font-size: 0.68rem; font-weight: 700; color: #64748b;
            text-transform: uppercase; letter-spacing: 1px; white-space: nowrap;
        }

        .sidebar-nav li { margin-bottom: 0.25rem; }
        .sidebar-nav a {
            display: flex; align-items: center; padding: 0.7rem 1rem;
            color: var(--sidebar-text); text-decoration: none;
            border-radius: 8px; font-weight: 500; font-size: 0.9rem;
            transition: all 0.2s; white-space: nowrap;
        }
        .sidebar-nav a i { width: 24px; font-size: 1.1rem; margin-right: 10px; text-align: center; }
        .sidebar-nav a:hover { color: #fff; background-color: rgba(255,255,255,0.05); }
        .sidebar-nav a.active {
            color: #fff; background: linear-gradient(to right, #0d9488, #14b8a6);
            box-shadow: 0 4px 10px rgba(13, 148, 136, 0.25);
        }

        /* ปุ่มออกจากระบบด้านล่าง */
        .logout-link {
            display: flex; align-items: center; padding: 0.7rem 1rem;
            color: #ef4444; text-decoration: none; border-radius: 8px;
            background: rgba(239, 68, 68, 0.05); font-weight: 500; font-size: 0.9rem;
            transition: all 0.2s; white-space: nowrap;
        }
        .logout-link:hover { background: rgba(239, 68, 68, 0.15); color: #dc2626; }
        .logout-link i { width: 24px; margin-right: 10px; text-align: center; }

        /* ================= 🌟 สถานะย่อเมนู (Collapsed) 🌟 ================= */
        html.sidebar-collapsed #sidebar { width: var(--sidebar-collapsed-width); }
        html.sidebar-collapsed .sidebar-brand { padding: 1.25rem 0; justify-content: center; min-height: 85px; }
        html.sidebar-collapsed .brand-icon { margin-right: 0; }
        html.sidebar-collapsed .brand-text, 
        html.sidebar-collapsed .nav-title, 
        html.sidebar-collapsed .sidebar-nav a span,
        html.sidebar-collapsed .logout-link span { display: none; }
        
        html.sidebar-collapsed .sidebar-nav a,
        html.sidebar-collapsed .logout-link { padding: 0.75rem 0; justify-content: center; }
        html.sidebar-collapsed .sidebar-nav a i,
        html.sidebar-collapsed .logout-link i { margin-right: 0; font-size: 1.25rem; }

        /* ================= Main Content & Topbar ================= */
        #content-wrapper {
            flex: 1; margin-left: var(--sidebar-width); display: flex; flex-direction: column;
            transition: margin-left var(--transition-speed) ease;
        }
        html.sidebar-collapsed #content-wrapper { margin-left: var(--sidebar-collapsed-width); }

        .topbar {
            height: var(--topbar-height); background-color: #fff;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 1.5rem; position: sticky; top: 0; z-index: 1030;
        }

        /* 🌟 ปรับปุ่ม Hamburger ให้สมมาตรกับโลโก้ 🌟 */
        .btn-toggle {
            background: #f1f5f9; border: none; color: #475569;
            width: 42px; height: 42px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s; cursor: pointer;
        }
        .btn-toggle:hover { background: #e2e8f0; color: var(--primary); }

        .main-content { padding: 2rem 2.5rem; flex: 1; }

        /* ================= Mobile View ================= */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(-100%); }
            html.sidebar-show #sidebar { transform: translateX(0); width: var(--sidebar-width); }
            #content-wrapper, html.sidebar-collapsed #content-wrapper { margin-left: 0; }
            .topbar { padding: 0 1rem; }
            .main-content { padding: 1.5rem 1rem; }
            
            .sidebar-overlay {
                position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(15, 23, 42, 0.5); backdrop-filter: blur(2px);
                z-index: 1035; opacity: 0; visibility: hidden; transition: all 0.3s;
            }
            html.sidebar-show .sidebar-overlay { opacity: 1; visibility: visible; }
        }
    </style>
</head>
<body class="preload">

    <!-- แผ่นใสบังหลังเวลาเปิดเมนูบนมือถือ -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div id="wrapper">
        
        <!-- ================= SIDEBAR ================= -->
        <aside id="sidebar">
            <a href="index.php?action=dashboard" class="sidebar-brand">
                <div class="brand-icon"><i class="fa-solid fa-users"></i></div>
                <div class="brand-text">
                    <h5>HRMS <span>Sisaket</span></h5>
                    <p>องค์การบริหารส่วนจังหวัดศรีสะเกษ</p>
                </div>
            </a>

            <ul class="sidebar-nav">
                <li class="nav-title">หน้าหลัก</li>
                <li>
                    <a href="index.php?action=dashboard" class="<?= is_active(['dashboard', ''], $current_action) ?>" title="แดชบอร์ด">
                        <i class="fa-solid fa-chart-pie"></i> <span>แดชบอร์ด</span>
                    </a>
                </li>

                <li class="nav-title">ระบบงานบุคลากร</li>
                <li>
                    <a href="index.php?action=employees" class="<?= is_active(['employees', 'employee_create', 'employee_edit', 'employee_detail', 'show'], $current_action) ?>" title="ทะเบียนประวัติ">
                        <i class="fa-regular fa-address-card"></i> <span>ทะเบียนประวัติ (ก.พ.7)</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?action=manpower" class="<?= is_active(['manpower', 'manpower_detail', 'manpower_create', 'manpower_edit'], $current_action) ?>" title="กรอบอัตรากำลัง">
                        <i class="fa-solid fa-sitemap"></i> <span>กรอบอัตรากำลัง</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?action=summary_report" class="<?= is_active(['summary_report', 'manpower_summary'], $current_action) ?>" title="รายงานและสถิติ">
                        <i class="fa-solid fa-chart-column"></i> <span>รายงานและสถิติ</span>
                    </a>
                </li>

                <li class="nav-title">ตั้งค่าระบบ</li>
                <li>
                    <a href="index.php?action=departments" class="<?= is_active(['departments', 'department_create', 'department_edit'], $current_action) ?>" title="จัดการหน่วยงาน">
                        <i class="fa-regular fa-building"></i> <span>จัดการหน่วยงาน</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?action=position_levels" class="<?= is_active(['position_levels'], $current_action) ?>" title="ระดับและสายงาน">
                        <i class="fa-solid fa-layer-group"></i> <span>ระดับและสายงาน</span>
                    </a>
                </li>

                <?php if(isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li class="nav-title">ผู้ดูแลระบบ</li>
                <li>
                    <a href="index.php?action=users" class="<?= is_active(['users'], $current_action) ?>" title="จัดการผู้ใช้งาน">
                        <i class="fa-solid fa-user-shield"></i> <span>จัดการผู้ใช้งาน</span>
                    </a>
                </li>
                <li>
                    <a href="index.php?action=settings" class="<?= is_active(['settings'], $current_action) ?>" title="ตั้งค่าระบบหลัก">
                        <i class="fa-solid fa-gear"></i> <span>ตั้งค่าระบบหลัก</span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="mt-auto p-3">
                <a href="index.php?action=logout" onclick="return confirm('ต้องการออกจากระบบใช่หรือไม่?');" class="logout-link" title="ออกจากระบบ">
                    <i class="fa-solid fa-power-off"></i> <span>ออกจากระบบ</span>
                </a>
            </div>
        </aside>

        <!-- ================= CONTENT WRAPPER ================= -->
        <div id="content-wrapper">
            
            <!-- TOPBAR -->
            <header class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="btn-toggle" id="btnToggleSidebar">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <h5 class="mb-0 fw-bold d-none d-md-block text-dark" style="font-size: 1.05rem; letter-spacing: 0.2px;">ระบบบริหารจัดการบุคลากร (HRMS)</h5>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <button class="btn btn-light position-relative border-0 bg-transparent p-2" type="button" data-bs-toggle="dropdown">
                            <i class="fa-regular fa-bell fs-5 text-secondary"></i>
                            <span class="position-absolute top-1 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="margin-top: 5px; margin-left: -5px;">
                                <span class="visually-hidden">New alerts</span>
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end shadow border-0 p-0 mt-2" style="width: 300px; border-radius: 10px; overflow: hidden;">
                            <div class="bg-light px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark fs-6">การแจ้งเตือน</span>
                                <span class="badge bg-danger rounded-pill">2 ใหม่</span>
                            </div>
                            <div class="p-2">
                                <a class="dropdown-item py-2 px-3 rounded text-wrap d-flex align-items-start gap-3" href="#">
                                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex justify-content-center align-items-center flex-shrink-0" style="width: 36px; height: 36px;">
                                        <i class="fa-solid fa-user-clock"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.85rem;">ผู้เกษียณอายุในปีนี้</h6>
                                        <p class="mb-0 text-muted" style="font-size: 0.75rem;">คุณสมศรี ขยันยิ่ง (อายุ 60 ปี) จะเกษียณในวันที่ 30 ก.ย.</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="dropdown border-start ps-3">
                        <div class="d-flex align-items-center gap-2" style="cursor: pointer;" data-bs-toggle="dropdown">
                            <div class="rounded-circle d-flex align-items-center justify-content-center bg-teal text-white shadow-sm border border-light" style="width: 36px; height: 36px;">
                                <i class="fa-solid fa-user fa-sm"></i>
                            </div>
                            <div class="d-none d-md-block lh-sm">
                                <div class="fw-bold text-dark" style="font-size: 0.85rem;"><?= htmlspecialchars($_SESSION['username'] ?? 'ผู้ดูแลระบบ'); ?></div>
                                <small class="text-muted" style="font-size: 0.7rem;"><?= strtoupper($_SESSION['role'] ?? 'ADMIN'); ?></small>
                            </div>
                            <i class="fa-solid fa-angle-down text-muted ms-1" style="font-size: 0.7rem;"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3" style="border-radius: 10px; min-width: 180px;">
                            <li><a class="dropdown-item py-2" href="#"><i class="fa-regular fa-id-badge text-secondary me-2 w-15"></i> โปรไฟล์ส่วนตัว</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 text-danger fw-bold" href="index.php?action=logout" onclick="return confirm('ออกจากระบบ?');"><i class="fa-solid fa-right-from-bracket me-2 w-15"></i> ออกจากระบบ</a></li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- MAIN CONTENT AREA -->
            <main class="main-content">
                
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?= $_SESSION['message_type']; ?> alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 10px;" role="alert">
                        <i class="fa-solid <?= $_SESSION['message_type'] == 'success' ? 'fa-circle-check text-success' : 'fa-circle-exclamation text-danger' ?> fs-5 me-3"></i>
                        <div class="fw-medium text-dark"><?= $_SESSION['message']; ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                <?php endif; ?>