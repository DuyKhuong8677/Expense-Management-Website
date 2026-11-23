<?php
/**
 * Ví tiền — TV4. Biến: $wallets (kèm balance), $total
 * Bento Dashboard Style.
 */
use App\Models\Wallet;

$icons = ['cash' => 'money', 'bank' => 'bank', 'ewallet' => 'device-mobile', 'other' => 'wallet'];
?>
<div class="row g-3">
    <div class="col-lg-8">
        <!-- Bento Tile: Tổng số dư -->
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="stat-label mb-0">Tổng số dư tất cả ví</span>
                    <span class="stat-icon-badge"><i class="ph ph-wallet" aria-hidden="true"></i></span>
                </div>
                <div class="money money-display <?= $total < 0 ? 'money-expense' : '' ?>">
                    <?= $total < 0 ? '−' : '' ?><?= money(abs($total)) ?>
                </div>
                <div class="text-muted small mt-2">Tổng giá trị thanh khoản trên mọi tài khoản</div>
            </div>
        </div>

        <!-- Bento Grid: Danh sách ví -->
        <div class="row g-3">
            <?php foreach ($wallets as $w): $b = (int)$w['balance']; ?>
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <span class="feature-icon mb-0"><i class="ph ph-<?= e($icons[$w['type']] ?? 'wallet') ?>" aria-hidden="true"></i></span>
                            <div class="flex-fill min-w-0">
                                <div class="fw-semibold text-truncate"><?= e($w['name']) ?></div>
                                <div class="text-muted small text-truncate"><?= e(Wallet::TYPES[$w['type']] ?? '') ?></div>
                            </div>
                            <div class="money fs-5 fw-semibold text-nowrap <?= $b < 0 ? 'money-expense' : '' ?>">
                                <?= $b < 0 ? '−' : '' ?><?= money(abs($b)) ?>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent py-2">
                            <a class="small fw-semibold d-inline-flex align-items-center gap-1" href="<?= url('giao-dich?vi=' . (int)$w['id']) ?>">
                                <span>Xem giao dịch ví này</span>
                                <i class="ph ph-arrow-right" aria-hidden="true"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card sticky-lg-top" style="top: calc(var(--header-h) + 16px);">
            <div class="card-body">
                <h2 class="h3 mb-1">Thêm ví tiền</h2>
                <p class="text-muted small mb-3">Tạo tài khoản ngân hàng, ví điện tử hoặc tiền mặt để theo dõi số dư riêng biệt.</p>
                <form method="post" action="<?= url('vi') ?>" novalidate>
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="wName">Tên ví</label>
                        <input class="form-control<?= has_error('name') ?>" id="wName" name="name" value="<?= old('name') ?>" maxlength="100" placeholder="VD: Vietcombank, MoMo" required>
                        <?= error_for('name') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="wType">Loại ví</label>
                        <select class="form-select<?= has_error('type') ?>" id="wType" name="type">
                            <?php foreach (Wallet::TYPES as $value => $label): ?>
                                <option value="<?= e($value) ?>"<?= old('type', 'bank') === $value ? ' selected' : '' ?>><?= e($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= error_for('type') ?>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="wInitial">Số dư ban đầu (₫)</label>
                        <input class="form-control js-money<?= has_error('initial_balance') ?>" id="wInitial" name="initial_balance" value="<?= old('initial_balance') ?>" inputmode="numeric" placeholder="0">
                        <?= error_for('initial_balance') ?>
                    </div>
                    <button class="btn btn-primary w-100 py-2" type="submit">Thêm ví</button>
                </form>
            </div>
        </div>
    </div>
</div>
