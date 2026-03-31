<!-- สิ้นสุดเนื้อหาหลัก (ถูกแทรกมาจาก View ต่างๆ) -->
            </div> <!-- End main-container -->
            
            <!-- Footer Section -->
            <footer class="mt-auto py-4 px-4 border-top bg-white bg-opacity-50">
                <div class="container-fluid d-flex flex-column flex-md-row justify-content-between align-items-center text-muted" style="font-size: 0.85rem;">
                    <div class="mb-2 mb-md-0">
                        &copy; <?= date('Y'); ?> <strong>HRMS Sisaket</strong>. All rights reserved.
                    </div>
                    <div>
                        องค์การบริหารส่วนจังหวัดศรีสะเกษ
                    </div>
                </div>
            </footer>
            
        </div> <!-- End page-content -->
    </div> <!-- End wrapper -->

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script ควบคุมระบบทั้งหมด -->
    <script>
        // 1. ==========================================
        // แก้ปัญหา Modal จอเทา / กดอะไรไม่ได้เลยบนหน้าจอ
        // ==========================================
        document.addEventListener('DOMContentLoaded', function() {
            // ย้าย Modal ทั้งหมดไปไว้ที่ <body> ตั้งแต่โหลดเว็บเสร็จ
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                document.body.appendChild(modal);
            });
        });

        // Failsafe: กวาดล้างฉากหลัง (Backdrop) ที่อาจค้างอยู่และบล็อกการคลิกหน้าจอ
        document.addEventListener('hidden.bs.modal', function () {
            // เช็คว่าถ้าไม่มี Modal ไหนเปิดอยู่แล้ว
            if (document.querySelectorAll('.modal.show').length === 0) {
                // ปลดล็อคการเลื่อนหน้าจอ
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                // ค้นหาและลบฉากสีดำใสๆ (backdrop) ที่ค้างอยู่ออกให้หมด
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(bg => bg.remove());
            }
        });

        // 2. ==========================================
        // โหลดสถานะเมนูจาก LocalStorage (ความจำของเบราว์เซอร์)
        // ==========================================
        document.addEventListener('DOMContentLoaded', () => {
            if (window.innerWidth > 992) {
                const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
                if (isCollapsed) {
                    document.getElementById('sidebar').classList.add('collapsed');
                    document.getElementById('page-content').classList.add('expanded');
                }
            }
        });

        // 3. ==========================================
        // ฟังก์ชันสลับการ ย่อ/ขยาย Sidebar
        // ==========================================
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const pageContent = document.getElementById('page-content');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (window.innerWidth <= 992) {
                // สำหรับมือถือ/แท็บเล็ต
                sidebar.classList.toggle('mobile-show');
                overlay.classList.toggle('mobile-show');
            } else {
                // สำหรับคอมพิวเตอร์ Desktop
                sidebar.classList.toggle('collapsed');
                pageContent.classList.toggle('expanded');
                
                // บันทึกสถานะลงใน Browser
                const isCollapsed = sidebar.classList.contains('collapsed');
                localStorage.setItem('sidebarCollapsed', isCollapsed);
            }
        }

        // จัดการเมื่อมีการดึงขอบหน้าจอ (Resize) ป้องกัน UI เพี้ยน
        window.addEventListener('resize', function() {
            const sidebar = document.getElementById('sidebar');
            const pageContent = document.getElementById('page-content');
            const overlay = document.getElementById('sidebar-overlay');

            if (window.innerWidth > 992) {
                sidebar.classList.remove('mobile-show');
                overlay.classList.remove('mobile-show');
                
                if(localStorage.getItem('sidebarCollapsed') === 'true') {
                    sidebar.classList.add('collapsed');
                    pageContent.classList.add('expanded');
                }
            } else {
                sidebar.classList.remove('collapsed');
                pageContent.classList.remove('expanded');
            }
        });

        // 4. ==========================================
        // ฟังก์ชันเสริม: ปิดแจ้งเตือนอัตโนมัติ และเปิดใช้งาน Tooltips
        // ==========================================
        document.addEventListener("DOMContentLoaded", function() {
            // ตั้งเวลาให้ Alert (แจ้งเตือน) ค่อยๆ หายไปเองหลังจาก 5 วินาที
            setTimeout(function() {
                let alertElements = document.querySelectorAll('.alert:not(.alert-permanent)');
                alertElements.forEach(function(alertNode) {
                    let bsAlert = bootstrap.Alert.getOrCreateInstance(alertNode);
                    if (bsAlert) {
                        bsAlert.close();
                    }
                });
            }, 5000);

            // เปิดใช้งาน Bootstrap Tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });
    </script>

    <!-- ส่วนสำหรับให้ View อื่นๆ แทรกสคริปต์เพิ่มเติม (เช่น DataTable, Chart.js) -->
    <?php if(isset($extra_scripts)) echo $extra_scripts; ?>

</body>
</html>