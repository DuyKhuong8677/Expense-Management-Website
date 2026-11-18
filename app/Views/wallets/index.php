<?php
/**
 * Ví tiền — TV4. Biến: $wallets (kèm balance), $total
 */
use App\Models\Wallet;

$icons = ['cash' => 'money', 'bank' => 'bank', 'ewallet' => 'device-mobile', 'other' => 'wallet'];
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
                        <div class="card-footer bg-transparent">
                            <a href="<?= url('giao-dich?vi=' . (int)$w['id']) ?>">Xem giao dịch của ví này</a>
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
                        <input class="form-control<?= has_error('name') ?>" id="wName" name="name" value="<?= old('name') ?>" maxlength="100" placeholder="VD: Vietcombank" required>
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
                        <input class="form-control<?= has_error('initial_balance') ?>" id="wInitial" name="initial_balance" value="<?= old('initial_balance') ?>" inputmode="numeric" placeholder="0">
                        <?= error_for('initial_balance') ?>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Thêm ví</button>
                </form>
            </div>
        </div>
    </div>
</div>
