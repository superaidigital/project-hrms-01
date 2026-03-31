<?php
// ตรวจสอบการเปิด Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// โหลด Model SystemMenu สำหรับทำ Dynamic Sidebar
require_once __DIR__ . '/../../models/SystemMenu.php';

// ดึงตัวแปรการเชื่อมต่อฐานข้อมูล (รองรับทั้งการเรียกจาก Controller และ Global)
$databaseConn = isset($this->db) ? $this->db : (isset($db) ? $db : null);

$menus_tree = [];
if ($databaseConn) {
    $menuObj = new SystemMenu($databaseConn);
    // ตรวจสอบสิทธิ์ผู้ใช้จาก Session (ถ้ายังไม่ล็อกอินให้เป็น guest หรือ user)
    $userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : (isset($_SESSION['role']) ? $_SESSION['role'] : 'user'); 
    
    // ดึงเฉพาะเมนูที่ Active และตรงกับสิทธิ์
    $activeMenus = $menuObj->readActive($userRole)->fetchAll(PDO::FETCH_ASSOC);

    // จัดกลุ่มเมนูหลักและเมนูย่อย
    foreach($activeMenus as $item) {
        if($item['parent_id'] == 0) {
            $menus_tree[$item['id']] = $item;
            $menus_tree[$item['id']]['sub_menus'] = [];
        } else {
            if(isset($menus_tree[$item['parent_id']])) {
                $menus_tree[$item['parent_id']]['sub_menus'][] = $item;
            }
        }
    }
}

