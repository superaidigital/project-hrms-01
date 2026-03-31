<?php include 'views/layout/header.php'; ?>
<?php
// ตรวจสอบว่าเป็นการแก้ไข (Edit) หรือ เพิ่มใหม่ (Create)
$is_edit = isset($department);
$action_url = $is_edit ? "index.php?action=department_update" : "index.php?action=department_store";

// ค่าเริ่มต้น
$d_type = $is_edit ? $department['dept_type'] : 'central';
$d_amphoe = $is_edit ? $department['amphoe'] : '';
$d_status = $is_edit ? $department['status'] : 'active';

// รายชื่ออำเภอ
$amphoes = ["เมืองศรีสะเกษ", "ยางชุมน้อย", "กันทรารมย์", "กันทรลักษ์", "ขุขันธ์", "ไพรบึง", "ปรางค์กู่", "ขุนหาญ", "ราษีไศล", "อุทุมพรพิสัย", "บึงบูรพ์", "ห้วยทับทัน", "โนนคูณ", "ศรีรัตนะ", "น้ำเกลี้ยง", "วังหิน", "ภูสิงห์", "เมืองจันทร์", "เบญจลักษ์", "พยุห์", "โพธิ์ศรีสุวรรณ", "ศิลาลาด"];
?>

<div class="container-fluid mb-5">
    
    <!-- Header -->
    <div class="d-flex align-items-center mb-4">
        <a href="index.php?action=departments" class="btn btn-light border mr-3 rounded-circle shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h2 class="h3 font-weight-bold text-dark mb-0">
                <?= $is_edit ? 'แก้ไขข้อมูลส่วนราชการ' : 'เพิ่มส่วนราชการ/หน่วยงานใหม่' ?>
            </h2>
            <p class="text-muted mb-0">บันทึกข้อมูลรายละเอียดหน่วยงานและพิกัดสถานที่</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <form action="<?= $action_url ?>" method="POST">
                <?php if($is_edit): ?>
                    <input type="hidden" name="id" value="<?= $department['id'] ?>">
                <?php endif; ?>

                <!-- Section 1: โครงสร้างและประเภท -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom pt-4 pb-3">
                        <h5 class="font-weight-bold text-primary mb-0"><i class="fas fa-sitemap mr-2"></i> 1. โครงสร้างและประเภท</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="font-weight-bold text-dark mb-3">เลือกโครงสร้าง/ประเภทหน่วยงาน <span class="text-danger">*</span></label>
                            <div class="row">
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-radio custom-control-inline w-100 p-0">
                                        <input type="radio" id="type_central" name="dept_type" class="custom-control-input d-none dept-type-radio" value="central" <?= $d_type == 'central' ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-primary w-100 text-left px-3 py-3" for="type_central" style="border-radius: 10px;">
                                            <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                            <span class="font-weight-bold d-block">ส่วนกลาง</span>
                                            <small class="text-muted">สำนัก, กองต่างๆ</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-radio custom-control-inline w-100 p-0">
                                        <input type="radio" id="type_school" name="dept_type" class="custom-control-input d-none dept-type-radio" value="school" <?= $d_type == 'school' ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-success w-100 text-left px-3 py-3" for="type_school" style="border-radius: 10px;">
                                            <i class="fas fa-school fa-2x mb-2 d-block"></i>
                                            <span class="font-weight-bold d-block">สังกัดโรงเรียน</span>
                                            <small class="text-muted">ต้องระบุอำเภอ</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-2">
                                    <div class="custom-control custom-radio custom-control-inline w-100 p-0">
                                        <input type="radio" id="type_health" name="dept_type" class="custom-control-input d-none dept-type-radio" value="health" <?= $d_type == 'health' ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-warning w-100 text-left px-3 py-3" for="type_health" style="border-radius: 10px;">
                                            <i class="fas fa-hospital fa-2x mb-2 d-block"></i>
                                            <span class="font-weight-bold d-block">รพ.สต.</span>
                                            <small class="text-muted">ต้องระบุอำเภอ</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-0" id="amphoeWrapper" style="display: <?= in_array($d_type, ['health', 'school']) ? 'block' : 'none' ?>;">
                            <label class="font-weight-bold text-dark" for="amphoe">ตั้งอยู่ในอำเภอใด? (รพ.สต. / โรงเรียน) <span class="text-danger">*</span></label>
                            <select class="form-control" id="amphoe" name="amphoe" style="border-radius: 8px; max-width: 400px;" <?= in_array($d_type, ['health', 'school']) ? 'required' : '' ?>>
                                <option value="" disabled <?= empty($d_amphoe) ? 'selected' : '' ?>>-- เลือกอำเภอ --</option>
                                <?php foreach($amphoes as $amp): ?>
                                    <option value="<?= $amp ?>" <?= $d_amphoe == $amp ? 'selected' : '' ?>><?= $amp ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Section 2: ข้อมูลรายละเอียดหน่วยงาน -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom pt-4 pb-3">
                        <h5 class="font-weight-bold text-success mb-0"><i class="fas fa-info-circle mr-2"></i> 2. ข้อมูลทั่วไปของหน่วยงาน</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark" for="name">ชื่อส่วนราชการ / หน่วยงาน (เต็ม) <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?= htmlspecialchars($department['name'] ?? '') ?>" required 
                                           placeholder="เช่น กองยุทธศาสตร์และงบประมาณ" style="border-radius: 8px;">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark" for="short_name">ชื่อย่อ (ถ้ามี)</label>
                                    <input type="text" class="form-control" id="short_name" name="short_name" 
                                           value="<?= htmlspecialchars($department['short_name'] ?? '') ?>" 
                                           placeholder="เช่น กย." style="border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-0">
                                    <label class="font-weight-bold text-dark" for="dept_code">รหัสหน่วยงาน (ระบบอื่น)</label>
                                    <input type="text" class="form-control" id="dept_code" name="dept_code" 
                                           value="<?= htmlspecialchars($department['dept_code'] ?? '') ?>" 
                                           placeholder="เช่น รหัสสถานศึกษา" style="border-radius: 8px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: ข้อมูลการติดต่อและพิกัดแผนที่ -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-white border-bottom pt-4 pb-3 d-flex justify-content-between align-items-center">
                        <h5 class="font-weight-bold text-info mb-0"><i class="fas fa-map-marked-alt mr-2"></i> 3. ข้อมูลการติดต่อ / ที่ตั้งและพิกัด</h5>
                        <button type="button" id="btn-get-location" class="btn btn-sm btn-outline-info" style="border-radius: 20px;">
                            <i class="fas fa-crosshairs mr-1"></i> ดึงพิกัดปัจจุบัน
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark" for="phone">เบอร์โทรศัพท์ติดต่อ</label>
                                    <input type="text" class="form-control" id="phone" name="phone" 
                                           value="<?= htmlspecialchars($department['phone'] ?? '') ?>" 
                                           placeholder="เช่น 045-123456 ต่อ 11" style="border-radius: 8px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark" for="email">อีเมลหน่วยงาน</label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?= htmlspecialchars($department['email'] ?? '') ?>" 
                                           placeholder="เช่น admin@school.ac.th" style="border-radius: 8px;">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark" for="address">ที่อยู่ตั้งทำการ</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" 
                                              placeholder="บ้านเลขที่ หมู่ที่ ตำบล..." style="border-radius: 8px;"><?= htmlspecialchars($department['address'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- เพิ่มช่องพิกัด GPS -->
                        <div class="row bg-light pt-3 pb-2 px-2" style="border-radius: 8px; border: 1px dashed #bee3f8;">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark" for="latitude"><i class="fas fa-map-pin text-danger mr-1"></i> ละติจูด (Latitude)</label>
                                    <input type="text" class="form-control" id="latitude" name="latitude" 
                                           value="<?= htmlspecialchars($department['latitude'] ?? '') ?>" 
                                           placeholder="เช่น 15.118600" style="border-radius: 8px;">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="font-weight-bold text-dark" for="longitude"><i class="fas fa-map-pin text-primary mr-1"></i> ลองจิจูด (Longitude)</label>
                                    <input type="text" class="form-control" id="longitude" name="longitude" 
                                           value="<?= htmlspecialchars($department['longitude'] ?? '') ?>" 
                                           placeholder="เช่น 104.322000" style="border-radius: 8px;">
                                </div>
                            </div>
                            <div class="col-12 text-right">
                                <a href="https://www.google.co.th/maps" target="_blank" class="text-info small text-decoration-none">
                                    <i class="fas fa-external-link-alt"></i> ค้นหาพิกัดจาก Google Maps
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: การตั้งค่า -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4 bg-light" style="border-radius: 12px;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="font-weight-bold text-dark mb-1">สถานะการใช้งานระบบ</h6>
                                <p class="text-muted mb-0 small">การปิดใช้งานจะทำให้ไม่สามารถเลือกหน่วยงานนี้ในหน้าเพิ่มบุคลากรได้</p>
                            </div>
                            <div>
                                <select class="form-control font-weight-bold" name="status" style="border-radius: 8px; width: 220px; <?= $d_status == 'active' ? 'color: #16a34a; border-color: #16a34a;' : 'color: #64748b;' ?>">
                                    <option value="active" <?= $d_status == 'active' ? 'selected' : '' ?>>🟢 เปิดใช้งาน (Active)</option>
                                    <option value="inactive" <?= $d_status == 'inactive' ? 'selected' : '' ?>>🔴 ปิดใช้งาน (Inactive)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- ปุ่ม Submit -->
                <div class="d-flex justify-content-end mb-5">
                    <a href="index.php?action=departments" class="btn btn-light border btn-lg mr-3 font-weight-bold shadow-sm" style="border-radius: 8px;">ยกเลิก</a>
                    <button type="submit" class="btn text-white btn-lg font-weight-bold px-5 shadow-sm" style="background-color: #0d9488; border-radius: 8px;">
                        <i class="fas fa-save mr-2"></i> บันทึกข้อมูลหน่วยงาน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .dept-type-radio:checked + label {
        border-width: 2px !important; background-color: rgba(0,0,0,0.03); box-shadow: 0 0 0 0.2rem rgba(38, 143, 255, 0.25);
    }
    .dept-type-radio:checked + label.btn-outline-primary { box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25); background-color: #eff6ff;}
    .dept-type-radio:checked + label.btn-outline-success { box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25); background-color: #f0fdf4;}
    .dept-type-radio:checked + label.btn-outline-warning { box-shadow: 0 0 0 0.2rem rgba(255, 193, 7, 0.25); background-color: #fffbeb;}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. ระบบจัดการ UI ประเภทหน่วยงาน
    const radios = document.querySelectorAll('.dept-type-radio');
    const amphoeWrapper = document.getElementById('amphoeWrapper');
    const amphoeInput = document.getElementById('amphoe');
    const nameInput = document.getElementById('name');

    function updateFormUI(type) {
        if (type === 'health' || type === 'school') {
            amphoeWrapper.style.display = 'block';
            amphoeInput.setAttribute('required', 'required');
            nameInput.placeholder = type === 'health' ? "เช่น รพ.สต.หนองไผ่ (ไม่ต้องใส่อำเภอ)" : "เช่น โรงเรียนราษีไศล";
        } else {
            amphoeWrapper.style.display = 'none';
            amphoeInput.removeAttribute('required');
            nameInput.placeholder = "เช่น กองยุทธศาสตร์และงบประมาณ";
        }
    }

    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            if(this.checked) updateFormUI(this.value);
        });
    });

    // 2. ระบบ GPS ดึงพิกัดปัจจุบัน
    const btnGetLocation = document.getElementById('btn-get-location');
    const inputLat = document.getElementById('latitude');
    const inputLng = document.getElementById('longitude');

    btnGetLocation.addEventListener('click', function() {
        if (navigator.geolocation) {
            btnGetLocation.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i> กำลังดึงพิกัด...';
            btnGetLocation.classList.add('disabled');
            
            navigator.geolocation.getCurrentPosition(function(position) {
                // สำเร็จ
                inputLat.value = position.coords.latitude.toFixed(6);
                inputLng.value = position.coords.longitude.toFixed(6);
                
                btnGetLocation.innerHTML = '<i class="fas fa-check text-success mr-1"></i> ดึงพิกัดสำเร็จ';
                setTimeout(() => {
                    btnGetLocation.innerHTML = '<i class="fas fa-crosshairs mr-1"></i> ดึงพิกัดปัจจุบัน';
                    btnGetLocation.classList.remove('disabled');
                }, 2000);
            }, function(error) {
                // ไม่สำเร็จ (เช่น ผู้ใช้ไม่กดยอมรับการเข้าถึงตำแหน่ง)
                alert('ไม่สามารถดึงพิกัดได้: กรุณาอนุญาตการเข้าถึงตำแหน่ง (Location) ในเบราว์เซอร์ของคุณ');
                btnGetLocation.innerHTML = '<i class="fas fa-crosshairs mr-1"></i> ดึงพิกัดปัจจุบัน';
                btnGetLocation.classList.remove('disabled');
            });
        } else {
            alert("เบราว์เซอร์ของคุณไม่รองรับการดึงพิกัด GPS");
        }
    });
});
</script>

<?php include 'views/layout/footer.php'; ?>