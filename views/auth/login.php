<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - HRMS Sisaket</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts (Prompt) -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: #ffffff;
            border-radius: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(241, 245, 249, 1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            display: flex;
        }
        .login-image {
            background: linear-gradient(135deg, #0f172a, #1e293b);
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            padding: 3rem;
            text-align: center;
        }
        .brand-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #2dd4bf, #059669);
            border-radius: 1.25rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.5rem; color: white;
            margin-bottom: 1.5rem;
            transform: rotate(-3deg);
            box-shadow: 0 10px 15px -3px rgba(20, 184, 166, 0.3);
        }
        .login-form-container {
            flex: 1;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            border: 1px solid #cbd5e1;
        }
        .form-control:focus {
            box-shadow: 0 0 0 0.25rem rgba(45, 212, 191, 0.25);
            border-color: #2dd4bf;
        }
        .btn-primary {
            background: linear-gradient(to right, #14b8a6, #10b981);
            border: none;
            padding: 0.75rem 1rem;
            border-radius: 0.75rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #0d9488, #059669);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        @media (max-width: 768px) {
            .login-card {
                flex-direction: column;
                max-width: 400px;
                margin: 1rem;
            }
            .login-image {
                padding: 2rem;
            }
            .login-form-container {
                padding: 2rem;
            }
        }
    </style>
</head>
<body>

    <div class="login-card">
        <!-- ฝั่งซ้าย: โลโก้และแบรนด์ -->
        <div class="login-image">
            <div class="brand-icon">
                <i class="fa-solid fa-users"></i>
            </div>
            <h2 class="fw-bold mb-2">HRMS Sisaket</h2>
            <p class="text-white-50 mb-0">ระบบบริหารจัดการบุคลากร<br>องค์การบริหารส่วนจังหวัดศรีสะเกษ</p>
        </div>
        
        <!-- ฝั่งขวา: ฟอร์มล็อกอิน -->
        <div class="login-form-container">
            <h3 class="fw-bold text-dark mb-1">เข้าสู่ระบบ</h3>
            <p class="text-muted mb-4">กรุณากรอกข้อมูลเพื่อเข้าใช้งานระบบ</p>

            <!-- แสดงแจ้งเตือนกรณีรหัสผ่านผิด -->
            <?php if(isset($_SESSION['message'])): ?>
                <div class="alert alert-<?= $_SESSION['message_type'] ?? 'danger'; ?> alert-dismissible fade show border-0 shadow-sm" style="border-radius: 0.75rem;" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid <?= isset($_SESSION['message_type']) && $_SESSION['message_type'] == 'success' ? 'fa-check-circle' : 'fa-circle-exclamation'; ?> me-2"></i>
                        <?= $_SESSION['message']; ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
            <?php endif; ?>

            <form action="index.php?action=login" method="POST">
                <div class="mb-3">
                    <label class="form-label fw-semibold text-secondary">ชื่อผู้ใช้งาน (Username)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fa-solid fa-user text-muted"></i></span>
                        <input type="text" name="username" class="form-control border-start-0 ps-0" placeholder="กรอกชื่อผู้ใช้งาน" required autofocus>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="form-label fw-semibold text-secondary">รหัสผ่าน (Password)</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fa-solid fa-lock text-muted"></i></span>
                        <input type="password" name="password" class="form-control border-start-0 ps-0" placeholder="กรอกรหัสผ่าน" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100 fs-6 text-white shadow-sm">
                    เข้าสู่ระบบ <i class="fa-solid fa-arrow-right-to-bracket ms-1"></i>
                </button>
            </form>
            
            <div class="text-center mt-4">
                <small class="text-muted">&copy; <?= date('Y'); ?> องค์การบริหารส่วนจังหวัดศรีสะเกษ</small>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>