$currentAction = $_GET['action'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HRMS - ระบบบริหารจัดการบุคลากร อบจ.ศรีสะเกษ</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Prompt) -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- SweetAlert2 CSS & JS (สำหรับแจ้งเตือนสวยงาม) -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        /* ================= Global Styles ================= */
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8fafc; 
            color: #1e293b; 
            overflow-x: hidden;
        }
        
        .modern-card {
            background: #ffffff;
            border-radius: 1.5rem; 
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.04);
            border: 1px solid rgba(241, 245, 249, 0.8); 
            padding: 1.5rem;
            transition: all 0.3s;
        }
        .modern-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }
        
        .text-teal { color: #0d9488 !important; }
        .bg-teal { background-color: #0d9488 !important; }

        /* การปรับแต่ง Dropdown ให้สวยงามเข้ากับธีม */
        .dropdown-menu {
            animation: fadeIn 0.2s ease-in-out;
        }
        .dropdown-item:hover {
            background-color: #f1f5f9;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ================= Layout Structure ================= */
        #wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }

        /* ================= Premium Sidebar ================= */
        #sidebar {
            width: 280px;
            min-width: 280px;
            background-color: #0f172a; 
            color: #cbd5e1;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            z-index: 1040;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 10px 0 25px rgba(0,0,0,0.1);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        /* --- สไตล์เมื่อเมนูถูกย่อ (Collapsed) --- */
        #sidebar.collapsed {
            width: 88px;
            min-width: 88px;
        }
        #sidebar.collapsed .menu-text, 
        #sidebar.collapsed .sidebar-brand h1,
        #sidebar.collapsed .sidebar-brand .badge {
            display: none;
        }
        #sidebar.collapsed .sidebar-brand {
            padding: 1.5rem 0.5rem;
        }
        #sidebar.collapsed .brand-icon {
            width: 48px;
            height: 48px;
            font-size: 1.5rem;
            margin-bottom: 0;
        }
        #sidebar.collapsed .sidebar-nav a {
            justify-content: center;
            padding: 0.875rem 0;
        }
        #sidebar.collapsed .sidebar-nav a i {
            margin-right: 0;
            font-size: 1.4rem;
        }

        /* เส้นขอบสีไล่ระดับด้านบนของ Sidebar */
        #sidebar .top-accent {
            position: absolute;
            top: 0; left: 0; width: 100%; height: 4px;
            background: linear-gradient(to right, #2dd4bf, #10b981);
        }

        .sidebar-brand {
            padding: 2.5rem 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: all 0.3s ease;
        }

        .brand-icon {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #2dd4bf, #059669);
            border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 2rem; color: white;
            margin-bottom: 1rem;
            transform: rotate(-3deg);
            box-shadow: 0 10px 15px -3px rgba(20, 184, 166, 0.3);
            transition: all 0.3s ease;
        }

        .sidebar-brand h1 { font-size: 1.25rem; font-weight: 800; color: white; letter-spacing: 0.05em; margin: 0; white-space: nowrap; }
        .sidebar-brand h1 span { color: #2dd4bf; }

        .sidebar-nav {
            padding: 1.25rem;
            list-style: none;
            margin: 0;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }
        .sidebar-nav li { margin-bottom: 0.5rem; }
        .sidebar-nav a {
            display: flex; align-items: center;
            padding: 0.875rem 1rem;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 1rem;
            font-weight: 600;
            transition: all 0.2s;
            white-space: nowrap;
        }
        .sidebar-nav a i { width: 24px; font-size: 1.25rem; margin-right: 0.75rem; text-align: center; transition: all 0.2s; }
        .sidebar-nav a:hover {
            background-color: rgba(255,255,255,0.05);
            color: white;
        }
        .sidebar-nav a.active {
            background: linear-gradient(to right, #14b8a6, #10b981);
            color: white;
            box-shadow: 0 4px 6px -1px rgba(20, 184, 166, 0.3);
        }

        /* ================= Main Content ================= */
        #page-content {
            flex: 1;
            margin-left: 280px;
            width: calc(100% - 280px);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            /* แก้ไขจอเทา: ไม่ใส่ position: relative หรือ z-index: 10 ไว้ตรงนี้ */
        }

        #page-content.expanded {
            margin-left: 88px;
            width: calc(100% - 88px);
        }

        .top-navbar {
            background: transparent;
            padding: 1.5rem 2.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            z-index: 1020;
        }

        .main-container {
            padding: 0 2.5rem 2.5rem 2.5rem;
            flex: 1;
            /* แก้ไขจอเทา: ไม่ใส่ position: relative หรือ z-index: 10 ไว้ตรงนี้ */
        }

        #sidebar-overlay { display: none; }

        /* ================= Responsive Mobile ================= */
        @media (max-width: 992px) {
            #sidebar {
                margin-left: -280px;
            }
            #sidebar.mobile-show {
                margin-left: 0;
            }
            #page-content, #page-content.expanded {
                margin-left: 0;
                width: 100%;
            }
            .top-navbar { padding: 1rem 1.5rem; }
            .main-container { padding: 0 1.5rem 1.5rem 1.5rem; }
            
            #sidebar-overlay.mobile-show {
                display: block; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
                background: rgba(15, 23, 42, 0.4); backdrop-filter: blur(3px); z-index: 1030;
            }
        }
    </style>
