<?php
/**
 * Ví tiền — TV4. Biến: $wallets (kèm balance), $total
 * Thêm: form bên phải. Sửa: hộp thoại (JS điền sẵn dữ liệu từ data-*, xem public/assets/js/wallets.js).
 */
use App\Models\Wallet;

$icons = ['cash' => 'money', 'bank' => 'bank', 'ewallet' => 'device-mobile', 'other' => 'wallet'];

// Form nào vừa nhập sai ('add' hoặc 'edit') thì chỉ form đó mới hiện lại dữ liệu cũ và lỗi
$oldForm = $_SESSION['old']['_form'] ?? '';
$addOld = fn ($k, $d = '') => $oldForm === 'add' ? old($k, $d) : e($d);
$addErr = fn ($k) => $oldForm === 'add' ? error_for($k) : '';
$addInvalid = fn ($k) => $oldForm === 'add' ? has_error($k) : '';
$editErr = fn ($k) => $oldForm === 'edit' ? error_for($k) : '';
$editInvalid = fn ($k) => $oldForm === 'edit' ? has_error($k) : '';
?>
<div class="row g-3">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <div class="text-muted">Tổng số dư tất cả ví</div>
                <div class="money money-display <?= $total < 0 ? 'money-expense' : '' ?>"><?= $total < 0 ? '−' : '' ?><?= money(abs($total)) ?></div>
            </div>
        </div>

        <div class="row g-3">
            <?php foreach ($wallets as $w): $b = (int)$w['balance']; ?>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <span class="feature-icon mb-0"><i class="ph ph-<?= e($icons[$w['type']] ?? 'wallet') ?>" aria-hidden="true"></i></span>
                            <div class="flex-fill">
                                <div class="fw-semibold"><?= e($w['name']) ?></div>
                                <div class="text-muted"><?= e(Wallet::TYPES[$w['type']] ?? '') ?></div>
                            </div>
                            <div class="money fs-5 fw-semibold <?= $b < 0 ? 'money-expense' : '' ?>"><?= $b < 0 ? '−' : '' ?><?= money(abs($b)) ?></div>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between align-items-center">
                            <a href="<?= url('giao-dich?vi=' . (int)$w['id']) ?>">Xem giao dịch của ví này</a>
                            <button class="btn btn-sm btn-outline-secondary js-edit-wallet" type="button"
                                    data-id="<?= (int)$w['id'] ?>" data-name="<?= e($w['name']) ?>" data-type="<?= e($w['type']) ?>"
                                    data-initial="<?= (int)$w['initial_balance'] ?>" aria-label="Sửa ví <?= e($w['name']) ?>">
                                <i class="ph ph-pencil-simple" aria-hidden="true"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h2 class="h3">Thêm ví</h2>
                <form method="post" action="<?= url('vi') ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="wName">Tên ví</label>
                        <input class="form-control<?= $addInvalid('name') ?>" id="wName" name="name" value="<?= $addOld('name') ?>" maxlength="100" placeholder="VD: Vietcombank" required>
                        <?= $addErr('name') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="wType">Loại ví</label>
                        <select class="form-select<?= $addInvalid('type') ?>" id="wType" name="type">
                            <?php foreach (Wallet::TYPES as $value => $label): ?>
                                <option value="<?= e($value) ?>"<?= $addOld('type', 'bank') === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= $addErr('type') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="wInitial">Số dư ban đầu (₫)</label>
                        <input class="form-control<?= $addInvalid('initial_balance') ?>" id="wInitial" name="initial_balance" value="<?= $addOld('initial_balance') ?>" inputmode="numeric" placeholder="0">
                        <?= $addErr('initial_balance') ?>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Thêm ví</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Hộp thoại sửa ví -->
<div class="modal fade" id="walletModal" tabindex="-1" aria-labelledby="walletModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="post" id="walletForm" data-base="<?= e(url('vi')) ?>" novalidate>
            <?= csrf_field() ?>
            <div class="modal-header">
                <h2 class="modal-title h3" id="walletModalTitle">Sửa ví</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="wEditName">Tên ví</label>
                    <input class="form-control<?= $editInvalid('name') ?>" id="wEditName" name="name" maxlength="100" required>
                    <?= $editErr('name') ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="wEditType">Loại ví</label>
                    <select class="form-select<?= $editInvalid('type') ?>" id="wEditType" name="type">
                        <?php foreach (Wallet::TYPES as $value => $label): ?>
                            <option value="<?= e($value) ?>"><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?= $editErr('type') ?>
                </div>
                <div>
                    <label class="form-label" for="wEditInitial">Số dư ban đầu (₫)</label>
                    <input class="form-control<?= $editInvalid('initial_balance') ?>" id="wEditInitial" name="initial_balance" inputmode="numeric" placeholder="0">
                    <?= $editErr('initial_balance') ?>
                    <div class="form-text">Số dư hiện tại = số dư ban đầu + thu − chi ± chuyển tiền, nên đổi số này sẽ làm số dư hiện tại thay đổi theo.</div>
                </div>
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
    <script type="application/json" id="walletEditOld"><?= json_encode([
        'id' => (int)($_SESSION['old']['_id'] ?? 0), 'name' => $_SESSION['old']['name'] ?? '',
        'type' => $_SESSION['old']['type'] ?? 'cash', 'initial' => $_SESSION['old']['initial_balance'] ?? '',
    ], JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<?php endif; ?>
