<?php include 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-user-shield text-primary me-2"></i>จัดการผู้ใช้งานระบบ</h2>
        <p class="text-muted mb-0">เพิ่ม ลบ แก้ไข และกำหนดสิทธิ์การเข้าถึงระบบ</p>
    </div>
    <button class="btn btn-primary fw-semibold shadow-sm px-4" style="border-radius: 0.75rem;" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="fa-solid fa-user-plus me-1"></i> เพิ่มผู้ใช้งาน
    </button>
</div>

<div class="modern-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th width="50" class="text-center">#</th>
                    <th>ชื่อ-สกุล (ผู้ใช้งาน)</th>
                    <th>Username</th>
                    <th class="text-center">ระดับสิทธิ์ (Role)</th>
                    <th class="text-center">สถานะ</th>
                    <th class="text-end pe-3">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1; foreach($users as $user): ?>
                <tr>
                    <td class="text-center text-muted"><?= $i++; ?></td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex justify-content-center align-items-center me-3" style="width: 40px; height: 40px;">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <span class="fw-bold text-dark"><?= $user['full_name']; ?></span>
                        </div>
                    </td>
                    <td class="text-muted fw-medium"><?= $user['username']; ?></td>
                    <td class="text-center">
                        <?php if($user['role'] == 'admin'): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill"><i class="fa-solid fa-crown me-1"></i> ผู้ดูแลระบบ</span>
                        <?php elseif($user['role'] == 'hr'): ?>
                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill"><i class="fa-solid fa-users-gear me-1"></i> เจ้าหน้าที่ HR</span>
                        <?php else: ?>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 px-3 py-2 rounded-pill"><i class="fa-solid fa-user me-1"></i> ผู้ใช้งานทั่วไป</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if($user['is_active'] == '1'): ?>
                            <a href="index.php?action=user_toggle&id=<?= $user['id']; ?>&status=0" class="badge bg-success text-decoration-none px-3 py-2 rounded-pill shadow-sm" title="คลิกเพื่อระงับการใช้งาน">เปิดใช้งาน</a>
                        <?php else: ?>
                            <a href="index.php?action=user_toggle&id=<?= $user['id']; ?>&status=1" class="badge bg-secondary text-decoration-none px-3 py-2 rounded-pill shadow-sm" title="คลิกเพื่อเปิดการใช้งาน">ระงับใช้งาน</a>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3">
                        <!-- ปุ่มแก้ไข -->
                        <button class="btn btn-sm btn-light text-primary border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id']; ?>" title="แก้ไขข้อมูล">
                            <i class="fa-solid fa-pen"></i>
                        </button>
                        <!-- ปุ่มรีเซ็ตรหัสผ่าน -->
                        <button class="btn btn-sm btn-light text-warning border shadow-sm me-1" data-bs-toggle="modal" data-bs-target="#resetPwdModal<?= $user['id']; ?>" title="รีเซ็ตรหัสผ่าน">
                            <i class="fa-solid fa-key"></i>
                        </button>
                        <!-- ปุ่มลบ -->
                        <?php if($_SESSION['user_id'] != $user['id']): // ห้ามลบตัวเอง ?>
                        <a href="index.php?action=user_delete&id=<?= $user['id']; ?>" class="btn btn-sm btn-light text-danger border shadow-sm" onclick="return confirm('ยืนยันการลบผู้ใช้งาน <?= $user['full_name']; ?> ออกจากระบบอย่างถาวร?');" title="ลบผู้ใช้">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                        <?php else: ?>
                        <button class="btn btn-sm btn-light text-muted border shadow-sm" disabled title="ไม่สามารถลบตัวเองได้"><i class="fa-solid fa-trash"></i></button>
                        <?php endif; ?>
                    </td>
                </tr>

                <!-- Modal แก้ไขข้อมูลผู้ใช้ -->
                <div class="modal fade" id="editUserModal<?= $user['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="border-radius: 1rem;">
                            <form action="index.php?action=user_update" method="POST">
                                <div class="modal-header bg-light border-bottom-0" style="border-radius: 1rem 1rem 0 0;">
                                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen me-2 text-primary"></i>แก้ไขข้อมูลผู้ใช้งาน</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $user['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Username (ไม่สามารถแก้ได้)</label>
                                        <input type="text" class="form-control bg-light" value="<?= $user['username']; ?>" readonly>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                        <input type="text" name="full_name" class="form-control" value="<?= $user['full_name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">ระดับสิทธิ์ (Role) <span class="text-danger">*</span></label>
                                        <select name="role" class="form-select" required>
                                            <option value="user" <?= $user['role'] == 'user' ? 'selected' : ''; ?>>ผู้ใช้งานทั่วไป (User)</option>
                                            <option value="hr" <?= $user['role'] == 'hr' ? 'selected' : ''; ?>>เจ้าหน้าที่ (HR)</option>
                                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : ''; ?>>ผู้ดูแลระบบ (Admin)</option>
                                        </select>
                                    </div>
                                    <div class="form-check form-switch mt-4 bg-light p-3 rounded border">
                                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="activeEdit<?= $user['id']; ?>" <?= $user['is_active'] == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-semibold" for="activeEdit<?= $user['id']; ?>">อนุญาตให้ผู้ใช้นี้เข้าสู่ระบบได้</label>
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">ยกเลิก</button>
                                    <button type="submit" class="btn btn-primary px-4">บันทึกการแก้ไข</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal รีเซ็ตรหัสผ่าน -->
                <div class="modal fade" id="resetPwdModal<?= $user['id']; ?>" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content" style="border-radius: 1rem;">
                            <form action="index.php?action=user_reset_pwd" method="POST">
                                <div class="modal-header bg-warning bg-opacity-10 border-bottom-0" style="border-radius: 1rem 1rem 0 0;">
                                    <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-key me-2 text-warning"></i>รีเซ็ตรหัสผ่าน</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $user['id']; ?>">
                                    <p class="text-muted small">ตั้งรหัสผ่านใหม่ให้กับ: <strong><?= $user['full_name']; ?></strong></p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">รหัสผ่านใหม่ <span class="text-danger">*</span></label>
                                        <input type="text" name="new_password" class="form-control" placeholder="ระบุรหัสผ่านใหม่" required minlength="6">
                                    </div>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">ยกเลิก</button>
                                    <button type="submit" class="btn btn-warning px-4 text-dark fw-bold">เปลี่ยนรหัสผ่าน</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($users)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fa-regular fa-folder-open fs-1 mb-3 opacity-50"></i><br>
                        ยังไม่มีข้อมูลผู้ใช้งานในระบบ
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal เพิ่มผู้ใช้งานใหม่ -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 1rem;">
            <form action="index.php?action=user_store" method="POST">
                <div class="modal-header bg-primary bg-opacity-10 border-bottom-0" style="border-radius: 1rem 1rem 0 0;">
                    <h5 class="modal-title fw-bold text-primary"><i class="fa-solid fa-user-plus me-2"></i>เพิ่มผู้ใช้งานใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" placeholder="ระบุชื่อ-นามสกุลจริง" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Username สำหรับเข้าสู่ระบบ <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control" placeholder="เช่น admin01, somchai_k" required pattern="[a-zA-Z0-9_]+" title="ภาษาอังกฤษ ตัวเลข หรือขีดล่างเท่านั้น">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">รหัสผ่าน (Password) <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ระดับสิทธิ์ (Role) <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="user">ผู้ใช้งานทั่วไป (User)</option>
                            <option value="hr">เจ้าหน้าที่ (HR)</option>
                            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
                        </select>
                    </div>
                    <div class="form-check form-switch mt-4 bg-light p-3 rounded border">
                        <input class="form-check-input ms-0 me-2" type="checkbox" name="is_active" id="activeAdd" checked>
                        <label class="form-check-label fw-semibold" for="activeAdd">อนุญาตให้ผู้ใช้นี้เข้าสู่ระบบได้ทันที</label>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary px-4">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>