</head>
<body>

    <div id="wrapper">
        <!-- ฉากหลังสีดำสำหรับมือถือ -->
        <div id="sidebar-overlay" onclick="toggleSidebar()"></div>

        <!-- Sidebar -->
        <aside id="sidebar">
            <div class="top-accent"></div>
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <h1>HRMS <span>Sisaket</span></h1>
                <span class="badge bg-white bg-opacity-10 text-light mt-3 rounded-pill px-3 py-1 border border-light border-opacity-10" style="letter-spacing: 1px; font-size: 0.65rem;">องค์การบริหารส่วนจังหวัดศรีสะเกษ</span>
            </div>

            <ul class="sidebar-nav d-flex flex-column h-100">
                <?php foreach($menus_tree as $mainMenu): ?>
                    
                    <?php if(empty($mainMenu['sub_menus'])): ?>
                        <!-- เมนูหลักที่ไม่มีเมนูย่อย -->
                        <li>
                            <a href="index.php?action=<?= $mainMenu['action_name']; ?>" 
                               class="<?= ($currentAction == $mainMenu['action_name']) ? 'active' : ''; ?>" title="<?= $mainMenu['menu_name']; ?>">
                                <i class="fa-solid <?= $mainMenu['icon']; ?>"></i> <span class="menu-text"><?= $mainMenu['menu_name']; ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <!-- เมนูหลักที่มีเมนูย่อย (Dropdown) -->
                        <?php 
                            // เช็คว่าเมนูย่อยตัวไหนกำลังถูกเลือกอยู่ (เพื่อขยาย dropdown อัตโนมัติ)
                            $isSubActive = in_array($currentAction, array_column($mainMenu['sub_menus'], 'action_name')); 
                        ?>
                        <li>
                            <a href="#submenu-<?= $mainMenu['id']; ?>" data-bs-toggle="collapse" 
                               class="justify-content-between <?= $isSubActive ? 'active' : ''; ?>" title="<?= $mainMenu['menu_name']; ?>">
                                <div class="d-flex align-items-center">
                                    <i class="fa-solid <?= $mainMenu['icon']; ?>"></i> <span class="menu-text"><?= $mainMenu['menu_name']; ?></span>
                                </div>
                                <!-- ใส่คลาส menu-text ไว้ที่ลูกศรด้วย เพื่อให้ถูกซ่อนอัตโนมัติเมื่อ Sidebar พับ -->
                                <i class="fa-solid fa-chevron-down fs-7 opacity-75 menu-text" style="width: auto; margin-right: 0;"></i>
                            </a>
                            <ul class="collapse list-unstyled <?= $isSubActive ? 'show' : ''; ?> mt-2 mb-2 p-2" id="submenu-<?= $mainMenu['id']; ?>" style="background: rgba(0,0,0,0.2); border-radius: 1rem;">
                                <?php foreach($mainMenu['sub_menus'] as $subMenu): ?>
                                <li>
                                    <a href="index.php?action=<?= $subMenu['action_name']; ?>" 
                                       class="<?= ($currentAction == $subMenu['action_name']) ? 'text-white bg-white bg-opacity-10' : 'text-muted'; ?>" 
                                       style="padding: 0.65rem 1rem; font-size: 0.9rem;" title="<?= $subMenu['menu_name']; ?>">
                                        <i class="fa-solid <?= $subMenu['icon']; ?> fs-6" style="width: 20px;"></i> 
                                        <span class="menu-text"><?= $subMenu['menu_name']; ?></span>
                                    </a>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endif; ?>

                <?php endforeach; ?>

                <!-- ปุ่มออกจากระบบ (ดันไปไว้ล่างสุด) -->
                <li class="mt-auto pt-3 border-top border-secondary border-opacity-25">
                    <a href="index.php?action=logout" class="text-danger hover-danger" onclick="return confirm('ต้องการออกจากระบบใช่หรือไม่?');" title="ออกจากระบบ">
                        <i class="fa-solid fa-right-from-bracket"></i> <span class="menu-text">ออกจากระบบ</span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Page Content -->
        <div id="page-content">
            
            <!-- Top Navbar -->
            <div class="top-navbar">
                <button class="btn btn-light border shadow-sm" onclick="toggleSidebar()" style="border-radius: 0.75rem; position: relative; z-index: 1050;">
                    <i class="fa-solid fa-bars fs-5 text-secondary"></i>
                </button>
                
                <div class="d-flex align-items-center">
                    
                    <!-- 🌟 ส่วนแจ้งเตือน (Notification Dropdown) 🌟 -->
                    <div class="dropdown">
                        <div class="position-relative d-flex align-items-center justify-content-center bg-white border shadow-sm rounded-circle" 
                             style="cursor: pointer; width: 40px; height: 40px;" 
                             data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fa-regular fa-bell fs-5 text-secondary"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-2 border-white" style="font-size: 0.65rem;">
                                2
                            </span>
                        </div>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-0" style="width: 320px; border-radius: 1rem; overflow: hidden;">
                            <li class="px-3 py-3 border-bottom bg-light d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-dark">การแจ้งเตือน</span>
                                <span class="badge bg-danger rounded-pill">2 ใหม่</span>
                            </li>
                            <!-- รายการที่ 1: เกษียณอายุ -->
                            <li>
                                <a class="dropdown-item py-3 border-bottom text-wrap" href="#">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="fa-solid fa-user-clock fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">ผู้เกษียณอายุในปีนี้</h6>
                                            <p class="mb-1 text-muted" style="font-size: 0.8rem;">คุณสมศรี ขยันยิ่ง (อายุ 60 ปี) จะเกษียณในวันที่ 30 ก.ย.</p>
                                            <small class="text-primary fw-bold" style="font-size: 0.75rem;">ดูรายละเอียด <i class="fa-solid fa-arrow-right fa-xs"></i></small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <!-- รายการที่ 2: ตำแหน่งว่าง -->
                            <li>
                                <a class="dropdown-item py-3 text-wrap" href="index.php?action=manpower">
                                    <div class="d-flex align-items-start">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex justify-content-center align-items-center me-3 shadow-sm" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="fa-solid fa-user-xmark fs-5"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.9rem;">ตำแหน่งว่างรอสรรหา</h6>
                                            <p class="mb-1 text-muted" style="font-size: 0.8rem;">ตำแหน่ง นิติกร (กองการเจ้าหน้าที่) สถานะว่าง</p>
                                            <small class="text-primary fw-bold" style="font-size: 0.75rem;">จัดการอัตรากำลัง <i class="fa-solid fa-arrow-right fa-xs"></i></small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                            <li class="border-top text-center bg-light">
                                <a class="dropdown-item text-teal fw-bold py-2" style="font-size: 0.85rem;" href="#">ดูการแจ้งเตือนทั้งหมด</a>
                            </li>
                        </ul>
                    </div>

                    <!-- 🌟 ส่วนโปรไฟล์ผู้ใช้ (User Dropdown) 🌟 -->
                    <div class="dropdown ms-3 ps-3 border-start">
                        <div class="d-flex align-items-center gap-2" style="cursor: pointer;" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div class="d-none d-md-block lh-1">
                                <div class="fw-bold" style="color: #1e293b; font-size: 0.95rem;"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'ผู้ดูแลระบบ'); ?></div>
                                <small class="text-muted fw-medium" style="font-size: 0.75rem;"><?php echo ucfirst($_SESSION['role'] ?? 'Admin'); ?></small>
                            </div>
                            <i class="fa-solid fa-chevron-down text-muted ms-1 fs-6 d-none d-md-block"></i>
                        </div>
                        
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-3" style="border-radius: 1rem; min-width: 200px;">
                            <li class="px-3 py-2 border-bottom bg-light d-md-none">
                                <div class="fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'ผู้ดูแลระบบ'); ?></div>
                                <small class="text-muted"><?php echo ucfirst($_SESSION['role'] ?? 'Admin'); ?></small>
                            </li>
                            <li><a class="dropdown-item py-2 mt-1" href="#"><i class="fa-regular fa-id-badge text-secondary me-2 w-15"></i> โปรไฟล์ของฉัน</a></li>
                            <li><a class="dropdown-item py-2" href="#"><i class="fa-solid fa-key text-secondary me-2 w-15"></i> เปลี่ยนรหัสผ่าน</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item py-2 mb-1 text-danger fw-bold" href="index.php?action=logout" onclick="return confirm('ต้องการออกจากระบบใช่หรือไม่?');"><i class="fa-solid fa-right-from-bracket me-2 w-15"></i> ออกจากระบบ</a></li>
                        </ul>
                    </div>

                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-container">
                
                <!-- แสดงแจ้งเตือน (Alerts) -->
                <?php if (isset($_SESSION['message'])): ?>
                    <div class="alert alert-<?php echo $_SESSION['message_type']; ?> alert-dismissible fade show border-0 shadow-sm mb-4 d-flex align-items-center" style="border-radius: 1rem;" role="alert">
                        <?php if($_SESSION['message_type'] == 'success'): ?>
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3 shrink-0" style="width: 32px; height: 32px;"><i class="fa-solid fa-check"></i></div>
                        <?php else: ?>
                            <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center me-3 shrink-0" style="width: 32px; height: 32px;"><i class="fa-solid fa-exclamation"></i></div>
                        <?php endif; ?>
                        
                        <div class="fw-bold text-dark"><?php echo $_SESSION['message']; ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                <?php endif; ?>

                <!-- 🌟 เนื้อหาแต่ละหน้าจะถูกแทรกต่อจากบรรทัดนี้ 🌟 -->