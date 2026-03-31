<div class="container-fluid">
    <style>
        /* สไตล์ช่องค้นหาแบบใหม่ (Modern Pill Shape) */
        .search-wrapper {
            position: relative;
            width: 100%;
            min-width: 320px;
        }
        .search-wrapper .form-control {
            border-radius: 30px;
            padding-left: 20px;
            padding-right: 80px; /* เว้นที่ให้ปุ่ม X และแว่นขยาย */
            border: 1px solid #e3e6f0;
            background-color: #f8f9fc;
            transition: all 0.3s ease;
            height: 45px;
            font-size: 0.9rem;
        }
        .search-wrapper .form-control:focus {
            background-color: #fff;
            box-shadow: 0 0 15px rgba(78, 115, 223, 0.15);
            border-color: #bac8f3;
        }
        .search-wrapper .btn-search {
            position: absolute;
            right: 5px;
            top: 5px;
            height: 35px;
            width: 35px;
            border-radius: 50%;
            background: #4e73df;
            color: white;
            border: none;
            z-index: 4;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 5px rgba(78, 115, 223, 0.3);
            transition: transform 0.2s;
        }
        .search-wrapper .form-control:focus ~ .btn-search {
            transform: scale(1.05); /* ขยายปุ่มเล็กน้อยเวลาคลิกที่ช่องค้นหา */
        }
        .search-wrapper .btn-clear {
            position: absolute;
            right: 45px;
            top: 5px;
            height: 35px;
            width: 30px;
            background: transparent;
            border: none;
            color: #b7b9cc;
            z-index: 5;
            display: none; /* ซ่อนไว้ก่อน จะแสดงตอนพิมพ์ */
            text-align: center;
            line-height: 35px;
            padding: 0;
            transition: color 0.2s;
            outline: none;
        }
        .search-wrapper .btn-clear:hover {
            color: #e74a3b;
        }
    </style>

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-pie mr-2 text-primary"></i>รายงานสรุปกรอบอัตรากำลัง
            <span class="d-block d-sm-inline-block text-sm text-muted mt-2 mt-sm-0 ml-sm-2" style="font-size: 0.6em;">
                (แยกตามประเภทหน่วยงาน)
            </span>
        </h1>
        
        <!-- ช่องค้นหาแบบ Global (UI ใหม่) -->
        <div class="mt-3 mt-sm-0 search-wrapper">
            <input type="text" class="form-control" id="customGlobalSearch" placeholder="พิมพ์ชื่อหน่วยงานเพื่อค้นหา..." aria-label="Search">
            <button class="btn-clear" id="clearSearch" type="button" title="ล้างคำค้นหา">
                <i class="fas fa-times"></i>
            </button>
            <button class="btn-search" type="button" style="pointer-events: none;">
                <i class="fas fa-search fa-sm"></i>
            </button>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- กรอบอัตรากำลังรวม -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">กรอบอัตรากำลังรวม (ทั้งหมด)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_fw) ?> อัตรา</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- มีคนครอง -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">มีผู้ดำรงตำแหน่ง (คนครอง)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_actual) ?> อัตรา</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-check fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- อัตราว่าง -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">อัตราว่างรวม</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= number_format($total_fw - $total_actual) ?> อัตรา</div>
                        </div>
                        <div class="col-auto"><i class="fas fa-user-times fa-2x text-gray-300"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation & Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active font-weight-bold text-primary" id="central-tab" data-toggle="tab" href="#central" role="tab" aria-controls="central" aria-selected="true">
                        <i class="fas fa-building mr-1"></i> ส่วนราชการส่วนกลาง 
                        <span class="badge badge-primary ml-1"><?= count($central) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-success" id="school-tab" data-toggle="tab" href="#school" role="tab" aria-controls="school" aria-selected="false">
                        <i class="fas fa-school mr-1"></i> สถานศึกษาในสังกัด 
                        <span class="badge badge-success ml-1"><?= count($school) ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link font-weight-bold text-info" id="health-tab" data-toggle="tab" href="#health" role="tab" aria-controls="health" aria-selected="false">
                        <i class="fas fa-hospital mr-1"></i> หน่วยบริการสาธารณสุข 
                        <span class="badge badge-info ml-1"><?= count($health) ?></span>
                    </a>
                </li>
            </ul>
        </div>
        
        <div class="card-body">
            <div class="tab-content" id="myTabContent">
                
                <?php
                // ฟังก์ชันสร้างตาราง
                function renderTable($data, $tableId) {
                    $html = '<div class="table-responsive">
                                <table class="table table-bordered table-hover" id="'.$tableId.'" width="100%" cellspacing="0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center align-middle" width="5%">ลำดับ</th>
                                            <th class="align-middle">ชื่อส่วนราชการ/หน่วยงาน</th>
                                            <th class="text-center align-middle" width="15%">กรอบอัตรา (คน)</th>
                                            <th class="text-center align-middle" width="15%">มีคนครอง (คน)</th>
                                            <th class="text-center align-middle" width="15%">อัตราว่าง (คน)</th>
                                            <th class="text-center align-middle" width="10%">จัดการ</th>
                                        </tr>
                                    </thead>
                                    <tbody>';
                    
                    if(empty($data)) {
                        $html .= '<tr><td colspan="6" class="text-center text-muted py-5">ไม่มีข้อมูลในหมวดหมู่นี้</td></tr>';
                    } else {
                        $sumFw = 0; $sumAc = 0; $sumVac = 0;
                        foreach($data as $index => $row) {
                            $vacant = $row['framework_count'] - $row['actual_count'];
                            $sumFw += $row['framework_count'];
                            $sumAc += $row['actual_count'];
                            $sumVac += $vacant;
                            
                            $vacantClass = $vacant > 0 ? 'text-danger font-weight-bold' : 'text-secondary';
                            $actualClass = $row['actual_count'] > 0 ? 'text-success font-weight-bold' : 'text-secondary';
                            
                            $detail_url = 'index.php?action=manpower_detail&dept=' . urlencode($row['department_name']);
                            
                            $html .= '<tr>
                                        <td class="text-center align-middle text-muted">'.($index + 1).'</td>
                                        <td class="align-middle">
                                            <a href="'.$detail_url.'" class="text-primary font-weight-bold text-decoration-none search-target">
                                                '.htmlspecialchars($row['department_name']).'
                                            </a>
                                        </td>
                                        <td class="text-center align-middle font-weight-bold text-dark">'.number_format($row['framework_count']).'</td>
                                        <td class="text-center align-middle '.$actualClass.'">'.number_format($row['actual_count']).'</td>
                                        <td class="text-center align-middle '.$vacantClass.'">'.number_format($vacant).'</td>
                                        <td class="text-center align-middle">
                                            <a href="'.$detail_url.'" class="btn btn-info btn-sm shadow-sm" title="ดูข้อมูล">
                                                <i class="fas fa-search"></i>
                                            </a>
                                        </td>
                                      </tr>';
                        }
                        // แถวสรุปผลรวม
                        $html .= '<tr class="bg-light font-weight-bold table-summary-row">
                                    <td colspan="2" class="text-right text-dark">รวมทั้งสิ้น</td>
                                    <td class="text-center text-primary" style="font-size: 1.1em;">'.number_format($sumFw).'</td>
                                    <td class="text-center text-success" style="font-size: 1.1em;">'.number_format($sumAc).'</td>
                                    <td class="text-center text-danger" style="font-size: 1.1em;">'.number_format($sumVac).'</td>
                                    <td></td>
                                  </tr>';
                    }
                    $html .= '</tbody></table></div>';
                    return $html;
                }
                ?>

                <!-- Tab 1: ส่วนกลาง -->
                <div class="tab-pane fade show active" id="central" role="tabpanel" aria-labelledby="central-tab">
                    <?= renderTable($central, 'dataTableCentral') ?>
                </div>

                <!-- Tab 2: สถานศึกษา -->
                <div class="tab-pane fade" id="school" role="tabpanel" aria-labelledby="school-tab">
                    <?= renderTable($school, 'dataTableSchool') ?>
                </div>

                <!-- Tab 3: สาธารณสุข -->
                <div class="tab-pane fade" id="health" role="tabpanel" aria-labelledby="health-tab">
                     <?= renderTable($health, 'dataTableHealth') ?>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- สคริปต์แก้ไขบัคถาวร: ไม่พึ่งพา Plugin ภายนอก 100% -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    
    // ==========================================
    // 1. ระบบจัดการ Tabs (ทำงานทันที ไม่ต้องรอ jQuery)
    // ==========================================
    var tabLinks = document.querySelectorAll('#myTab a[data-toggle="tab"]');
    var tabPanes = document.querySelectorAll('.tab-pane');

    tabLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault(); // ป้องกันการกระโดดของหน้าเว็บ

            // ลบสถานะ Active เดิมออกให้หมด
            tabLinks.forEach(function(l) { l.classList.remove('active'); });
            tabPanes.forEach(function(p) { p.classList.remove('show', 'active'); });

            // ตั้งค่าสถานะ Active ให้กับ Tab ที่ถูกคลิก
            this.classList.add('active');
            var targetId = this.getAttribute('href').substring(1); // เอาเครื่องหมาย # ออก
            var targetPane = document.getElementById(targetId);
            if(targetPane) {
                targetPane.classList.add('show', 'active');
            }

            // ถ้า DataTables โหลดผ่าน ให้รีเซ็ตขนาดตารางป้องกันบัคบีบตัว
            if (window.jQuery && window.jQuery.fn && window.jQuery.fn.dataTable) {
                window.jQuery.fn.dataTable.tables({ visible: true, api: true }).columns.adjust();
            }
        });
    });

    // ==========================================
    // 2. ระบบค้นหา (พยายามใช้ DataTables ก่อน ถ้าไม่มีจะใช้ระบบพื้นฐาน)
    // ==========================================
    var searchInput = document.getElementById('customGlobalSearch');
    var clearBtn = document.getElementById('clearSearch');
    
    var checkAttempts = 0;
    var initScripts = setInterval(function() {
        checkAttempts++;
        
        // หากระบบมี DataTables ถูกโหลดครบสมบูรณ์
        if (window.jQuery && window.jQuery.fn && window.jQuery.fn.DataTable) {
            clearInterval(initScripts);
            
            var jq = window.jQuery;
            var dtConfig = {
                "language": { "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Thai.json" },
                "paging": false, 
                "info": false,
                "searching": true,
                "dom": 'rt',
                "order": [[ 1, "asc" ]], 
                "columnDefs": [{ "orderable": false, "targets": [5] }]
            };

            var tableCentral = jq('#dataTableCentral').DataTable(dtConfig);
            var tableSchool = jq('#dataTableSchool').DataTable(dtConfig);
            var tableHealth = jq('#dataTableHealth').DataTable(dtConfig);

            jq(searchInput).on('input', function() {
                var searchTerm = jq(this).val();
                if(searchTerm.length > 0) { jq(clearBtn).show(); } else { jq(clearBtn).hide(); }
                tableCentral.search(searchTerm).draw();
                tableSchool.search(searchTerm).draw();
                tableHealth.search(searchTerm).draw();
            });

            jq(clearBtn).on('click', function() {
                jq(searchInput).val('').trigger('input');
                jq(searchInput).focus();
            });

        // หากรอจนครบ 5 วินาทีแล้วไม่มี DataTables ให้เปลี่ยนไปใช้ระบบค้นหาสำรอง (Vanilla JS)
        } else if (checkAttempts > 50) { 
            clearInterval(initScripts);
            console.warn("DataTables library not found. Falling back to native search.");
            
            // ระบบค้นหาสำรอง (ทำงานได้โดยไม่ต้องมี jQuery)
            var allRows = document.querySelectorAll('tbody tr:not(.table-summary-row)');
            
            searchInput.addEventListener('input', function() {
                var term = this.value.toLowerCase().trim();
                
                if(term.length > 0) { clearBtn.style.display = 'block'; } else { clearBtn.style.display = 'none'; }

                allRows.forEach(function(row) {
                    var targetName = row.querySelector('.search-target');
                    if (targetName) {
                        var text = targetName.textContent.toLowerCase();
                        if (text.includes(term)) {
                            row.style.display = ''; // แสดงแถว
                        } else {
                            row.style.display = 'none'; // ซ่อนแถว
                        }
                    }
                });
            });

            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input')); // ทริกเกอร์ให้เคลียร์หน้าจอ
                searchInput.focus();
            });
        }
    }, 100);
});
</script>