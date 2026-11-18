<?php
/**
 * Danh mục — TV4. Biến: $groups (expense, income), $icons
 * Thêm: form bên phải. Sửa: hộp thoại (JS điền sẵn dữ liệu từ data-*). Xóa: form POST có xác nhận.
 */
$titles = ['expense' => 'Danh mục chi', 'income' => 'Danh mục thu'];
$oldForm = $_SESSION['old']['_form'] ?? '';
$addOld = fn ($k, $d = '') => $oldForm === 'add' ? old($k, $d) : e($d);
$addErr = fn ($k) => $oldForm === 'add' ? error_for($k) : '';
$addInvalid = fn ($k) => $oldForm === 'add' ? has_error($k) : '';
$iconPicker = function (string $name, string $selected, string $idPrefix) use ($icons) {
    $html = '<div class="icon-picker" role="radiogroup" aria-label="Biểu tượng">';
    foreach ($icons as $icon) {
        $id = $idPrefix . $icon;
        $html .= '<input type="radio" class="btn-check" name="' . e($name) . '" id="' . e($id) . '" value="' . e($icon) . '"'
            . ($icon === $selected ? ' checked' : '') . '>'
            . '<label class="icon-option" for="' . e($id) . '" title="' . e($icon) . '"><i class="ph ph-' . e($icon) . '" aria-hidden="true"></i><span class="visually-hidden">' . e($icon) . '</span></label>';
    }
    return $html . '</div>';
};
?>
<div class="row g-3">
    <div class="col-lg-8 d-grid gap-3 align-content-start">
        <?php foreach ($groups as $type => $list): ?>
            <div class="card">
                <div class="card-body">
                    <h2 class="h3 mb-3"><?= e($titles[$type]) ?> <span class="text-muted fw-normal">(<?= count($list) ?>)</span></h2>
                    <div class="cat-grid">
                        <?php foreach ($list as $c): $own = $c['user_id'] !== null; ?>
                            <div class="cat-card">
                                <span class="cat-icon"><i class="ph ph-<?= e($c['icon']) ?>" aria-hidden="true"></i></span>
                                <div class="flex-fill min-w-0">
                                    <a class="fw-semibold text-reset text-decoration-none d-block text-truncate" href="<?= url('giao-dich?danh_muc=' . (int)$c['id']) ?>"><?= e($c['name']) ?></a>
                                    <div class="text-muted small-14"><?= $own ? 'Của bạn' : 'Mặc định' ?> · <?= (int)$c['used'] ?> giao dịch</div>
                                </div>
                                <?php if ($own): ?>
                                    <button class="btn btn-sm btn-outline-secondary js-edit-cat" type="button"
                                            data-id="<?= (int)$c['id'] ?>" data-name="<?= e($c['name']) ?>" data-type="<?= e($c['type']) ?>"
                                            data-icon="<?= e($c['icon']) ?>" data-used="<?= (int)$c['used'] ?>" aria-label="Sửa danh mục <?= e($c['name']) ?>">
                                        <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                                    </button>
                                    <form method="post" action="<?= url('danh-muc/' . (int)$c['id'] . '/xoa') ?>"
                                          data-confirm="<?= (int)$c['used'] > 0 ? 'Danh mục đã có giao dịch nên sẽ được ẩn đi, giao dịch cũ vẫn giữ nguyên. Tiếp tục?' : 'Xóa danh mục ' . e($c['name']) . '?' ?>">
                                        <?= csrf_field() ?>
                                        <button class="btn btn-sm btn-outline-secondary" type="submit" aria-label="Xóa danh mục <?= e($c['name']) ?>"><i class="ph ph-trash" aria-hidden="true"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h3">Thêm danh mục</h2>
                <p class="text-muted">Danh mục mặc định dùng chung cho mọi người. Bạn có thể tạo thêm danh mục riêng.</p>
                <form method="post" action="<?= url('danh-muc') ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="btn-group w-100 mb-3" role="group" aria-label="Loại danh mục">
                        <?php $t = $oldForm === 'add' ? ($_SESSION['old']['type'] ?? 'expense') : 'expense'; ?>
                        <input type="radio" class="btn-check" name="type" id="addTypeExpense" value="expense"<?= $t === 'expense' ? ' checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="addTypeExpense">Chi</label>
                        <input type="radio" class="btn-check" name="type" id="addTypeIncome" value="income"<?= $t === 'income' ? ' checked' : '' ?>>
                        <label class="btn btn-outline-primary" for="addTypeIncome">Thu</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="addName">Tên danh mục</label>
                        <input class="form-control<?= $addInvalid('name') ?>" id="addName" name="name" value="<?= $addOld('name') ?>" maxlength="50" placeholder="VD: Cà phê, Nuôi mèo" required>
                        <?= $addErr('name') ?>
                    </div>
                    <div class="mb-3">
                        <span class="form-label d-block">Biểu tượng</span>
                        <?= $iconPicker('icon', $oldForm === 'add' ? ($_SESSION['old']['icon'] ?? 'tag') : 'tag', 'addIcon-') ?>
                        <?= $addErr('icon') ?>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Thêm danh mục</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hộp thoại sửa danh mục -->
<div class="modal fade" id="catModal" tabindex="-1" aria-labelledby="catModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" id="catForm" data-base="<?= e(url('danh-muc')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="modal-header">
                <h2 class="modal-title h3" id="catModalTitle">Sửa danh mục</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="btn-group w-100 mb-2" role="group" aria-label="Loại danh mục">
                    <input type="radio" class="btn-check" name="type" id="editTypeExpense" value="expense">
                    <label class="btn btn-outline-primary" for="editTypeExpense">Chi</label>
                    <input type="radio" class="btn-check" name="type" id="editTypeIncome" value="income">
                    <label class="btn btn-outline-primary" for="editTypeIncome">Thu</label>
                </div>
                <p class="text-muted mb-3 d-none" id="editTypeLocked">Danh mục đã có giao dịch nên không đổi được loại.</p>
                <div class="mb-3">
                    <label class="form-label" for="editName">Tên danh mục</label>
                    <input class="form-control<?= $oldForm === 'edit' ? has_error('name') : '' ?>" id="editName" name="name" maxlength="50" required>
                    <?= $oldForm === 'edit' ? error_for('name') : '' ?>
                </div>
                <span class="form-label d-block">Biểu tượng</span>
                <?= $iconPicker('icon', '', 'editIcon-') ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
<?php if ($oldForm === 'edit'): ?>
    <!-- Sửa bị lỗi: mở lại hộp thoại với dữ liệu vừa nhập -->
    <script type="application/json" id="catEditOld"><?= json_encode([
        'id' => (int)($_SESSION['old']['_id'] ?? 0), 'name' => $_SESSION['old']['name'] ?? '',
        'type' => $_SESSION['old']['type'] ?? 'expense', 'icon' => $_SESSION['old']['icon'] ?? 'tag', 'used' => 0,
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endif; ?>
