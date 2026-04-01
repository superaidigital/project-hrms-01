<?php
// ==========================================
// ชื่อไฟล์: footer.php
// ที่อยู่ไฟล์: views/layout/footer.php
// ==========================================
?>
            </main> <!-- End main-content -->
            
            <!-- Footer Section -->
            <footer class="bg-white border-top py-4 px-4 mt-auto">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center text-muted" style="font-size: 0.85rem;">
                    <div class="mb-2 mb-md-0">
                        &copy; <?= date('Y'); ?> <strong class="text-teal">HRMS Sisaket</strong>. All rights reserved.
                    </div>
                    <div>
                        องค์การบริหารส่วนจังหวัดศรีสะเกษ
                    </div>
                </div>
            </footer>
            
        </div> <!-- End content-wrapper -->
    </div> <!-- End wrapper -->

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Core System Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // 1. ลบคลาส preload ออก เพื่อให้แอนิเมชันเริ่มทำงานได้หลังจากจัดหน้าเสร็จแล้ว
            // นี่คือหัวใจสำคัญที่ป้องกัน FOUC (เมนูกระตุกตอนโหลด)
            setTimeout(() => {
                document.body.classList.remove('preload');
            }, 100);

            // 2. จัดการเรื่อง Modal แก้ปัญหาจอดำ/จอเทากดไม่ได้
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => document.body.appendChild(modal));

            document.addEventListener('hidden.bs.modal', function () {
                if (document.querySelectorAll('.modal.show').length === 0) {
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    const backdrops = document.querySelectorAll('.modal-backdrop');
                    backdrops.forEach(bg => bg.remove());
                }
            });

            // 3. ฟังก์ชัน Toggle Sidebar (รองรับทั้ง PC และ Mobile)
            const btnToggle = document.getElementById('btnToggleSidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const htmlElement = document.documentElement;

            function toggleMenu() {
                if (window.innerWidth >= 992) {
                    // Desktop: สลับคลาสที่ <html> tag
                    const isCollapsed = htmlElement.classList.toggle('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', isCollapsed);
                } else {
                    // Mobile: สไลด์เมนู
                    htmlElement.classList.toggle('sidebar-show');
                }
            }

            if (btnToggle) btnToggle.addEventListener('click', toggleMenu);
            if (overlay) overlay.addEventListener('click', toggleMenu);

            // 4. จัดการตอนย่อขยายขนาดจอเบราว์เซอร์
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    htmlElement.classList.remove('sidebar-show');
                }
            });

            // 5. ปิด Alert อัตโนมัติใน 5 วินาที
            setTimeout(function() {
                let alertElements = document.querySelectorAll('.alert:not(.alert-permanent)');
                alertElements.forEach(function(alertNode) {
                    let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                    if (bsAlert) bsAlert.close();
                });
            }, 5000);
            
            // เปิดใช้งาน Tooltip
            const tooltipTriggerList = document.querySelectorAll('[title]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));
        });
    </script>

    <!-- แทรก Script พิเศษเฉพาะบางหน้า (ถ้ามี) -->
    <?php if(isset($extra_scripts)) echo $extra_scripts; ?>
    
</body>
</html>