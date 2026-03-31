<?php include 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold mb-0">จัดการเมนูระบบ</h2>
        <p class="text-muted">ตั้งค่าเมนูที่แสดงในแถบด้านข้าง (Sidebar)</p>
    </div>
    <button class="btn text-white fw-semibold shadow-sm" style="background: linear-gradient(to right, #3b82f6, #2563eb);" data-bs-toggle="modal" data-bs-target="#addMenuModal">
        <i class="fa-solid fa-plus me-1"></i> เพิ่มเมนูใหม่
    </button>
</div>

<div class="modern-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="bg-light">
                <tr>
                    <th class="text-center" width="80">ลำดับ</th>
                    <th>ไอคอน</th>
                    <th>ชื่อเมนู</th>
                    <th>Action (ลิงก์)</th>
                    <th class="text-center">สิทธิ์การเข้าถึง</th>
                    <th class="text-center">สถานะ</th>
                    <th class="text-end pe-3">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($menus as $menu): ?>
                <tr>
                    <td class="text-center fw-bold text-muted"><?= $menu['sort_order']; ?></td>
                    <td>
                        <?php if($menu['parent_id'] > 0): ?>
                            <span class="ms-3 text-muted">|_</span>
                        <?php endif; ?>
                        <i class="fa-solid <?= $menu['icon']; ?> fs-5 text-secondary"></i> 
                        <small class="text-muted ms-1">(<?= $menu['icon']; ?>)</small>
                    </td>
                    <td class="fw-bold">
                        <?php if($menu['parent_id'] > 0): ?>
                            <span class="text-muted fw-normal ms-2"><?= $menu['menu_name']; ?></span>
                        <?php else: ?>
                            <?= $menu['menu_name']; ?>
                        <?php endif; ?>
                    </td>
                    <td><code>?action=<?= $menu['action_name']; ?></code></td>
                    <td class="text-center">
                        <?php if($menu['role_access'] == 'all'): ?>
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25">ทุกคน</span>
                        <?php elseif($menu['role_access'] == 'admin'): ?>
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">ผู้ดูแลระบบ</span>
                        <?php else: ?>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25"><?= ucfirst($menu['role_access']); ?></span>
                        <?php endif; ?>
                    </td>
                    <td class="text-center">
                        <?php if($menu['is_active'] == '1'): ?>
                            <a href="index.php?action=menu_toggle&id=<?= $menu['id']; ?>&status=0" class="badge bg-success text-decoration-none p-2" title="คลิกเพื่อปิดการใช้งาน">เปิดใช้งาน</a>
                        <?php else: ?>
                            <a href="index.php?action=menu_toggle&id=<?= $menu['id']; ?>&status=1" class="badge bg-secondary text-decoration-none p-2" title="คลิกเพื่อเปิดการใช้งาน">ปิดใช้งาน</a>
                        <?php endif; ?>
                    </td>
                    <td class="text-end pe-3">
                        <button class="btn btn-sm btn-light text-primary border me-1" data-bs-toggle="modal" data-bs-target="#editMenuModal<?= $menu['id']; ?>">
                            <i class="fa-solid fa-pen"></i> แก้ไข
                        </button>
                        <a href="index.php?action=menu_delete&id=<?= $menu['id']; ?>" class="btn btn-sm btn-light text-danger border" onclick="return confirm('ยืนยันการลบเมนู <?= $menu['menu_name']; ?>?');">
                            <i class="fa-solid fa-trash"></i>
                        </a>
                    </td>
                </tr>

                <!-- Modal แก้ไขเมนู -->
                <div class="modal fade" id="editMenuModal<?= $menu['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="index.php?action=menu_update" method="POST">
                                <div class="modal-header bg-light">
                                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen me-2 text-primary"></i>แก้ไขเมนู</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?= $menu['id']; ?>">
                                    
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">สังกัดเมนูหลัก</label>
                                            <select name="parent_id" class="form-select">
                                                <option value="0">-- เป็นเมนูหลัก --</option>
                                                <?php foreach($mainMenus as $main): ?>
                                                    <?php if($main['id'] != $menu['id']): // ป้องกันการเลือกตัวเองเป็นแม่ ?>
                                                        <option value="<?= $main['id']; ?>" <?= $menu['parent_id'] == $main['id'] ? 'selected' : ''; ?>>
                                                            <?= $main['menu_name']; ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold">สิทธิ์การมองเห็น</label>
                                            <select name="role_access" class="form-select">
                                                <option value="all" <?= $menu['role_access'] == 'all' ? 'selected' : ''; ?>>ทุกคน (All)</option>
                                                <option value="admin" <?= $menu['role_access'] == 'admin' ? 'selected' : ''; ?>>แอดมิน (Admin)</option>
                                                <option value="user" <?= $menu['role_access'] == 'user' ? 'selected' : ''; ?>>ผู้ใช้งาน (User)</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">ชื่อเมนู</label>
                                        <input type="text" name="menu_name" class="form-control" value="<?= $menu['menu_name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Action (ไม่ต้องใส่ ?action=)</label>
                                        <input type="text" name="action_name" class="form-control" value="<?= $menu['action_name']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">คลาสของ Icon (FontAwesome)</label>
                                        <input type="text" name="icon" class="form-control" value="<?= $menu['icon']; ?>" placeholder="เช่น fa-users, fa-cog" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">ลำดับการแสดงผล</label>
                                        <input type="number" name="sort_order" class="form-control" value="<?= $menu['sort_order']; ?>">
                                    </div>
                                    <div class="form-check form-switch mt-3">
                                        <input class="form-check-input" type="checkbox" name="is_active" id="activeEdit<?= $menu['id']; ?>" <?= $menu['is_active'] == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="activeEdit<?= $menu['id']; ?>">เปิดใช้งานเมนูนี้</label>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                                    <button type="submit" class="btn btn-primary">บันทึกการแก้ไข</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if(empty($menus)): ?>
                <tr>
                    <td colspan="7" class="text-center py-4 text-muted">ยังไม่มีข้อมูลเมนูในระบบ</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal เพิ่มเมนูใหม่ -->
<div class="modal fade" id="addMenuModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="index.php?action=menu_store" method="POST">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus me-2 text-primary"></i>เพิ่มเมนูใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">สังกัดเมนูหลัก</label>
                            <select name="parent_id" class="form-select">
                                <option value="0">-- เป็นเมนูหลัก --</option>
                                <?php foreach($mainMenus as $main): ?>
                                    <option value="<?= $main['id']; ?>"><?= $main['menu_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">สิทธิ์การมองเห็น</label>
                            <select name="role_access" class="form-select">
                                <option value="all">ทุกคน (All)</option>
                                <option value="admin">แอดมิน (Admin)</option>
                                <option value="user">ผู้ใช้งาน (User)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">ชื่อเมนู</label>
                        <input type="text" name="menu_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Action (ไม่ต้องใส่ ?action=)</label>
                        <input type="text" name="action_name" class="form-control" placeholder="เช่น employees, settings" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">คลาสของ Icon (FontAwesome)</label>
                        <input type="text" name="icon" class="form-control" placeholder="เช่น fa-users, fa-cog" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ลำดับการแสดงผล (ตัวเลข)</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="activeAdd" checked>
                        <label class="form-check-label" for="activeAdd">เปิดใช้งานเมนูนี้</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึกข้อมูล</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'views/layout/footer.php'; ?>